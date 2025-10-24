/*
    🚀 THƯ VIỆN AI - SCRIPT BACKEND
    Frontend sử dụng Backend PHP thay vì gọi API trực tiếp
*/

// ===== CẤU HÌNH =====
let currentUser = null;
let selectedCategory = '';
let selectedProvider = '';
let isTyping = false;
let currentConversation = null;
let conversations = [];

// ===== AUTHENTICATION =====
// Debug function để kiểm tra trạng thái
function debugUserStatus() {
    console.log('🔍 DEBUG USER STATUS:');
    console.log('- currentUser:', currentUser);
    console.log('- localStorage user_data:', localStorage.getItem('user_data'));
    console.log('- localStorage user_token:', localStorage.getItem('user_token'));
    
    // Kiểm tra DOM elements
    const userSection = document.getElementById('user-section');
    const authSection = document.getElementById('auth-section');
    console.log('- userSection display:', userSection ? userSection.style.display : 'not found');
    console.log('- authSection display:', authSection ? authSection.style.display : 'not found');
}

// Force reload user data
function forceReloadUser() {
    let userData = localStorage.getItem('user_data');
    if (!userData) {
        userData = localStorage.getItem('user');
    }
    if (!userData) {
        userData = localStorage.getItem('userData');
    }
    
    if (userData) {
        try {
            currentUser = JSON.parse(userData);
            console.log('🔄 Force reloaded currentUser:', currentUser);
            return true;
        } catch (error) {
            console.error('❌ Error force reloading user:', error);
            return false;
        }
    }
    return false;
}

// Kiểm tra trạng thái đăng nhập
async function checkLoginStatus() {
    try {
        // Thử tìm user data với các key khác nhau
        let userData = localStorage.getItem('user_data');
        if (!userData) {
            userData = localStorage.getItem('user');
        }
        if (!userData) {
            userData = localStorage.getItem('userData');
        }
        
        console.log('🔍 Checking login status, userData:', userData);
        
        if (userData) {
            currentUser = JSON.parse(userData);
            console.log('✅ User logged in:', currentUser);
            showUserSection();
            return true;
        }
        
        console.log('❌ No user data found');
        return false;
    } catch (error) {
        console.error('Lỗi kiểm tra đăng nhập:', error);
        return false;
    }
}

// Hiển thị section user
function showUserSection() {
    const authSection = document.getElementById('auth-section');
    const userSection = document.getElementById('user-section');
    
    if (authSection) authSection.style.display = 'none';
    if (userSection) {
        userSection.style.display = 'block';
        document.getElementById('user-name').textContent = currentUser.username;
        document.getElementById('user-credits').textContent = `${currentUser.credits || 0} credits`;
    }
}

// Đăng xuất
function logout() {
    localStorage.removeItem('user_data');
    localStorage.removeItem('user_token');
    currentUser = null;
    
    const authSection = document.getElementById('auth-section');
    const userSection = document.getElementById('user-section');
    
    if (authSection) authSection.style.display = 'block';
    if (userSection) userSection.style.display = 'none';
    
    location.reload();
}

