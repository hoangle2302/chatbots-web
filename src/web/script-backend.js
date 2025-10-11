// script-backend.js - Frontend sử dụng Backend PHP
// Thay thế script.js để sử dụng backend thay vì gọi API trực tiếp

// Kiểm tra trạng thái đăng nhập
let currentUser = null;

// Hàm format ensemble response
function formatEnsembleResponse(content) {
    // Tách các phần response
    const parts = content.split(/\*\*✅|\*\*❌/);
    let formatted = '<div class="ensemble-container">';
    
    // Header - check if it's QWEN or ENSEMBLE
    if (content.includes('**QWEN AI RESPONSE**')) {
        formatted += '<div class="ensemble-header">🤖 <strong>QWEN AI RESPONSE</strong></div>';
    } else {
        formatted += '<div class="ensemble-header">🤖 <strong>ENSEMBLE AI RESPONSE</strong></div>';
    }
    
    // Xử lý từng response
    for (let i = 1; i < parts.length; i++) {
        const part = parts[i].trim();
        if (!part) continue;
        
        const lines = part.split('\n');
        const header = lines[0];
        const responseContent = lines.slice(1).join('\n').trim();
        
        if (header && responseContent) {
            const isSuccess = header.includes('✅');
            const provider = header.replace(/[✅❌]/g, '').trim();
            
            formatted += `<div class="ensemble-item ${isSuccess ? 'success' : 'error'}">`;
            formatted += `<div class="ensemble-provider">${isSuccess ? '✅' : '❌'} ${provider}</div>`;
            formatted += `<div class="ensemble-content">${responseContent}</div>`;
            formatted += '</div>';
        }
    }
    
    // Footer
    const footerMatch = content.match(/(⚠️|ℹ️|✨).*$/);
    if (footerMatch) {
        formatted += `<div class="ensemble-footer">${footerMatch[0]}</div>`;
    }
    
    formatted += '</div>';
    return formatted;
}

// Hàm thông minh để phân loại model theo provider
function getModelProvider(modelValue) {
    const modelLower = modelValue.toLowerCase();
    
    // Doubao models (check first before Chinese models)
    if (modelLower.includes('doubao')) {
        return 'doubao';
    }
    
    // DeepSeek models (check before Chinese models)
    if (modelLower.includes('deepseek')) {
        return 'deepseek';
    }
    
    // OpenAI Plus models (premium models)
    if ((modelLower.includes('gpt-4') || modelLower.includes('o1') || modelLower.includes('o3') || 
         modelLower.includes('o4')) && !modelLower.includes('doubao')) {
        return 'openai-plus';
    }
    
    // OpenAI models (regular models)
    if (modelLower.includes('gpt') || modelLower.includes('dall-e') || modelLower.includes('whisper') || 
        modelLower.includes('tts') || modelLower.includes('babbage') || modelLower.includes('davinci')) {
        return 'openai';
    }
    
    // Claude models
    if (modelLower.includes('claude')) {
        return 'claude';
    }
    
    // Google models
    if (modelLower.includes('gemini') || modelLower.includes('google/imagen')) {
        return 'google';
    }
    
    // Chinese models (without doubao and deepseek)
    if (modelLower.includes('qwen') || modelLower.includes('qwq') || modelLower.includes('yi-') ||
        modelLower.includes('glm') || modelLower.includes('hunyuan') || modelLower.includes('kimi') ||
        modelLower.includes('ernie') || modelLower.includes('sparkdesk') || modelLower.includes('baai')) {
        return 'chinese';
    }
    
    // Image generation models
    if (modelLower.includes('mj_') || modelLower.includes('stable-diffusion') || 
        modelLower.includes('flux') || modelLower.includes('swap_face') || 
        modelLower.includes('/remove-bg') || modelLower.includes('ideogram') ||
        modelLower.includes('imagen')) {
        return 'image';
    }
    
    // Video models
    if (modelLower.includes('kling') || modelLower.includes('luma_video') || 
        modelLower.includes('minimax/video') || modelLower.includes('jimeng-videos') ||
        modelLower.includes('animate-diff')) {
        return 'video';
    }
    
    // SiliconFlow models (có thể cần pattern khác)
    if (modelLower.includes('siliconflow')) {
        return 'siliconflow';
    }
    
    // Default: other
    return 'other';
}

