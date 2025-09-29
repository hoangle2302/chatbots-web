// Cấu hình API cho Thư Viện AI
// File này chứa các thông tin cấu hình API

const CONFIG = {
    // API Key4U
    KEY4U: {
        API_URL: "https://api.key4u.shop/v1/chat/completions",
        API_KEY: null, // Sẽ được load từ config.env
        DEFAULT_TEMPERATURE: 0.7,
        DEFAULT_MAX_TOKENS: 2000
    },
    
    // Cấu hình Ensemble - Sử dụng các model có sẵn
    ENSEMBLE: {
        TOP_MODELS: ['gpt-4-turbo', 'claude-3-5-sonnet', 'gemini-2-5-pro', 'deepseek-v3'],
        MAX_TOKENS_PER_MODEL: 1500
    },
    
    // Cấu hình UI
    UI: {
        AUTO_SCROLL: true,
        SHOW_MODEL_NAME: true,
        TYPING_ANIMATION: false
    }
};

// Backward compatibility
CONFIG.YESCALE = CONFIG.KEY4U;

// Export config để sử dụng trong script.js
window.CONFIG = CONFIG;
