/*
    ⚙️ CẤU HÌNH HỆ THỐNG THƯ VIỆN AI
    Quản lý các thông số cấu hình cho frontend và backend
*/

// ============================================
// ⚙️ CẤU HÌNH HOST - DỄ BẢO TRÌ
// ============================================
// Thay đổi cấu hình ở đây khi deploy lên server mới hoặc đổi domain
// Tất cả các file khác sẽ tự động sử dụng cấu hình này
// ============================================

const HOST_CONFIG = {
    // 📍 Production host (IP hoặc domain server production)
    // Thay đổi khi deploy lên server mới
    PRODUCTION_HOST: 'http://103.77.243.190',
    
    // 🏠 Development host (localhost cho dev)
    // Có thể thay đổi nếu dev server chạy ở port khác
    DEVELOPMENT_HOST: 'http://localhost:8000',
    
    // 🔧 Chế độ môi trường
    // true: Luôn dùng PRODUCTION_HOST (cho production)
    // false: Tự động detect localhost hoặc dùng domain hiện tại (cho dev)
    USE_PRODUCTION: true, // ⚠️ Đặt false khi test local, true khi deploy
    
    // 🌐 Custom domain (nếu có domain riêng với SSL)
    // Đặt domain ở đây và set USE_PRODUCTION = true
    // Ví dụ: 'https://yourdomain.com' hoặc 'https://api.yourdomain.com'
    CUSTOM_HOST: null, // ⚠️ Đặt domain nếu có, ví dụ: 'https://yourdomain.com'
};

// Tính toán BACKEND_URL dựa trên cấu hình
function getBackendUrl() {
    // Nếu có CUSTOM_HOST, ưu tiên dùng nó
    if (HOST_CONFIG.CUSTOM_HOST) {
        return HOST_CONFIG.CUSTOM_HOST;
    }
    
    // Nếu USE_PRODUCTION = true, dùng PRODUCTION_HOST
    if (HOST_CONFIG.USE_PRODUCTION) {
        return HOST_CONFIG.PRODUCTION_HOST;
    }
    
    // Nếu đang chạy trên localhost, dùng DEVELOPMENT_HOST
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        return HOST_CONFIG.DEVELOPMENT_HOST;
    }
    
    // Mặc định: dùng domain hiện tại
    return window.location.origin;
}

const CONFIG = {
    // ===== BACKEND API =====
    // Tự động sử dụng cấu hình host từ HOST_CONFIG
    BACKEND_URL: getBackendUrl(),
    
    // Expose HOST_CONFIG để dễ debug
    HOST_CONFIG: HOST_CONFIG,
    
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
        AUTH: '/api/auth.php',
        ADMIN: '/api/admin.php',
        CHAT: '/api/chat-real.php',
        HEALTH: '/api/health.php',
        DOCUMENTS: '/api/documents.php'
    }
};

// Backward compatibility
CONFIG.YESCALE = CONFIG.KEY4U;

// Export config để sử dụng trong các script khác
window.CONFIG = CONFIG;