// Render message từ history (không lưu vào history)
function renderMessage(sender, content, model = null) {
    const chatArea = document.getElementById('chat-area');
    const message = document.createElement('div');
    message.className = `message ${sender === 'bot' ? 'bot' : 'user'}`;
    
    // Tạo avatar
    const avatar = document.createElement('div');
    avatar.className = 'message-avatar';
    avatar.textContent = sender === 'bot' ? '🤖' : '👤';
    
    // Tạo nội dung
    const messageContent = document.createElement('div');
    messageContent.className = 'message-content';
    
    // Thêm model info nếu là bot và có model
    if (sender === 'bot' && model) {
        const modelInfo = document.createElement('div');
        modelInfo.className = 'model-info';
        
        modelInfo.innerHTML = `<span class="model-icon">🤖</span> <span class="model-name">${model}</span>`;
        messageContent.appendChild(modelInfo);
    }
    
    // Thêm nội dung chính
    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-text';
    contentDiv.innerHTML = content;
    messageContent.appendChild(contentDiv);
    
    // Thêm timestamp (sử dụng timestamp từ history hoặc hiện tại)
    const time = document.createElement('div');
    time.className = 'message-time';
    time.textContent = new Date().toLocaleTimeString('vi-VN', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
    messageContent.appendChild(time);
    
    // Ghép các phần lại
    message.appendChild(avatar);
    message.appendChild(messageContent);
    
    chatArea.appendChild(message);
    chatArea.scrollTop = chatArea.scrollHeight;
}

// Hàm lấy tên hiển thị của model từ model ID
function getModelDisplayName(modelId) {
    if (!modelId) return 'AI';
    
    // Nếu đã là tên hiển thị (ensemble, distributed, etc.)
    if (modelId.toLowerCase().includes('ensemble') || 
        modelId.toLowerCase().includes('distributed') ||
        modelId.toLowerCase().includes('thinking') ||
        modelId.includes('(') && modelId.includes(')')) {
        return modelId;
    }
    
    // Xử lý đặc biệt cho ensemble
    if (modelId === 'ensemble') {
        return '🤖 Tất cả AI (Ensemble)';
    }
    
    // Tìm trong select options
    const modelSelect = document.getElementById('model-select');
    if (modelSelect) {
        const option = modelSelect.querySelector(`option[value="${modelId}"]`);
        if (option) {
            return option.textContent.trim();
        }
    }
    
    // Fallback: format model ID để dễ đọc hơn
    return modelId
        .replace(/-/g, ' ')
        .replace(/_/g, ' ')
        .replace(/\b\w/g, l => l.toUpperCase());
}

function checkAuthStatus() {
    const userData = localStorage.getItem('user');
    // Kiểm tra userData hợp lệ (không phải null, undefined, hoặc "undefined")
    if (userData && userData !== 'null' && userData !== 'undefined' && userData.trim() !== '') {
        try {
            currentUser = JSON.parse(userData);
            showUserSection();
        } catch (error) {
            console.error('Error parsing user data in checkAuthStatus:', error);
            showAuthSection();
        }
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

// Thêm message chat với cấu trúc mới
async function addBubble(sender, content, model = null) {
    const chatArea = document.getElementById('chat-area');
    const message = document.createElement('div');
    message.className = `message ${sender === 'ai' ? 'bot' : 'user'}`;
    
    // Tạo avatar
    const avatar = document.createElement('div');
    avatar.className = 'message-avatar';
    avatar.textContent = sender === 'ai' ? '🤖' : '👤';
    
    // Tạo nội dung
    const messageContent = document.createElement('div');
    messageContent.className = 'message-content';
    
    // Thêm model info nếu là AI và có model
    if (sender === 'ai' && model) {
        const modelInfo = document.createElement('div');
        modelInfo.className = 'model-info';
        
        // Sử dụng getModelDisplayName để lấy tên đẹp
        const displayName = getModelDisplayName(model);
        modelInfo.innerHTML = `<span class="model-icon">🤖</span> <span class="model-name">${displayName}</span>`;
        messageContent.appendChild(modelInfo);
    }
    
    // Thêm nội dung chính
    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-text';
    
    // Xử lý đặc biệt cho ensemble response
    if (model === 'ensemble' && (content.includes('**ENSEMBLE AI RESPONSE**') || content.includes('**QWEN AI RESPONSE**'))) {
        contentDiv.innerHTML = formatEnsembleResponse(content);
        contentDiv.classList.add('ensemble-response');
    } else {
        contentDiv.innerHTML = content;
    }
    
    messageContent.appendChild(contentDiv);
    
    // Thêm timestamp
    const time = document.createElement('div');
    time.className = 'message-time';
    time.textContent = new Date().toLocaleTimeString('vi-VN', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
    messageContent.appendChild(time);
    
    // Ẩn welcome screen nếu đây là message đầu tiên
    const welcomeScreen = document.getElementById('welcome-screen');
    if (welcomeScreen) {
        welcomeScreen.style.display = 'none';
        chatArea.classList.add('has-messages');
    }
    
    // Ghép các phần lại
    message.appendChild(avatar);
    message.appendChild(messageContent);
    
    chatArea.appendChild(message);
    chatArea.scrollTop = chatArea.scrollHeight;
    
    // Lưu message vào chat history (không lưu các message hệ thống như "Đang xử lý...")
    if (!content.includes('Đang') && !content.includes('...') && content.trim() !== '') {
        const messageType = sender === 'ai' ? 'bot' : 'user';
        const modelName = sender === 'ai' && model ? getModelDisplayName(model) : '';
        saveMessageToHistory(messageType, content, modelName);
    }
}

// Hiển thị typing indicator
function showTypingIndicator() {
    const chatArea = document.getElementById('chat-area');
    
    // Xóa typing indicator cũ nếu có
    const existingTyping = chatArea.querySelector('.typing-indicator');
    if (existingTyping) {
        existingTyping.remove();
    }
    
    const typingDiv = document.createElement('div');
    typingDiv.className = 'typing-indicator show';
    typingDiv.innerHTML = `
        <div class="message-avatar">🤖</div>
        <div class="typing-dots">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        </div>
    `;
    
    chatArea.appendChild(typingDiv);
    chatArea.scrollTop = chatArea.scrollHeight;
}

// Ẩn typing indicator
function hideTypingIndicator() {
    const chatArea = document.getElementById('chat-area');
    const typingIndicator = chatArea.querySelector('.typing-indicator');
    if (typingIndicator) {
        typingIndicator.remove();
    }
}

// Set loading state
function setLoading(loading) {
    const loadingEl = document.getElementById('loading');
    const sendBtn = document.getElementById('send-btn');
    
    if (loading) {
        loadingEl.style.display = 'block';
        sendBtn.disabled = true;
        sendBtn.textContent = 'Đang xử lý...';
        showTypingIndicator();
    } else {
        loadingEl.style.display = 'none';
        sendBtn.disabled = false;
        sendBtn.textContent = 'Gửi';
        hideTypingIndicator();
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
        await addBubble('ai', `❌ Lỗi: ${error.message}`, model || 'AI');
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
        await addBubble('ai', `❌ Lỗi: ${error.message}`, 'Ensemble AI');
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
        await addBubble('ai', `❌ Lỗi: ${error.message}`, 'Distributed AI');
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
        const healthResponse = await fetch('http://127.0.0.1:8000/api/health');
        if (!healthResponse.ok) {
            throw new Error('Backend không khả dụng');
        }
        
        // Lấy cài đặt
        const modelSelect = document.getElementById('model-select');
        if (!modelSelect) {
            throw new Error('Model select element not found');
        }
        const selectedModel = modelSelect.value;
        
        const processingModeElement = document.querySelector('input[name="processing-mode"]:checked');
        const processingMode = processingModeElement ? processingModeElement.value : 'single';
        
        let result;
        
        if (processingMode === 'ensemble') {
            await addBubble('ai', '🤖 Đang hỏi 4 AI hàng đầu...', 'Ensemble AI');
            result = await processEnsembleChat(message);
        } else if (processingMode === 'distributed') {
            await addBubble('ai', '🚀 Đang phân công 28 AI...', 'Distributed AI');
            result = await processDistributedChat(message);
        } else {
            await addBubble('ai', `🤖 Đang hỏi ${getModelDisplayName(selectedModel)}...`, selectedModel);
            result = await processSingleChat(message, selectedModel);
        }
        
        if (result.success) {
            console.log('✅ Chat thành công:', result);
        } else {
            console.error('❌ Chat thất bại:', result.error);
        }
        
    } catch (error) {
        await addBubble('ai', `❌ Lỗi: ${error.message}`, 'System');
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
        await addBubble('ai', `❌ Lỗi: ${error.message}`, model || 'AI');
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
        await addBubble('ai', `❌ Lỗi: ${error.message}`, 'Ensemble AI');
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
        await addBubble('ai', `❌ Lỗi: ${error.message}`, 'Distributed AI');
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

// Hàm lọc models theo provider
function filterModelsByProvider(providerValue) {
    // Sử dụng hàm tích hợp để áp dụng tất cả bộ lọc
    applyAllFilters();
}

// Áp dụng tất cả bộ lọc (provider + search)
function applyAllFilters() {
    const modelSelect = document.getElementById('model-select');
    const modelSearchInput = document.getElementById('model-search');
    const allOptions = Array.from(modelSelect.querySelectorAll('option'));
    
    // Lấy giá trị search hiện tại
    const searchTerm = modelSearchInput ? modelSearchInput.value.toLowerCase().trim() : '';
    
    // Lấy provider được chọn
    const activeProvider = document.querySelector('.provider-option.active');
    const selectedProvider = activeProvider ? activeProvider.getAttribute('data-value') : '';
    
    let visibleCount = 0;
    
    allOptions.forEach(option => {
        const value = option.value.toLowerCase();
        const text = option.textContent.toLowerCase();
        
        // Luôn hiển thị option "Tất cả AI (Ensemble)"
        if (value === 'ensemble') {
            option.style.display = 'block';
            return;
        }
        
        // Kiểm tra provider filter
        let matchesProvider = true;
        if (selectedProvider && selectedProvider !== '') {
            const modelProvider = getModelProvider(option.value);
            matchesProvider = (modelProvider === selectedProvider);
        }
        
        // Kiểm tra search filter
        let matchesSearch = true;
        if (searchTerm && searchTerm !== '') {
            matchesSearch = text.includes(searchTerm) || 
                           value.includes(searchTerm) ||
                           // Hỗ trợ tìm kiếm theo từ khóa phổ biến
                           (searchTerm.includes('gpt') && (text.includes('gpt') || value.includes('gpt'))) ||
                           (searchTerm.includes('claude') && (text.includes('claude') || value.includes('claude'))) ||
                           (searchTerm.includes('gemini') && (text.includes('gemini') || value.includes('gemini'))) ||
                           (searchTerm.includes('doubao') && (text.includes('doubao') || value.includes('doubao'))) ||
                           (searchTerm.includes('deepseek') && (text.includes('deepseek') || value.includes('deepseek'))) ||
                           (searchTerm.includes('qwen') && (text.includes('qwen') || value.includes('qwen'))) ||
                           (searchTerm.includes('yi') && (text.includes('yi-') || value.includes('yi-'))) ||
                           (searchTerm.includes('flux') && (text.includes('flux') || value.includes('flux'))) ||
                           (searchTerm.includes('stable') && (text.includes('stable') || value.includes('stable'))) ||
                           (searchTerm.includes('dall') && (text.includes('dall') || value.includes('dall'))) ||
                           (searchTerm.includes('whisper') && (text.includes('whisper') || value.includes('whisper')));
        }
        
        // Hiển thị option nếu thỏa mãn cả 2 điều kiện
        if (matchesProvider && matchesSearch) {
            option.style.display = 'block';
            visibleCount++;
        } else {
            option.style.display = 'none';
        }
    });
    
    updateModelCount(visibleCount);
    updateCombinedStatus(selectedProvider, searchTerm);
}

// Hàm tìm kiếm riêng (gọi applyAllFilters)
function filterModelsBySearch(searchTerm) {
    applyAllFilters();
}

// Cập nhật trạng thái kết hợp (provider + search)
function updateCombinedStatus(selectedProvider, searchTerm) {
    const statusElement = document.getElementById('current-filter-status');
    if (!statusElement) return;
    
    let statusText = '';
    
    // Xây dựng status text dựa trên filters đang active
    if (selectedProvider && selectedProvider !== '' && searchTerm && searchTerm !== '') {
        // Cả provider và search đều active
        const providerName = getProviderDisplayName(selectedProvider);
        statusText = `📋 ${providerName} • � "${searchTerm}"`;
    } else if (selectedProvider && selectedProvider !== '') {
        // Chỉ có provider filter
        const providerName = getProviderDisplayName(selectedProvider);
        statusText = `📋 ${providerName} models`;
    } else if (searchTerm && searchTerm !== '') {
        // Chỉ có search filter
        statusText = `🔍 Tìm kiếm: "${searchTerm}"`;
    } else {
        // Không có filter nào
        statusText = '📋 Tất cả models';
    }
    
    statusElement.innerHTML = `<span>${statusText}</span>`;
}

// Lấy tên hiển thị của provider
function getProviderDisplayName(providerValue) {
    const providerNames = {
        'openai': 'OpenAI',
        'openai-plus': 'OpenAI Plus',
        'claude': 'Anthropic',
        'google': 'Google',
        'chinese': 'Chinese AI',
        'deepseek': 'DeepSeek',
        'doubao': 'Doubao',
        'image': 'Image AI',
        'video': 'Video AI',
        'siliconflow': 'SiliconFlow',
        'other': 'Khác'
    };
    return providerNames[providerValue] || providerValue;
}

// Cập nhật số lượng models hiển thị
function updateModelCount(visibleCount) {
    const totalModelsSpan = document.querySelector('.total-models');
    const filteredModelsSpan = document.querySelector('.filtered-models');
    
    if (totalModelsSpan) {
        // Đếm tổng số models (trừ ensemble option)
        const modelSelect = document.getElementById('model-select');
        const totalCount = modelSelect.querySelectorAll('option').length - 1;
        totalModelsSpan.textContent = `Tổng: ${totalCount} models`;
    }
    
    if (filteredModelsSpan) {
        filteredModelsSpan.textContent = `Hiển thị: ${visibleCount} models`;
    }
}

// Cập nhật trạng thái active của provider
function updateProviderActiveState(selectedProvider) {
    const providerOptions = document.querySelectorAll('.provider-option');
    
    providerOptions.forEach(option => {
        const value = option.getAttribute('data-value');
        if (value === selectedProvider) {
            option.classList.add('active');
        } else {
            option.classList.remove('active');
        }
    });
}

// Cập nhật số đếm models cho mỗi provider
function updateProviderCounts() {
    const modelSelect = document.getElementById('model-select');
    const allOptions = Array.from(modelSelect.querySelectorAll('option'));
    
    // Đếm tổng số models (trừ ensemble)
    const totalCount = allOptions.length - 1;
    const allCountSpan = document.getElementById('count-all');
    if (allCountSpan) {
        allCountSpan.textContent = `(${totalCount})`;
    }
    
    // Đếm models cho từng provider
    const providerCounts = {
        'openai': 0,
        'openai-plus': 0,
        'claude': 0,
        'google': 0,
        'chinese': 0,
        'deepseek': 0,
        'image': 0,
        'video': 0,
        'siliconflow': 0,
        'doubao': 0,
        'other': 0
    };
    
    allOptions.forEach(option => {
        if (option.value === 'ensemble') return; // Skip ensemble option
        
        const provider = getModelProvider(option.value);
        if (providerCounts.hasOwnProperty(provider)) {
            providerCounts[provider]++;
        }
    });
    
    // Cập nhật UI
    Object.keys(providerCounts).forEach(providerValue => {
        const countSpan = document.getElementById(`count-${providerValue}`);
        if (countSpan) {
            countSpan.textContent = `(${providerCounts[providerValue]})`;
        }
    });
}

// ===== WELCOME SCREEN FUNCTIONS =====

// Ẩn/hiện welcome screen
function toggleWelcomeScreen(show = true) {
    const welcomeScreen = document.getElementById('welcome-screen');
    const chatArea = document.getElementById('chat-area');
    
    if (welcomeScreen) {
        if (show) {
            welcomeScreen.style.display = 'flex';
            if (chatArea) {
                chatArea.classList.remove('has-messages');
            }
        } else {
            welcomeScreen.style.display = 'none';
            if (chatArea) {
                chatArea.classList.add('has-messages');
            }
        }
    }
}

// Tạo welcome screen content
function createWelcomeScreen() {
    return `
        <div id="welcome-screen" class="welcome-screen">
            <div class="welcome-header">
                <div class="welcome-logo">
                    <span class="logo-icon">🧠</span>
                    <h1>Thư Viện AI</h1>
                    <p class="tagline">Nền tảng AI đa năng với hơn 500+ Models</p>
                </div>
            </div>
            
            <div class="features-grid">
                <div class="feature-card highlight">
                    <div class="feature-icon">🚀</div>
                    <h3>500+ AI Models</h3>
                    <p>Truy cập hơn 500 mô hình AI từ OpenAI, Anthropic, Google, Chinese AI và nhiều nhà cung cấp hàng đầu</p>
                    <div class="feature-stats">
                        <span class="stat">✨ GPT-4, Claude, Gemini</span>
                        <span class="stat">🎨 DALL-E, MidJourney, Flux</span>
                        <span class="stat">🎵 Suno, Whisper, TTS</span>
                    </div>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">💬</div>
                    <h3>Chat Thông Minh</h3>
                    <p>Lưu trữ lịch sử cuộc trò chuyện, tìm kiếm models theo từ khóa, và quản lý nhiều cuộc hội thoại</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3>Lọc & Tìm Kiếm</h3>
                    <p>Tìm kiếm models theo tên, phân loại theo nhà cung cấp, lọc theo tính năng (Text, Image, Audio, Video)</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Xử Lý Đa Dạng</h3>
                    <p>Chế độ đơn lẻ, Ensemble (4 AI), hoặc Phân tán (28 AI) để có kết quả tối ưu nhất</p>
                </div>
            </div>
            
            <div class="cta-section">
                <h2>Bắt đầu trò chuyện với AI ngay!</h2>
                <p>Chọn một AI model phù hợp và bắt đầu cuộc trò chuyện của bạn</p>
                <div class="quick-actions">
                    <button class="quick-btn" onclick="selectModel('gpt-4')">
                        <span>🤖</span> GPT-4
                    </button>
                    <button class="quick-btn" onclick="selectModel('claude-3-5-sonnet-20241022')">
                        <span>🧠</span> Claude 3.5
                    </button>
                    <button class="quick-btn" onclick="selectModel('gemini-1-5-pro')">
                        <span>💎</span> Gemini Pro
                    </button>
                    <button class="quick-btn" onclick="selectModel('doubao-1-5-pro-256k-250115')">
                        <span>🎯</span> Doubao Pro
                    </button>
                </div>
            </div>
            
            <div class="stats-section">
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">AI Models</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">28</div>
                    <div class="stat-label">Nhà cung cấp</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">4</div>
                    <div class="stat-label">Loại AI</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Hoạt động</div>
                </div>
            </div>
        </div>
    `;
}

// Quick action để chọn model
function selectModel(modelValue) {
    const modelSelect = document.getElementById('model-select');
    if (modelSelect) {
        // Tìm và chọn model
        const option = modelSelect.querySelector(`option[value="${modelValue}"]`);
        if (option) {
            modelSelect.value = modelValue;
            
            // Trigger change event để update UI
            const changeEvent = new Event('change', { bubbles: true });
            modelSelect.dispatchEvent(changeEvent);
            
            // Focus vào chat input
            const chatInput = document.getElementById('chat-input');
            if (chatInput) {
                chatInput.focus();
                chatInput.placeholder = `Bắt đầu trò chuyện với ${getModelDisplayName(modelValue)}...`;
            }
            
            // Ẩn welcome screen
            toggleWelcomeScreen(false);
            
            console.log(`✅ Đã chọn model: ${getModelDisplayName(modelValue)}`);
        } else {
            console.warn(`❌ Không tìm thấy model: ${modelValue}`);
        }
    }
}

// Make selectModel globally accessible
window.selectModel = selectModel;

// ===== CHAT HISTORY FUNCTIONS =====

// Khởi tạo chat history data structure
let chatHistory = {
    conversations: [],
    currentConversationId: null
};

// Load chat history từ localStorage
function loadChatHistory() {
    const saved = localStorage.getItem('chatHistory');
    if (saved) {
        try {
            chatHistory = JSON.parse(saved);
        } catch (e) {
            console.error('Error loading chat history:', e);
            chatHistory = { conversations: [], currentConversationId: null };
        }
    }
}

// Save chat history vào localStorage
function saveChatHistory() {
    try {
        localStorage.setItem('chatHistory', JSON.stringify(chatHistory));
    } catch (e) {
        console.error('Error saving chat history:', e);
    }
}

// Tạo conversation mới
function createNewConversation() {
    const conversationId = 'conv_' + Date.now();
    const newConversation = {
        id: conversationId,
        title: 'Cuộc trò chuyện mới',
        timestamp: Date.now(),
        lastMessage: '',
        model: '',
        messages: []
    };
    
    chatHistory.conversations.unshift(newConversation); // Thêm vào đầu array
    chatHistory.currentConversationId = conversationId;
    
    // Clear chat area và hiển thị welcome screen
    const chatArea = document.getElementById('chat-area');
    if (chatArea) {
        chatArea.innerHTML = createWelcomeScreen();
        chatArea.classList.remove('has-messages');
    }
    
    saveChatHistory();
    renderConversationsList();
    updateActiveConversation();
    
    console.log(`✅ Tạo cuộc trò chuyện mới: ${conversationId}`);
}

// Load conversation đã chọn
function loadConversation(conversationId) {
    const conversation = chatHistory.conversations.find(c => c.id === conversationId);
    if (!conversation) return;
    
    chatHistory.currentConversationId = conversationId;
    
    // Clear chat area
    const chatArea = document.getElementById('chat-area');
    if (chatArea) {
        chatArea.innerHTML = '';
        
        // Nếu conversation có messages, load chúng. Nếu không, hiển thị welcome screen
        if (conversation.messages.length > 0) {
            conversation.messages.forEach(message => {
                if (message.type === 'user') {
                    renderMessage('user', message.content);
                } else if (message.type === 'bot') {
                    renderMessage('bot', message.content, message.model || 'AI');
                }
            });
            chatArea.classList.add('has-messages');
        } else {
            // Hiển thị welcome screen cho conversation trống
            chatArea.innerHTML = createWelcomeScreen();
            chatArea.classList.remove('has-messages');
        }
    }
    
    saveChatHistory();
    updateActiveConversation();
    
    console.log(`✅ Loaded conversation: ${conversationId}`);
}

// Xóa conversation
function deleteConversation(conversationId, event) {
    if (event) {
        event.stopPropagation(); // Ngăn trigger load conversation
    }
    
    if (!confirm('Bạn có chắc muốn xóa cuộc trò chuyện này?')) {
        return;
    }
    
    chatHistory.conversations = chatHistory.conversations.filter(c => c.id !== conversationId);
    
    // Nếu đang active conversation này, chuyển sang conversation khác hoặc tạo mới
    if (chatHistory.currentConversationId === conversationId) {
        if (chatHistory.conversations.length > 0) {
            loadConversation(chatHistory.conversations[0].id);
        } else {
            createNewConversation();
        }
    }
    
    saveChatHistory();
    renderConversationsList();
}

// Xóa tất cả conversations
function clearAllHistory() {
    if (!confirm('Bạn có chắc muốn xóa tất cả lịch sử trò chuyện?')) {
        return;
    }
    
    chatHistory = { conversations: [], currentConversationId: null };
    saveChatHistory();
    renderConversationsList();
    createNewConversation();
}

// Render danh sách conversations
function renderConversationsList() {
    const conversationsList = document.getElementById('conversations-list');
    if (!conversationsList) return;
    
    if (chatHistory.conversations.length === 0) {
        conversationsList.innerHTML = `
            <div class="no-conversations">
                <p>Chưa có cuộc trò chuyện nào</p>
                <p>Bắt đầu chat để tạo lịch sử!</p>
            </div>
        `;
        return;
    }
    
    conversationsList.innerHTML = chatHistory.conversations.map(conversation => {
        const isActive = conversation.id === chatHistory.currentConversationId;
        const timeStr = formatTime(conversation.timestamp);
        const preview = conversation.lastMessage || 'Chưa có tin nhắn';
        
        return `
            <div class="conversation-item ${isActive ? 'active' : ''}" 
                 onclick="loadConversation('${conversation.id}')">
                <div class="conversation-actions">
                    <button class="conversation-delete" 
                            onclick="deleteConversation('${conversation.id}', event)"
                            title="Xóa cuộc trò chuyện">
                        🗑️
                    </button>
                </div>
                <div class="conversation-title">${conversation.title}</div>
                <div class="conversation-meta">
                    <span class="conversation-time">${timeStr}</span>
                    ${conversation.model ? `<span class="conversation-model">${conversation.model}</span>` : ''}
                </div>
                <div class="conversation-preview">${preview}</div>
            </div>
        `;
    }).join('');
}

// Update active conversation visual state
function updateActiveConversation() {
    const items = document.querySelectorAll('.conversation-item');
    items.forEach(item => {
        const onclick = item.getAttribute('onclick');
        if (onclick && onclick.includes(chatHistory.currentConversationId)) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
}

// Format timestamp thành string dễ đọc
function formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diffMs = now - date;
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) {
        return date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    } else if (diffDays === 1) {
        return 'Hôm qua';
    } else if (diffDays < 7) {
        return `${diffDays} ngày trước`;
    } else {
        return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
    }
}

// Lưu message vào conversation hiện tại
function saveMessageToHistory(type, content, model = '') {
    if (!chatHistory.currentConversationId) {
        createNewConversation();
    }
    
    const conversation = chatHistory.conversations.find(c => c.id === chatHistory.currentConversationId);
    if (!conversation) return;
    
    const message = {
        type: type,
        content: content,
        timestamp: Date.now()
    };
    
    if (type === 'bot' && model) {
        message.model = model;
    }
    
    conversation.messages.push(message);
    conversation.lastMessage = type === 'user' ? content : `AI: ${content.substring(0, 100)}...`;
    conversation.timestamp = Date.now();
    
    // Cập nhật title nếu đây là message đầu tiên của user
    if (type === 'user' && conversation.messages.length === 1) {
        conversation.title = content.substring(0, 50) + (content.length > 50 ? '...' : '');
        conversation.model = getCurrentModel();
    }
    
    saveChatHistory();
    renderConversationsList();
}

// Lấy model hiện tại đang được chọn
function getCurrentModel() {
    const modelSelect = document.getElementById('model-select');
    if (modelSelect && modelSelect.value) {
        return getModelDisplayName(modelSelect.value);
    }
    return 'AI';
}

// Khởi tạo
document.addEventListener('DOMContentLoaded', function() {
    // Debug: Kiểm tra model-select element
    const modelSelect = document.getElementById('model-select');
    console.log('🔍 Model select element on DOMContentLoaded:', modelSelect);
    if (!modelSelect) {
        console.error('❌ Model select element not found on page load!');
    } else {
        console.log('✅ Model select element found, options count:', modelSelect.options.length);
        
        // Đảm bảo model-select hiển thị
        modelSelect.style.display = 'block';
        modelSelect.style.visibility = 'visible';
        modelSelect.style.opacity = '1';
        
        // Đảm bảo container cũng hiển thị
        const container = modelSelect.closest('.model-select-container');
        if (container) {
            container.style.display = 'block';
            container.style.visibility = 'visible';
            container.style.opacity = '1';
        }
    }
    
    // Khởi tạo chat history
    loadChatHistory();
    renderConversationsList();
    
    // Nếu chưa có conversation nào, tạo mới
    if (chatHistory.conversations.length === 0) {
        createNewConversation();
    } else {
        // Nếu có currentConversationId, load nó. Nếu không, load conversation đầu tiên
        const currentId = chatHistory.currentConversationId || chatHistory.conversations[0].id;
        loadConversation(currentId);
    }
    
    // Event listeners cho chat history
    const newChatBtn = document.getElementById('new-chat-btn');
    if (newChatBtn) {
        newChatBtn.addEventListener('click', createNewConversation);
    }
    
    const clearAllHistoryBtn = document.getElementById('clear-all-history');
    if (clearAllHistoryBtn) {
        clearAllHistoryBtn.addEventListener('click', clearAllHistory);
    }
    
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
    
    // Xử lý lọc provider
    const providerOptions = document.querySelectorAll('.provider-option');
    providerOptions.forEach(option => {
        option.addEventListener('click', function() {
            const providerValue = this.getAttribute('data-value');
            
            // Cập nhật active state
            updateProviderActiveState(providerValue);
            
            // Lọc models
            filterModelsByProvider(providerValue);
            
            // Status sẽ được cập nhật bởi applyAllFilters()
        });
    });
    
    // Xử lý nút reset filters
    const resetFiltersBtn = document.getElementById('reset-filters-btn');
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            // Reset về "Tất cả providers"
            updateProviderActiveState('');
            
            // Reset search input cũng
            const modelSearchInput = document.getElementById('model-search');
            if (modelSearchInput) {
                modelSearchInput.value = '';
            }
            
            // Áp dụng lại filters (sẽ hiển thị tất cả)
            applyAllFilters();
        });
    }
    
    // Khởi tạo số đếm provider
    updateProviderCounts();
    
    // Khởi tạo model count
    updateModelCount(document.querySelectorAll('#model-select option').length - 1);
    
    // Thêm chức năng tìm kiếm models
    const modelSearchInput = document.getElementById('model-search');
    if (modelSearchInput) {
        modelSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            filterModelsBySearch(searchTerm);
        });
        
        // Clear search khi nhấn Escape
        modelSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                filterModelsBySearch('');
            }
        });
    }
    
    console.log('✅ Thư Viện AI Frontend đã khởi tạo với Backend PHP');

    // Thêm event listener cho nút giới thiệu
    const introBtn = document.getElementById('intro-btn');
    console.log('🔍 Intro button element:', introBtn);
    if (introBtn) {
        introBtn.addEventListener('click', openIntroModal);
        console.log('✅ Event listener added to intro button');
    } else {
        console.error('❌ Intro button not found!');
    }
    
    // Thêm event listener cho đóng modal
    const closeBtn = document.querySelector('.intro-modal-close');
    const overlay = document.querySelector('.intro-modal-overlay');
    console.log('🔍 Close button:', closeBtn);
    console.log('🔍 Overlay:', overlay);
    
    if (closeBtn) {
        closeBtn.addEventListener('click', closeIntroModal);
        console.log('✅ Event listener added to close button');
    }
    if (overlay) {
        overlay.addEventListener('click', closeIntroModal);
        console.log('✅ Event listener added to overlay');
    }
    
    // Thêm event listener cho các nút quick actions
    const quickBtns = document.querySelectorAll('.quick-btn[data-model]');
    quickBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            selectModelAndClose(this.dataset.model);
        });
    });
});

