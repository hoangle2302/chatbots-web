// script-backend.js - Frontend sử dụng Backend PHP
// Thay thế script.js để sử dụng backend thay vì gọi API trực tiếp

// Kiểm tra trạng thái đăng nhập
let currentUser = null;

function checkAuthStatus() {
    const userData = localStorage.getItem('user');
    if (userData) {
        currentUser = JSON.parse(userData);
        showUserSection();
    } else {
        showAuthSection();
    }
}

// Hiển thị section user
function showUserSection() {
    document.getElementById('user-section').style.display = 'block';
    document.getElementById('auth-section').style.display = 'none';
}

// Hiển thị section auth
function showAuthSection() {
    document.getElementById('user-section').style.display = 'none';
    document.getElementById('auth-section').style.display = 'block';
}

// Thêm bubble chat
async function addBubble(sender, content, model = null) {
    const chatArea = document.getElementById('chat-area');
    const bubble = document.createElement('div');
    bubble.className = `bubble ${sender}`;
    
    let header = '';
    if (sender === 'ai' && model) {
        header = `<div class="bubble-header">🤖 ${model}</div>`;
    }
    
    bubble.innerHTML = `
        ${header}
        <div class="bubble-content">${content}</div>
    `;
    
    chatArea.appendChild(bubble);
    chatArea.scrollTop = chatArea.scrollHeight;
}

// Set loading state
function setLoading(loading) {
    const loadingEl = document.getElementById('loading');
    const sendBtn = document.getElementById('send-btn');
    
    if (loading) {
        loadingEl.style.display = 'block';
        sendBtn.disabled = true;
        sendBtn.textContent = 'Đang xử lý...';
    } else {
        loadingEl.style.display = 'none';
        sendBtn.disabled = false;
        sendBtn.textContent = 'Gửi';
    }
}

// Gọi backend API với fallback system
async function callBackendAPI(message, model, mode = 'single') {
    const API_ENDPOINTS = [
        'http://127.0.0.1:8000/api/chat-real.php',
        'http://127.0.0.1:8000/api/chat-simple.php'
    ];
    
    const requestData = {
        message: message,
        model: model,
        mode: mode
    };
    
    for (let i = 0; i < API_ENDPOINTS.length; i++) {
        const apiUrl = API_ENDPOINTS[i];
        
        try {
            console.log(`🔄 Trying API: ${apiUrl}`);
            
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestData),
                signal: AbortSignal.timeout(10000) // 10 seconds timeout
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error || 'API request failed');
            }
            
            console.log(`✅ API Success: ${apiUrl}`);
            if (data.data && data.data.ai_source) {
                console.log(`🤖 AI Source: ${data.data.ai_source}`);
            }
            
            return data.data;
            
        } catch (error) {
            console.warn(`❌ API Failed: ${apiUrl} - ${error.message}`);
            
            // If this is the last API, throw the error
            if (i === API_ENDPOINTS.length - 1) {
                throw new Error(`All APIs failed. Last error: ${error.message}`);
            }
            
            // Wait before trying next API
            await new Promise(resolve => setTimeout(resolve, 1000));
        }
    }
}

// Xử lý chat single
async function processSingleChat(message, model) {
    try {
        const data = await callBackendAPI(message, model, 'single');
        
        await addBubble('ai', data.content, data.model);
        
        return {
            success: true,
            content: data.content,
            model: data.model,
            tokens: data.tokens_used || 0
        };
        
    } catch (error) {
        await addBubble('ai', `❌ Lỗi: ${error.message}`);
        return { success: false, error: error.message };
    }
}

// Xử lý chat ensemble
async function processEnsembleChat(message) {
    try {
        const data = await callBackendAPI(message, null, 'ensemble');
        
        if (data.mode === 'ensemble' && data.responses) {
            // Hiển thị từng response
            for (const resp of data.responses) {
                await addBubble('ai', resp.content, resp.model);
            }
        } else {
            await addBubble('ai', data.content, 'Ensemble');
        }
        
        return {
            success: true,
            content: data.content,
            mode: data.mode,
            responses: data.responses || []
        };
        
    } catch (error) {
        await addBubble('ai', `❌ Lỗi: ${error.message}`);
        return { success: false, error: error.message };
    }
}

// Xử lý chat distributed
async function processDistributedChat(message) {
    try {
        const data = await callBackendAPI(message, null, 'distributed');
        
        if (data.mode === 'distributed' && data.tasks) {
            // Hiển thị từng task
            for (const task of data.tasks) {
                await addBubble('ai', task.content, `${task.task} (${task.model})`);
            }
        } else {
            await addBubble('ai', data.content, 'Distributed');
        }
        
        return {
            success: true,
            content: data.content,
            mode: data.mode,
            tasks: data.tasks || []
        };
        
    } catch (error) {
        await addBubble('ai', `❌ Lỗi: ${error.message}`);
        return { success: false, error: error.message };
    }
}

