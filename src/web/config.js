/*
    ⚙️ CẤU HÌNH HỆ THỐNG THƯ VIỆN AI
    Quản lý các thông số cấu hình cho frontend và backend
    
    📝 HƯỚNG DẪN SỬ DỤNG:
    
    1. THAY ĐỔI BACKEND_URL:
       - Tìm dòng: BACKEND_URL: 'http://103.77.243.190',
       - Thay đổi URL này khi deploy sang server khác
       - Ví dụ: 'http://localhost:8000' hoặc 'https://your-domain.com'
    
    2. SỬ DỤNG TRONG CODE:
       - Luôn sử dụng CONFIG.API.url() hoặc CONFIG.ENDPOINTS thay vì hardcode
       - Ví dụ: CONFIG.API.url('AUTH_PHP') thay vì 'http://103.77.243.190/api/auth.php'
       - Ví dụ: CONFIG.BACKEND_URL + CONFIG.ENDPOINTS.AUTH_PHP
       
    3. CÁC ENDPOINT CÓ SẴN:
       - CONFIG.API.url('AUTH_PHP') -> '/api/auth.php'
       - CONFIG.API.url('ADMIN') -> '/api/admin'
       - CONFIG.API.url('CHAT_REAL') -> '/api/chat-real.php'
       - CONFIG.API.url('DOCUMENTS_PHP') -> '/api/documents.php'
       - Và nhiều endpoint khác trong CONFIG.ENDPOINTS
       
    4. TẠO URL VỚI QUERY PARAMS:
       - CONFIG.API.urlWithParams('AUTH_PHP', {action: 'login'})
       - Kết quả: 'http://backend-url/api/auth.php?action=login'
       
    5. LOAD CONFIG TRONG HTML:
       - Đảm bảo load config.js trước các script khác
       - <script src="config.js"></script>
       - Sau đó sử dụng: window.CONFIG hoặc CONFIG (nếu đã load)
       
    ⚠️ LƯU Ý:
    - KHÔNG hardcode URL trong các file code khác
    - Chỉ thay đổi URL ở file config.js này
    - Tất cả các file sẽ tự động sử dụng cấu hình từ file này
*/

const CONFIG = {
    // ===== BACKEND API =====
    // ⚠️ THAY ĐỔI URL NÀY KHI DEPLOY SANG SERVER KHÁC
    // Ví dụ: 'http://localhost:8000' hoặc 'https://your-domain.com'
    BACKEND_URL: 'http://103.77.243.190',

    // ===== API KEY4U =====
    KEY4U: {
        API_URL: "https://api.key4u.shop/v1/chat/completions",
        API_KEY: null, // Sẽ được load từ config.env
        DEFAULT_TEMPERATURE: 0.7,
        DEFAULT_MAX_TOKENS: 2000
    },
    
    // ===== ENSEMBLE MODELS =====
    ENSEMBLE: {
        TOP_MODELS: [
            'qwen3-235b-a22b',
            'gpt-4-turbo', 
            'claude-3-5-sonnet', 
            'gemini-2-5-pro', 
            'deepseek-v3'
        ],
        MAX_TOKENS_PER_MODEL: 1500
    },
    
    // ===== DEFAULT MODELS =====
    DEFAULT_MODELS: {
        CHAT: 'qwen3-235b-a22b',
        IMAGE: 'flux-kontext-max',
        AUDIO: 'whisper-1',
        VIDEO: 'veo2'
    },
    
    // ===== UI CONFIGURATION =====
    UI: {
        AUTO_SCROLL: true,
        SHOW_MODEL_NAME: true,
        TYPING_ANIMATION: false,
        THEME: 'black-white'
    },
    
    // ===== API ENDPOINTS =====
    ENDPOINTS: {
        AUTH: '/api/auth',
        AUTH_PHP: '/api/auth.php', // Backward compatibility
        ADMIN: '/api/admin',
        ADMIN_PHP: '/api/admin.php', // Backward compatibility
        CHAT: '/api/chat',
        CHAT_REAL: '/api/chat-real.php',
        HEALTH: '/api/health',
        DOCUMENTS: '/api/documents',
        DOCUMENTS_PHP: '/api/documents.php', // Backward compatibility
        MODELS: '/api/models',
        UPLOAD: '/api/upload',
        AI_TOOL: '/api/ai-tool',
        USER_PROFILE: '/api/user/profile',
        USER_HISTORY: '/api/user/history',
        LOGOUT: '/api/logout'
    },
    
    // ===== HELPER FUNCTIONS =====
    // Tạo full URL cho endpoint
    getUrl: function(endpoint) {
        // Nếu endpoint đã là full URL, return luôn
        if (endpoint.startsWith('http://') || endpoint.startsWith('https://')) {
            return endpoint;
        }
        
        // Nếu endpoint bắt đầu bằng /, ghép với BACKEND_URL
        if (endpoint.startsWith('/')) {
            return this.BACKEND_URL + endpoint;
        }
        
        // Nếu là key trong ENDPOINTS, lấy value
        if (this.ENDPOINTS[endpoint]) {
            return this.BACKEND_URL + this.ENDPOINTS[endpoint];
        }
        
        // Nếu không, ghép trực tiếp
        return this.BACKEND_URL + '/' + endpoint;
    },
    
    // Lấy BACKEND_URL với fallback
    getBackendUrl: function() {
        return this.BACKEND_URL || window.location.origin;
    }
};

// Backward compatibility
CONFIG.YESCALE = CONFIG.KEY4U;

// Helper functions global để dễ sử dụng
CONFIG.API = {
    // Tạo full API URL
    url: function(endpoint) {
        return CONFIG.getUrl(endpoint);
    },
    
    // Lấy endpoint từ key
    endpoint: function(key) {
        return CONFIG.ENDPOINTS[key] || key;
    },
    
    // Tạo full URL với query params
    urlWithParams: function(endpoint, params) {
        const baseUrl = CONFIG.getUrl(endpoint);
        if (!params || Object.keys(params).length === 0) {
            return baseUrl;
        }
        const queryString = new URLSearchParams(params).toString();
        return baseUrl + '?' + queryString;
    }
};

// Export config để sử dụng trong các script khác
window.CONFIG = CONFIG;

// Backward compatibility - expose BACKEND_URL trực tiếp
window.BACKEND_URL = CONFIG.getBackendUrl();

// Log để debug (chỉ trong development)
if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    console.log('⚙️ CONFIG loaded:', {
        BACKEND_URL: CONFIG.BACKEND_URL,
        ENDPOINTS: CONFIG.ENDPOINTS
    });
}