// ===== API FUNCTIONS =====
// Gọi API với authentication
async function fetchAPI(url, options = {}) {
    const token = localStorage.getItem('user_token');
    const headers = {
        'Content-Type': 'application/json',
        ...options.headers
    };
    
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }
    
    try {
        const response = await fetch(url, {
            ...options,
            headers
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// ===== MODEL MANAGEMENT =====
// Load danh sách models từ config
async function loadModels() {
    try {
        // Sử dụng models từ config.js hoặc tạo danh sách mặc định
        let models = [];
        
        // Thử load từ window.APP_CONFIG trước
        if (window.APP_CONFIG?.MODELS && window.APP_CONFIG.MODELS.length > 0) {
            models = window.APP_CONFIG.MODELS;
        } else {
            // Tạo danh sách models mặc định nếu không có
            models = [
                'gpt-4-turbo', 'gpt-4o', 'gpt-4o-mini', 'gpt-3.5-turbo',
                'claude-3-5-sonnet', 'claude-3-haiku', 'claude-3-opus',
                'gemini-2-5-pro', 'gemini-1-5-pro', 'gemini-1-5-flash',
                'deepseek-v3', 'deepseek-coder', 'deepseek-chat',
                'qwen-2-5-72b', 'qwen-2-5-32b', 'qwen-2-5-14b',
                'llama-3-1-405b', 'llama-3-1-70b', 'llama-3-1-8b',
                'mixtral-8x7b', 'mixtral-8x22b', 'mixtral-8x3b',
                'dall-e-3', 'dall-e-2', 'midjourney', 'flux',
                'whisper-1', 'tts-1', 'tts-1-hd'
            ];
        }
        
        const modelSelect = document.getElementById('model-select');
        if (modelSelect) {
            modelSelect.innerHTML = '';
            models.forEach(model => {
                const option = document.createElement('option');
                option.value = model;
                option.textContent = model;
                modelSelect.appendChild(option);
            });
        }
        
        console.log(`✅ Loaded ${models.length} models`);
        return models;
    } catch (error) {
        console.error('Lỗi load models:', error);
        return [];
    }
}

// Lọc models theo provider
function filterModels() {
    const modelSelect = document.getElementById('model-select');
    const searchInput = document.getElementById('model-search');
    
    if (!modelSelect) return;
    
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const options = Array.from(modelSelect.options);
    
    options.forEach(option => {
        const modelName = option.textContent.toLowerCase();
        const matchesSearch = !searchTerm || modelName.includes(searchTerm);
        const matchesProvider = !selectedProvider || modelName.includes(selectedProvider);
        
        option.style.display = matchesSearch && matchesProvider ? 'block' : 'none';
    });
}

// ===== PROVIDER FILTERING =====
// Khởi tạo provider filtering
function initProviderFiltering() {
    const providerOptions = document.querySelectorAll('.provider-option');
    
    providerOptions.forEach(option => {
        option.addEventListener('click', () => {
            // Remove active class from all options
            providerOptions.forEach(opt => opt.classList.remove('active'));
            
            // Add active class to clicked option
            option.classList.add('active');
            
            // Update selected provider
            selectedProvider = option.dataset.value || '';
            
            // Filter models
            filterModels();
        });
    });
}

// ===== SEARCH FUNCTIONALITY =====
// Khởi tạo search
function initSearch() {
    const searchInput = document.getElementById('model-search');
    if (searchInput) {
        searchInput.addEventListener('input', filterModels);
    }
}

// ===== CHAT HISTORY MANAGEMENT =====
// Tạo cuộc trò chuyện mới
function createNewConversation() {
    const conversationId = 'conv_' + Date.now();
    const conversation = {
        id: conversationId,
        title: 'Cuộc trò chuyện mới',
        messages: [],
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
    };
    
    currentConversation = conversation;
    conversations.unshift(conversation);
    saveConversations();
    updateConversationsList();
    
    // Clear chat area
    const chatArea = document.getElementById('chat-area');
    if (chatArea) {
        chatArea.innerHTML = '';
    }
    
    console.log('✅ Created new conversation:', conversationId);
    return conversation;
}

// Lưu cuộc trò chuyện
function saveConversations() {
    localStorage.setItem('chat_conversations', JSON.stringify(conversations));
    console.log('💾 Saved conversations to localStorage');
}

// Load cuộc trò chuyện từ localStorage
function loadConversations() {
    const saved = localStorage.getItem('chat_conversations');
    if (saved) {
        try {
            conversations = JSON.parse(saved);
            console.log('📂 Loaded conversations:', conversations.length);
        } catch (error) {
            console.error('❌ Error loading conversations:', error);
            conversations = [];
        }
    }
    updateConversationsList();
}

// Cập nhật danh sách cuộc trò chuyện
function updateConversationsList() {
    const conversationsList = document.getElementById('conversations-list');
    if (!conversationsList) return;
    
    if (conversations.length === 0) {
        conversationsList.innerHTML = `
            <div class="no-conversations">
                <p>Chưa có cuộc trò chuyện nào</p>
                <p>Bắt đầu chat để tạo lịch sử!</p>
            </div>
        `;
        return;
    }
    
    conversationsList.innerHTML = conversations.map(conv => `
        <div class="conversation-item ${currentConversation && currentConversation.id === conv.id ? 'active' : ''}" 
             data-conversation-id="${conv.id}">
            <div class="conversation-title">${conv.title}</div>
            <div class="conversation-time">${new Date(conv.updatedAt).toLocaleString()}</div>
            <div class="conversation-messages-count">${conv.messages.length} tin nhắn</div>
        </div>
    `).join('');
    
    // Add click listeners
    conversationsList.querySelectorAll('.conversation-item').forEach(item => {
        item.addEventListener('click', () => {
            const conversationId = item.dataset.conversationId;
            loadConversation(conversationId);
        });
    });
}

// Load cuộc trò chuyện
function loadConversation(conversationId) {
    const conversation = conversations.find(conv => conv.id === conversationId);
    if (!conversation) return;
    
    currentConversation = conversation;
    updateConversationsList();
    
    // Clear và load messages
    const chatArea = document.getElementById('chat-area');
    if (chatArea) {
        chatArea.innerHTML = '';
        conversation.messages.forEach(msg => {
            addMessage(msg.content, msg.type, false); // false = không lưu lại
        });
    }
    
    console.log('📖 Loaded conversation:', conversationId);
}

// Thêm tin nhắn vào cuộc trò chuyện
function addMessageToConversation(content, type) {
    if (!currentConversation) {
        createNewConversation();
    }
    
    const message = {
        content: content,
        type: type,
        timestamp: new Date().toISOString()
    };
    
    currentConversation.messages.push(message);
    currentConversation.updatedAt = new Date().toISOString();
    
    // Update title nếu là tin nhắn đầu tiên
    if (currentConversation.messages.length === 1 && type === 'user') {
        currentConversation.title = content.length > 30 ? content.substring(0, 30) + '...' : content;
    }
    
    saveConversations();
    updateConversationsList();
}

// Xóa tất cả cuộc trò chuyện
function clearAllConversations() {
    if (confirm('Bạn có chắc muốn xóa tất cả lịch sử chat?')) {
        conversations = [];
        currentConversation = null;
        saveConversations();
        updateConversationsList();
        
        // Clear chat area
        const chatArea = document.getElementById('chat-area');
        if (chatArea) {
            chatArea.innerHTML = '';
        }
        
        console.log('🗑️ Cleared all conversations');
    }
}

// ===== CHAT FUNCTIONALITY =====
// Gửi tin nhắn
async function sendMessage() {
    const messageInput = document.getElementById('chat-input');
    const selectedModel = document.getElementById('model-select');
    
    if (!messageInput || !selectedModel) return;
    
    const message = messageInput.value.trim();
    const model = selectedModel.value;
    
    if (!message) {
        alert('Vui lòng nhập tin nhắn!');
        return;
    }
    
    // Nếu không chọn model, sử dụng QwenService mặc định
    if (!model || model === 'loading' || model === '') {
        console.log('🤖 Không chọn model, sử dụng QwenService mặc định');
        // Hiển thị thông báo cho user
        const chatHeader = document.querySelector('.chat-header span');
        if (chatHeader) {
            chatHeader.textContent = 'Trợ lý AI Qwen (mặc định)';
        }
    }
    
    if (!currentUser) {
        console.log('❌ currentUser is null, trying to sync...');
        debugUserStatus(); // Debug trạng thái
        
        // Thử force sync trước
        if (forceSyncUser()) {
            console.log('✅ Force sync successful');
        } else {
            // Thử force reload
            if (!forceReloadUser()) {
                // Thử cách khác - kiểm tra localStorage trực tiếp
                let userData = localStorage.getItem('user_data');
                if (!userData) {
                    userData = localStorage.getItem('user');
                }
                if (!userData) {
                    userData = localStorage.getItem('userData');
                }
                console.log('🔍 Direct localStorage check:', userData);
                
                if (userData) {
                    try {
                        const parsedUser = JSON.parse(userData);
                        console.log('✅ Parsed user from localStorage:', parsedUser);
                        currentUser = parsedUser;
                    } catch (error) {
                        console.error('❌ Error parsing user_data:', error);
                        alert('Vui lòng đăng nhập để sử dụng chat!');
                        return;
                    }
                } else {
                    alert('Vui lòng đăng nhập để sử dụng chat!');
                    return;
                }
            }
        }
    }
    
    console.log('✅ currentUser found:', currentUser);
    
    // Ẩn welcome screen nếu có
    const welcomeScreen = document.getElementById('welcome-screen');
    if (welcomeScreen) {
        welcomeScreen.style.display = 'none';
    }
    
    // Add user message to chat
    addMessage(message, 'user');
    messageInput.value = '';
    
    // Show loading
    showTypingIndicator();
    
    try {
        // Gọi API chat với QwenService làm mặc định
        const response = await fetchAPI('http://127.0.0.1:8000/api/chat-real.php', {
            method: 'POST',
            body: JSON.stringify({
                message: message,
                model: model || 'qwen3-235b-a22b', // Sử dụng Qwen mặc định nếu không chọn model
                user_id: currentUser.id,
                use_qwen_default: false // Sử dụng Key4U API thay vì QwenService
            })
        });
        
        hideTypingIndicator();
        
        console.log('🔍 API Response:', response);
        console.log('🔍 Response success:', response.success);
        console.log('🔍 Response data:', response.data);
        
        if (response.success) {
            const aiResponse = response.data.content || response.data.response || '';
            console.log('✅ Adding AI message:', aiResponse);
            
            // Kiểm tra nếu response rỗng
            if (!aiResponse || aiResponse.trim() === '') {
                addMessage('Xin chào! Tôi là AI assistant của Thư Viện AI. Hiện tại tôi đang được cập nhật, vui lòng thử lại sau.', 'assistant');
            } else {
                addMessage(aiResponse, 'assistant');
            }
        } else {
            console.log('❌ API Error:', response.message);
            addMessage('Lỗi: ' + (response.message || 'Không thể gửi tin nhắn'), 'assistant error');
        }
        
    } catch (error) {
        hideTypingIndicator();
        addMessage('Lỗi kết nối: ' + error.message, 'assistant error');
    }
}

// Thêm tin nhắn vào chat
function addMessage(content, type, saveToHistory = true) {
    console.log('🔍 addMessage called:', { content, type, saveToHistory });
    
    const messagesContainer = document.getElementById('chat-area');
    console.log('🔍 messagesContainer:', messagesContainer);
    
    if (!messagesContainer) {
        console.log('❌ messagesContainer not found');
        return;
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = content;
    
    console.log('🔍 Created messageDiv:', messageDiv);
    console.log('🔍 Appending to container...');
    
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    // Lưu vào lịch sử nếu cần
    if (saveToHistory) {
        addMessageToConversation(content, type);
    }
    
    console.log('✅ Message added successfully');
}

// Hiển thị typing indicator
function showTypingIndicator() {
    if (isTyping) return;
    
    isTyping = true;
    const messagesContainer = document.getElementById('chat-area');
    if (!messagesContainer) return;
    
    const typingDiv = document.createElement('div');
    typingDiv.className = 'message assistant loading';
    typingDiv.innerHTML = `
        <div class="loading">
            <span>AI đang suy nghĩ</span>
            <div class="loading-dots">
                <div class="loading-dot"></div>
                <div class="loading-dot"></div>
                <div class="loading-dot"></div>
            </div>
        </div>
    `;
    
    messagesContainer.appendChild(typingDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Ẩn typing indicator
function hideTypingIndicator() {
    isTyping = false;
    const loadingMessage = document.querySelector('.message.loading');
    if (loadingMessage) {
        loadingMessage.remove();
    }
}

// ===== CLEAR FUNCTIONALITY =====
// Xóa chat
function clearChat() {
    const messagesContainer = document.getElementById('chat-area');
    if (messagesContainer) {
        messagesContainer.innerHTML = '';
    }
}

// ===== KEYBOARD SHORTCUTS =====
// Khởi tạo keyboard shortcuts
function initKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
        // Ctrl + Enter để gửi tin nhắn
        if (e.ctrlKey && e.key === 'Enter') {
            sendMessage();
        }
        
        // Escape để clear input
        if (e.key === 'Escape') {
            const messageInput = document.getElementById('message-input');
            if (messageInput) {
                messageInput.value = '';
                messageInput.blur();
            }
        }
    });
}

// ===== INITIALIZATION =====
// Khởi tạo ứng dụng
async function init() {
    console.log('🚀 Khởi tạo Thư Viện AI...');
    
    try {
        // Kiểm tra đăng nhập
        console.log('🔍 Initializing, checking login...');
        await checkLoginStatus();
        console.log('🔍 After checkLoginStatus, currentUser:', currentUser);
        
        // Load models
        await loadModels();
        
        // Load chat history
        loadConversations();
        
        // Khởi tạo các tính năng
        initProviderFiltering();
        initSearch();
        initKeyboardShortcuts();
        
        // Khởi tạo event listeners
        const sendBtn = document.getElementById('send-btn');
        const messageInput = document.getElementById('chat-input');
        const clearBtn = document.querySelector('.btn-clear');
        
        // Chat history buttons
        const newChatBtn = document.getElementById('new-chat-btn');
        const clearAllBtn = document.getElementById('clear-all-history');
        
        if (sendBtn) {
            sendBtn.addEventListener('click', sendMessage);
        }
        
        if (messageInput) {
            messageInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
        }
        
        // Thêm event listener cho form submit
        const chatForm = document.getElementById('chat-form');
        if (chatForm) {
            chatForm.addEventListener('submit', (e) => {
                e.preventDefault();
                sendMessage();
            });
        }
        
        // Thêm event listener cho model select
        const modelSelect = document.getElementById('model-select');
        if (modelSelect) {
            modelSelect.addEventListener('change', function() {
                updateSelectedModelDisplay();
            });
        }
        
        if (clearBtn) {
            clearBtn.addEventListener('click', clearChat);
        }
        
        // Chat history event listeners
        if (newChatBtn) {
            newChatBtn.addEventListener('click', createNewConversation);
        }
        
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', clearAllConversations);
        }
        
        console.log('✅ Khởi tạo hoàn tất!');
        
    } catch (error) {
        console.error('❌ Lỗi khởi tạo:', error);
    }
}

// ===== START =====
document.addEventListener('DOMContentLoaded', init);

// Force sync với index.html
function forceSyncUser() {
    console.log('🔄 Force syncing user data...');
    let userData = localStorage.getItem('user_data');
    if (!userData) {
        userData = localStorage.getItem('user');
    }
    if (!userData) {
        userData = localStorage.getItem('userData');
    }
    
    console.log('🔍 Force sync userData:', userData);
    
    if (userData) {
        try {
            currentUser = JSON.parse(userData);
            console.log('✅ Force sync success - currentUser:', currentUser);
            return true;
        } catch (error) {
            console.error('❌ Force sync error:', error);
            return false;
        }
    } else {
        console.log('❌ No user data for force sync');
        return false;
    }
}

// Function để refresh credits
function refreshUserCredits() {
    const userCreditsElement = document.getElementById('user-credits');
    if (!userCreditsElement) return;
    
    // Lấy user data từ localStorage
    let userData = localStorage.getItem('user_data');
    if (!userData) {
        userData = localStorage.getItem('user');
    }
    if (!userData) {
        userData = localStorage.getItem('userData');
    }
    
    if (userData) {
        try {
            const user = JSON.parse(userData);
            userCreditsElement.textContent = (user.credits || 0) + ' credits';
            console.log('✅ Refreshed user credits:', user.credits || 0);
        } catch (error) {
            console.error('❌ Error parsing user data:', error);
        }
    }
}

// Expose debug function to global scope
window.debugUserStatus = debugUserStatus;
window.forceReloadUser = forceReloadUser;
window.forceSyncUser = forceSyncUser;
window.refreshUserCredits = refreshUserCredits;

// Set currentUser ngay khi script load
(function() {
    console.log('🚀 Script loaded, checking for user data...');
    let userData = localStorage.getItem('user_data');
    if (!userData) {
        userData = localStorage.getItem('user');
    }
    if (!userData) {
        userData = localStorage.getItem('userData');
    }
    
    console.log('🔍 Raw userData from localStorage:', userData);
    
    if (userData) {
        try {
            currentUser = JSON.parse(userData);
            console.log('✅ Set currentUser on script load:', currentUser);
        } catch (error) {
            console.error('❌ Error setting currentUser on script load:', error);
        }
    } else {
        console.log('❌ No user data found on script load');
        // Thử kiểm tra tất cả localStorage keys
        console.log('🔍 All localStorage keys:', Object.keys(localStorage));
        console.log('🔍 All localStorage values:', Object.values(localStorage));
    }
})();

// Đảm bảo currentUser được set ngay khi có thể
window.addEventListener('load', function() {
    console.log('🔄 Window loaded, checking currentUser...');
    if (!currentUser) {
        let userData = localStorage.getItem('user_data');
        if (!userData) {
            userData = localStorage.getItem('user');
        }
        if (!userData) {
            userData = localStorage.getItem('userData');
        }
        
        console.log('🔍 Window load - userData:', userData);
        if (userData) {
            try {
                currentUser = JSON.parse(userData);
                console.log('✅ Set currentUser on window load:', currentUser);
            } catch (error) {
                console.error('❌ Error setting currentUser on window load:', error);
            }
        } else {
            console.log('❌ No user data on window load');
            // Thử sync với index.html
            setTimeout(() => {
                console.log('🔄 Retrying user data sync...');
                let retryUserData = localStorage.getItem('user_data');
                if (!retryUserData) {
                    retryUserData = localStorage.getItem('user');
                }
                if (!retryUserData) {
                    retryUserData = localStorage.getItem('userData');
                }
                
                console.log('🔍 Retry userData:', retryUserData);
                if (retryUserData) {
                    try {
                        currentUser = JSON.parse(retryUserData);
                        console.log('✅ Retry success - currentUser:', currentUser);
                    } catch (error) {
                        console.error('❌ Retry error:', error);
                    }
                }
            }, 1000);
        }
    }
});

// Function để cập nhật hiển thị model đã chọn
function updateSelectedModelDisplay() {
    const selectedModel = document.getElementById('model-select');
    const chatHeader = document.querySelector('.chat-header span');
    
    if (selectedModel && chatHeader) {
        const model = selectedModel.value;
        
        if (!model || model === 'loading' || model === '') {
            chatHeader.textContent = 'Trợ lý AI Qwen (mặc định)';
        } else {
            chatHeader.textContent = `Trợ lý AI - ${model}`;
        }
    }
}