// Xử lý gửi tin nhắn
async function sendMessage() {
    const input = document.getElementById('chat-input');
    const message = input.value.trim();
    
    if (!message) return;
    
    // Thêm tin nhắn user
    await addBubble('user', message);
    input.value = '';
    
    setLoading(true);
    
    try {
        // Kiểm tra kết nối backend
        const healthResponse = await fetch('http://127.0.0.1:8000/api/health.php');
        if (!healthResponse.ok) {
            throw new Error('Backend không khả dụng');
        }
        
        // Lấy cài đặt
        const modelSelect = document.getElementById('model-select');
        const selectedModel = modelSelect.value;
        const processingMode = document.querySelector('input[name="processing-mode"]:checked').value;
        
        let result;
        
        if (processingMode === 'ensemble') {
            await addBubble('ai', '🤖 Đang hỏi 4 AI hàng đầu...');
            result = await processEnsembleChat(message);
        } else if (processingMode === 'distributed') {
            await addBubble('ai', '🚀 Đang phân công 28 AI...');
            result = await processDistributedChat(message);
        } else {
            await addBubble('ai', `🤖 Đang hỏi ${selectedModel}...`);
            result = await processSingleChat(message, selectedModel);
        }
        
        if (result.success) {
            console.log('✅ Chat thành công:', result);
        } else {
            console.error('❌ Chat thất bại:', result.error);
        }
        
    } catch (error) {
        await addBubble('ai', `❌ Lỗi: ${error.message}`);
        console.error('Chat Error:', error);
    } finally {
        setLoading(false);
    }
}

// Xử lý chat đơn lẻ
async function processSingleChat(message, model) {
    try {
        const result = await callBackendAPI(message, model, 'single');
        
        if (result && result.content) {
            await addBubble('ai', result.content, model);
            return { success: true, data: result };
        } else {
            throw new Error('Không nhận được phản hồi từ AI');
        }
    } catch (error) {
        console.error('Single Chat Error:', error);
        await addBubble('ai', `❌ Lỗi: ${error.message}`);
        return { success: false, error: error.message };
    }
}

// Xử lý chat ensemble
async function processEnsembleChat(message) {
    try {
        const result = await callBackendAPI(message, 'ensemble', 'ensemble');
        
        if (result && result.content) {
            await addBubble('ai', result.content, 'ensemble');
            return { success: true, data: result };
        } else {
            throw new Error('Không nhận được phản hồi từ AI');
        }
    } catch (error) {
        console.error('Ensemble Chat Error:', error);
        await addBubble('ai', `❌ Lỗi: ${error.message}`);
        return { success: false, error: error.message };
    }
}

// Xử lý chat distributed
async function processDistributedChat(message) {
    try {
        const result = await callBackendAPI(message, 'distributed', 'distributed');
        
        if (result && result.content) {
            await addBubble('ai', result.content, 'distributed');
            return { success: true, data: result };
        } else {
            throw new Error('Không nhận được phản hồi từ AI');
        }
    } catch (error) {
        console.error('Distributed Chat Error:', error);
        await addBubble('ai', `❌ Lỗi: ${error.message}`);
        return { success: false, error: error.message };
    }
}

// Upload tài liệu
async function uploadDocument(file) {
    const formData = new FormData();
    formData.append('file', file);
    
    try {
        const response = await fetch('http://127.0.0.1:8000/api/upload', {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'Upload failed');
        }
        
        return data.data;
        
    } catch (error) {
        console.error('Upload Error:', error);
        throw error;
    }
}

// Xử lý upload file
function handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    const fileInfo = document.getElementById('document-info');
    const docName = document.getElementById('doc-name');
    const removeBtn = document.getElementById('remove-doc');
    
    // Hiển thị thông tin file
    docName.textContent = file.name;
    fileInfo.style.display = 'block';
    
    // Upload file
    uploadDocument(file)
        .then(data => {
            console.log('✅ Upload thành công:', data);
            // Lưu thông tin file để sử dụng trong chat
            window.currentDocument = data;
        })
        .catch(error => {
            console.error('❌ Upload thất bại:', error);
            alert(`Lỗi upload: ${error.message}`);
        });
}

// Xóa tài liệu
function removeDocument() {
    const fileInfo = document.getElementById('document-info');
    const fileInput = document.getElementById('document-upload');
    
    fileInfo.style.display = 'none';
    fileInput.value = '';
    window.currentDocument = null;
}

// Khởi tạo
document.addEventListener('DOMContentLoaded', function() {
    // Kiểm tra auth
    checkAuthStatus();
    
    // Xử lý form chat
    const chatForm = document.getElementById('chat-form');
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });
    
    // Xử lý upload
    const uploadBtn = document.getElementById('upload-btn');
    const fileInput = document.getElementById('document-upload');
    
    uploadBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', handleFileUpload);
    
    // Xử lý xóa tài liệu
    const removeBtn = document.getElementById('remove-doc');
    removeBtn.addEventListener('click', removeDocument);
    
    // Xử lý quản lý tài liệu
    const manageDocsBtn = document.getElementById('manage-docs-btn');
    manageDocsBtn.addEventListener('click', () => {
        window.open('document-manager.html', '_blank');
    });
    
    console.log('✅ Thư Viện AI Frontend đã khởi tạo với Backend PHP');
});
