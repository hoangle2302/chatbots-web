/*
    ⚙️ CẤU HÌNH HỆ THỐNG THƯ VIỆN AI
    Quản lý các thông số cấu hình cho frontend và backend
*/

const CONFIG = {
    // ===== BACKEND API =====
    BACKEND_URL: 'http://127.0.0.1:8000',
    
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