// Hàm mở modal giới thiệu
function openIntroModal() {
    console.log('🔍 Attempting to open intro modal...');
    const modal = document.getElementById('intro-modal');
    console.log('📦 Modal element:', modal);
    
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Ngăn scroll body
        console.log('✅ Modal opened successfully!');
    } else {
        console.error('❌ Modal element not found!');
    }
}

// Hàm đóng modal giới thiệu
function closeIntroModal() {
    const modal = document.getElementById('intro-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Cho phép scroll body
    }
}

// Hàm chọn model và đóng modal
function selectModelAndClose(modelName) {
    // Cập nhật model được chọn
    localStorage.setItem('selectedModel', modelName);
    document.getElementById('selected-model').textContent = modelName;
    
    // Tìm và cập nhật UI dropdown nếu có
    const modelOptions = document.querySelectorAll('.model-option');
    modelOptions.forEach(option => {
        option.classList.remove('selected');
        if (option.textContent.includes(modelName)) {
            option.classList.add('selected');
        }
    });
    
    // Hiển thị thông báo
    showNotification(`Đã chọn mô hình: ${modelName}`, 'success');
    
    // Đóng modal
    closeIntroModal();
}

// Hàm hiển thị thông báo
function showNotification(message, type = 'info') {
    // Tạo element thông báo
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-icon">${type === 'success' ? '✓' : 'ℹ'}</span>
            <span class="notification-text">${message}</span>
        </div>
    `;
    
    // Thêm styles inline nếu chưa có
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        z-index: 1001;
        animation: slideInRight 0.3s ease;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    `;
    
    if (type === 'success') {
        notification.style.background = 'rgba(34, 197, 94, 0.9)';
    }
    
    // Thêm vào body
    document.body.appendChild(notification);
    
    // Tự động xóa sau 3 giây
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Test function để kiểm tra modal (có thể gọi từ console)
function testModal() {
    console.log('🧪 Testing modal...');
    const modal = document.getElementById('intro-modal');
    const button = document.getElementById('intro-btn');
    
    console.log('Modal element:', modal);
    console.log('Button element:', button);
    
    if (modal && button) {
        console.log('✅ All elements found, opening modal...');
        openIntroModal();
    } else {
        console.error('❌ Missing elements!');
    }
}

// Function để kiểm tra và sửa model-select
function fixModelSelect() {
    console.log('🔧 Fixing model-select...');
    const modelSelect = document.getElementById('model-select');
    const container = document.querySelector('.model-select-container');
    
    if (!modelSelect) {
        console.error('❌ Model select element not found!');
        return false;
    }
    
    if (!container) {
        console.error('❌ Model select container not found!');
        return false;
    }
    
    // Đảm bảo hiển thị
    modelSelect.style.display = 'block';
    modelSelect.style.visibility = 'visible';
    modelSelect.style.opacity = '1';
    modelSelect.style.height = '200px';
    
    container.style.display = 'block';
    container.style.visibility = 'visible';
    container.style.opacity = '1';
    
    // Force hiển thị tất cả options
    Array.from(modelSelect.options).forEach(option => {
        option.style.display = 'block';
        option.style.visibility = 'visible';
        option.style.opacity = '1';
    });
    
    console.log('✅ Model select fixed!');
    console.log('Model select:', modelSelect);
    console.log('Container:', container);
    console.log('Options count:', modelSelect.options.length);
    
    return true;
}

// Function để reset all filters và hiển thị tất cả models
function resetAllFilters() {
    console.log('🔄 Resetting all filters...');
    const modelSelect = document.getElementById('model-select');
    if (!modelSelect) return;
    
    // Clear tất cả filters
    const searchInput = document.querySelector('#model-search');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Force hiển thị tất cả options
    Array.from(modelSelect.options).forEach(option => {
        option.style.display = 'block';
        option.style.visibility = 'visible';
        option.style.opacity = '1';
    });
    
    console.log('✅ All filters reset, all options visible');
}

// Gọi fixModelSelect và resetAllFilters sau 1 giây
setTimeout(() => {
    fixModelSelect();
    resetAllFilters();
}, 1000);
