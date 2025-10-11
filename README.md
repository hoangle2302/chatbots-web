# 🧠 Thư Viện AI - Hệ thống AI Chat & Authentication với 449 AI Models

[![PHP Version](https://img.shields.io/badge/PHP-8.4+-blue.svg)](https://php.net)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-yellow.svg)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![HTML5](https://img.shields.io/badge/HTML5-5.0-orange.svg)](https://developer.mozilla.org/en-US/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-3.0-blue.svg)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![AI Models](https://img.shields.io/badge/AI_Models-449-purple.svg)](AI_MODELS_LIST.md)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Hệ thống AI Chat hiện đại với authentication hoàn chỉnh, tích hợp 449 AI models từ các nhà cung cấp hàng đầu, hỗ trợ ENSEMBLE mode và Qwen AI miễn phí.

## 🎯 **Tổng quan dự án**

**Thư Viện AI** là một hệ thống web application hoàn chỉnh bao gồm:

### 🔐 **Core Features:**
- ✅ **Authentication System** - Đăng nhập/đăng ký với localStorage
- ✅ **User Management** - Quản lý trạng thái người dùng
- ✅ **Document Processing** - Upload và phân tích tài liệu
- ✅ **Responsive Design** - Giao diện thích ứng mọi thiết bị
- ✅ **Clean Architecture** - Kiến trúc gọn gàng, dễ bảo trì

### 🤖 **AI Integration:**
- ✅ **449 AI Models** - Hỗ trợ đầy đủ các loại AI models
- ✅ **Key4U API Integration** - GPT-4, Claude, Gemini, DALL-E, Midjourney...
- ✅ **Qwen AI Integration** - Miễn phí với streaming response
- ✅ **ENSEMBLE Mode** - Kết hợp multiple AI responses
- ✅ **Real-time Chat** - Chat interface với streaming
- ✅ **Multi-modal Support** - Text, Image, Audio, Video processing

### 📊 **AI Models Breakdown:**
- 🗣️ **Chat/Text Models** (147) - GPT-4, Claude, Gemini, Qwen, Llama
- 🎨 **Image Models** (95) - DALL-E, Midjourney, Stable Diffusion
- 🎵 **Audio Models** (31) - TTS, STT, Music Generation
- 🎬 **Video Models** (19) - Runway, Pika Labs, Stable Video
- 🔍 **Embedding Models** (31) - OpenAI, Cohere, Hugging Face
- 🛡️ **Moderation Models** (19) - Content moderation
- ⚡ **Special Models** (107) - Code generation, Math, Tools

## 🏗️ **Kiến trúc hệ thống**

```
┌─────────────────────────────────────────────────────────────────┐
│                    THƯ VIỆN AI - 449 AI MODELS                │
├─────────────────────────────────────────────────────────────────┤
│  Frontend (Port 8001)     │  Backend PHP (Port 8000)          │
│  ┌─────────────────────┐  │  ┌─────────────────────────────┐   │
│  │ • index.html        │  │  │ • auth-login.php           │   │
│  │ • login.html        │  │  │ • auth-register.php        │   │
│  │ • register.html     │  │  │ • auth.php                 │   │
│  │ • dashboard.html    │  │  │ • chat-real.php (AI Chat)  │   │
│  │ • document-manager  │  │  │ • documents.php            │   │
│  │ • pricing.html      │  │  │ • health.php               │   │
│  │ • script-backend.js │  │  │ • index.php (Router)       │   │
│  │ • style.css         │  │  └─────────────────────────────┘   │
│  │ • config.js         │  │                                    │
│  └─────────────────────┘  │  AI Services:                      │
│                            │  ┌─────────────────────────────┐   │
│  AI Models Integration:    │  │ • Key4UService.php (449)   │   │
│  ┌─────────────────────┐  │  │ • QwenService.php (Free)   │   │
│  │ • 449 AI Models     │  │  │ • AIService.php (Core)     │   │
│  │ • ENSEMBLE Mode     │  │  │ • DocumentService.php      │   │
│  │ • Real-time Chat    │  │  │ • UserService.php          │   │
│  │ • Multi-modal UI    │  │  └─────────────────────────────┘   │
│  └─────────────────────┘  │                                    │
│                            │  API Endpoints:                   │
│  Authentication Flow:      │  ┌─────────────────────────────┐   │
│  ┌─────────────────────┐  │  │ POST /api/auth-register.php │   │
│  │ 1. User đăng ký     │◄─┼──│ POST /api/auth-login.php    │   │
│  │ 2. Lưu vào localStorage│ │  │ POST /api/chat-real.php    │   │
│  │ 3. Ẩn nút đăng nhập  │  │  │ GET  /api/health.php       │   │
│  │ 4. Hiện thông tin user│ │  │ POST /api/documents.php    │   │
│  │ 5. Chọn AI Model    │  │  └─────────────────────────────┘   │
│  └─────────────────────┘  │                                    │
└─────────────────────────────────────────────────────────────────┘
```

## 🛠️ **Stack công nghệ**

### **Backend (PHP)**
| Technology | Version | Purpose |
|------------|---------|---------|
| **PHP** | 8.4+ | Server-side programming |
| **Composer** | Latest | Dependency management |
| **JSON** | Native | Data serialization |
| **PDO** | Native | Database abstraction |
| **CORS Headers** | Native | Cross-origin requests |

### **Frontend (Web)**
| Technology | Version | Purpose |
|------------|---------|---------|
| **HTML5** | 5.0 | Semantic markup |
| **CSS3** | 3.0 | Styling & animations |
| **JavaScript** | ES6+ | Client-side logic |
| **Fetch API** | Native | HTTP requests |
| **localStorage** | Native | Client-side storage |
| **DOM API** | Native | Dynamic content |

### **Development Tools**
| Tool | Purpose |
|------|---------|
| **PHP Built-in Server** | Development server |
| **Python HTTP Server** | Frontend server |
| **PowerShell** | Windows automation |
| **Batch Scripts** | Cross-platform launchers |

## 📁 **Cấu trúc thư mục**

```
chatbots-web/
├── 📄 start.bat                  # Launcher duy nhất
├── 📄 config.env                 # Biến môi trường (DB, API keys)
├── 📄 config.env.example         # Mẫu cấu hình
├── 📄 README.md                  # Tài liệu chính
├── 📁 data/                      # Dữ liệu và uploads
│   ├── 📁 database/
│   │   └── 📄 thuvien_ai.db      # SQLite backup (tùy chọn)
│   └── 📁 uploads/               # File upload
└── 📁 src/
    ├── 📁 php-backend/           # Backend PHP
    │   ├── 📄 server.php         # Router cho PHP dev server
    │   ├── 📄 index.php          # Router JSON (fallback)
    │   ├── 📁 api/               # API endpoints
    │   │   ├── 📄 auth.php
    │   │   ├── 📄 chat-simple.php
    │   │   ├── 📄 documents.php
    │   │   ├── 📄 health.php
    │   │   └── 📄 admin.php
    │   ├── 📁 config/
    │   │   ├── 📄 Config.php
    │   │   └── 📄 Database.php
    │   ├── 📁 middleware/
    │   │   └── 📄 AuthMiddleware.php
    │   ├── 📁 models/
    │   │   ├── 📄 AIQueryHistory.php
    │   │   ├── 📄 Document.php
    │   │   ├── 📄 Log.php
    │   │   └── 📄 User.php
    │   ├── 📁 services/
    │   │   ├── 📄 AIService.php
    │   │   ├── 📄 DocumentService.php
    │   │   ├── 📄 Key4UService.php
    │   │   ├── 📄 QwenService.php
    │   │   └── 📄 UserService.php
    │   └── 📁 tools/
    │       ├── 📄 init-db.php
    │       ├── 📄 init-mysql.php
    │       └── 📄 mysql-schema.sql
    └── 📁 web/                   # Frontend
        ├── 📄 index.html
        ├── 📄 login.html
        ├── 📄 register.html
        ├── 📄 dashboard.html
        ├── 📄 document-manager.html
        ├── 📄 pricing.html
        ├── 📄 script-backend.js
        ├── 📄 style.css
        └── 📄 favicon.ico
```

## 🚀 **Tính năng chính**

### **1. Authentication System**
- ✅ **Đăng ký tài khoản** - Form validation, API integration
- ✅ **Đăng nhập** - Credential verification, session management
- ✅ **Quản lý trạng thái** - localStorage, auto UI update
- ✅ **Đăng xuất** - Clear session, reset UI state
- ✅ **Cross-tab sync** - Real-time status updates

### **2. User Interface**
- ✅ **Responsive Design** - Mobile-first, adaptive layout
- ✅ **Dynamic UI** - Hide/show elements based on auth state
- ✅ **User Info Display** - Username, credits, actions
- ✅ **Smooth Transitions** - CSS animations, loading states
- ✅ **Error Handling** - User-friendly error messages

### **3. AI Chat System (449 Models)**
- ✅ **449 AI Models** - Complete support for all AI model types
- ✅ **Key4U Integration** - GPT-4, Claude, Gemini, DALL-E, Midjourney...
- ✅ **Qwen AI Integration** - Free streaming AI with high quality
- ✅ **ENSEMBLE Mode** - Combine multiple AI responses intelligently
- ✅ **Real-time Streaming** - Live chat responses with streaming
- ✅ **Multi-modal Support** - Text, Image, Audio, Video processing
- ✅ **Document Processing** - Upload and analyze documents
- ✅ **Processing Modes** - Single, ensemble, distributed
- ✅ **History Tracking** - Complete chat history and user queries
- ✅ **Error Handling** - Robust error management for AI services

### **4. API Architecture**
- ✅ **RESTful APIs** - Clean, consistent endpoints
- ✅ **CORS Support** - Cross-origin request handling
- ✅ **Error Handling** - Comprehensive error responses
- ✅ **Input Validation** - Server-side validation
- ✅ **Security Headers** - XSS, CSRF protection

## 🔧 **Cài đặt và chạy**

### **Yêu cầu hệ thống**
- **PHP 8.4+** với extensions: `curl`, `json`, `pdo`
- **Composer** - PHP dependency manager
- **Python 3.x** - Frontend server (optional)
- **Git** - Version control
- **Windows 10/11** - Operating system

### **Cách 1: Quick Start (Khuyến nghị)**

```bash
# Khởi động nhanh toàn bộ hệ thống
.\start.bat
```

### **Cách 2: PowerShell (thủ công, hai cửa sổ)**

```bash
# Backend (cửa sổ 1)
cd src\php-backend
php -S 127.0.0.1:8000 server.php

# Frontend (cửa sổ 2)
cd src\web
php -S 127.0.0.1:8001 -t .
```

### **Cách 3: Khởi động thủ công (tùy chọn)**

```powershell
# Backend PHP
cd src\php-backend
php -S 127.0.0.1:8000 server.php

# Frontend
cd src\web
php -S 127.0.0.1:8001 -t .
```

### **Cách 3: Sử dụng XAMPP**

1. **Cài đặt XAMPP** từ https://www.apachefriends.org/
2. **Start Apache và MySQL**
3. **Copy project** vào `C:\xampp\htdocs\thuvien-ai`
4. **Cài đặt dependencies**:
   ```bash
   cd C:\xampp\htdocs\thuvien-ai\src\php-backend
   composer install
   ```
5. **Cấu hình database** trong `config.env`
6. **Truy cập**: http://localhost/thuvien-ai/src/web/

## 🌐 **API Endpoints**

### **Authentication APIs**
| Endpoint | Method | Mô tả | Request Body |
|----------|--------|-------|--------------|
| `/api/auth-register.php` | POST | Đăng ký user mới | `{username, password, email}` |
| `/api/auth-login.php` | POST | Đăng nhập user | `{username, password}` |
| `/api/auth.php` | POST | Auth chính (với actions) | `{action: register/login}` |

### **AI Chat APIs**
| Endpoint | Method | Mô tả | Request Body |
|----------|--------|-------|--------------|
| `/api/chat-real.php` | POST | AI Chat với 449 models | `{message, model, mode}` |
| `/api/chat-simple.php` | POST | Simple AI Chat | `{message, model}` |

### **Utility APIs**
| Endpoint | Method | Mô tả | Response |
|----------|--------|-------|----------|
| `/api/health.php` | GET | Health check | `{status: "ok"}` |
| `/api/documents.php` | POST | Upload documents | `{success: true}` |
| `/api/index.php` | GET/POST | Main router | Depends on route |

### **Request/Response Examples**

#### **AI Chat Request:**
```json
POST /api/chat-real.php
{
  "message": "Hello, how are you?",
  "model": "gpt-4o",
  "mode": "single"
}
```

#### **AI Chat Response:**
```json
{
  "success": true,
  "data": {
    "content": "Hello! I'm doing well, thank you for asking. How can I assist you today?",
    "model": "gpt-4o",
    "mode": "single",
    "source": "key4u",
    "tokens_used": 25,
    "response_time": 1.2,
    "timestamp": "2025-09-30 10:30:00"
  }
}
```

#### **ENSEMBLE Mode Request:**
```json
POST /api/chat-real.php
{
  "message": "Explain quantum computing",
  "model": "ensemble",
  "mode": "single"
}
```

#### **ENSEMBLE Mode Response:**
```json
{
  "success": true,
  "data": {
    "content": "🤖 **QWEN AI RESPONSE**\n\n✅ Qwen (qwen3-235b-a22b):\nQuantum computing is a revolutionary computing paradigm...",
    "model": "ensemble",
    "source": "ensemble",
    "tokens_used": 150,
    "response_time": 2.5,
    "timestamp": "2025-09-30 10:30:00",
    "ensemble_responses": {
      "qwen": {
        "provider": "Qwen",
        "model": "qwen3-235b-a22b",
        "content": "Quantum computing is a revolutionary computing paradigm...",
        "success": true
      }
    }
  }
}
```

#### **Register Request:**
```json
POST /api/auth-register.php
{
  "username": "testuser",
  "password": "password123",
  "email": "test@example.com"
}
```

#### **Register Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "user": {
    "id": 1234,
    "username": "testuser",
    "email": "test@example.com",
    "credits": 100,
    "role": "user",
    "created_at": "2025-09-30 10:30:00"
  }
}
```

## 🎨 **Giao diện người dùng**

### **Trang chủ (index.html)**
- **AI Model Selection** - 449 AI models với phân loại rõ ràng
- **Chat Interface** - Real-time messaging với streaming
- **ENSEMBLE Mode** - Kết hợp multiple AI responses
- **Authentication UI** - Dynamic login/register buttons
- **User Dashboard** - Credits, profile, logout

### **AI Chat Features**
- **Model Categories** - Chat/Text, Image, Audio, Video, Embedding, Moderation
- **Real-time Streaming** - Live responses từ AI models
- **Glass Effect UI** - Modern design với animations
- **Error Handling** - User-friendly error messages
- **History Tracking** - Complete chat history

### **Additional Pages**
- **Dashboard (dashboard.html)** - User management interface
- **Document Manager (document-manager.html)** - File upload và processing
- **Pricing (pricing.html)** - Bảng giá và plans
- **Login/Register** - Authentication forms với validation

### **Responsive Features**
- **Mobile-first** - Optimized for mobile devices
- **Flexible Layout** - Adapts to different screen sizes
- **Touch-friendly** - Large buttons, easy navigation
- **Fast Loading** - Optimized assets, minimal dependencies
- **Glass Effects** - Modern UI với blur và transparency

## 🔒 **Bảo mật**

### **Frontend Security**
- **Input Validation** - Client-side validation
- **XSS Prevention** - Sanitized output
- **CSRF Protection** - Token-based protection
- **Secure Storage** - localStorage with validation

### **Backend Security**
- **API Validation** - Server-side input validation
- **CORS Headers** - Controlled cross-origin access
- **Error Handling** - No sensitive info exposure
- **Rate Limiting** - Prevention of abuse

### **Data Protection**
- **No Password Storage** - Passwords not stored in plain text
- **Session Management** - Secure token handling
- **Input Sanitization** - All inputs cleaned
- **Error Logging** - Comprehensive audit trail

## 📊 **Performance**

### **Optimization Features**
- **Minimal Dependencies** - Only essential libraries
- **Efficient APIs** - Optimized database queries
- **Caching Strategy** - localStorage for user data
- **Lazy Loading** - On-demand resource loading

### **Monitoring**
- **Health Checks** - `/api/health.php` endpoint
- **Error Logging** - PHP error logs
- **Performance Metrics** - Response time tracking
- **User Analytics** - Usage statistics

## 🧪 **Testing**

### **Manual Testing**
```bash
# Test authentication flow
1. Truy cập http://127.0.0.1:8001/index.html
2. Đăng ký tài khoản mới
3. Kiểm tra UI thay đổi (ẩn nút đăng nhập)
4. Đăng xuất và kiểm tra UI reset
```

### **API Testing**
```bash
# Test register API
curl -X POST http://127.0.0.1:8000/api/auth-register.php \
  -H "Content-Type: application/json" \
  -d '{"username":"test","password":"test123","email":"test@example.com"}'

# Test login API
curl -X POST http://127.0.0.1:8000/api/auth-login.php \
  -H "Content-Type: application/json" \
  -d '{"username":"test","password":"test123"}'
```

## 🚀 **Deployment**

### **Production Setup**
1. **Web Server** - Apache/Nginx với PHP-FPM
2. **Database** - MySQL hoặc SQLite
3. **SSL Certificate** - HTTPS encryption
4. **Environment** - Production config
5. **Monitoring** - Error tracking

### **Environment Configuration**
```env
# config.env
# Database Configuration
DATABASE_PATH=data/database/thuvien_ai.db

# Key4U API Configuration (for real AI models)
KEY4U_API_KEY=sk-your-actual-key4u-api-key-here

# Server Configuration
SERVER_PORT=8000
DEBUG_MODE=false
```

### **API Key Setup (Optional)**
Để sử dụng AI models thật:

#### **Key4U API (GPT-4, Claude, Gemini...):**
1. **Lấy Key4U API key** từ https://api.key4u.shop
2. **Cập nhật config.env**: `KEY4U_API_KEY=sk-your-key-here`

#### **Qwen AI API (Miễn phí):**
- ✅ **Đã tích hợp sẵn** - Không cần API key
- ✅ **Streaming response** - Real-time chat
- ✅ **ENSEMBLE mode** - Chỉ sử dụng Qwen AI

**Không có API key**: Hệ thống sẽ sử dụng Qwen AI (miễn phí)
**Có Key4U API key**: Kết nối thêm với GPT-4, Claude, Gemini (có phí)

## 🤝 **Contributing**

### **Development Setup**
1. Fork repository
2. Create feature branch
3. Make changes
4. Test thoroughly
5. Submit pull request

### **Code Standards**
- **PSR-12** - PHP coding standards
- **ESLint** - JavaScript linting
- **Prettier** - Code formatting
- **JSDoc** - Documentation

## 📈 **Roadmap**

### **Phase 1** ✅ (Completed)
- [x] Basic authentication system
- [x] User registration and login
- [x] Dynamic UI management
- [x] Clean project structure
- [x] API documentation

### **Phase 2** ✅ (Completed)
- [x] AI chat integration (449 models)
- [x] Key4U API integration (GPT-4, Claude, Gemini...)
- [x] Qwen AI integration (Free streaming)
- [x] ENSEMBLE mode
- [x] Multi-modal support (Text, Image, Audio, Video)
- [x] Document processing
- [x] User dashboard
- [x] Advanced features
- [x] Glass effect UI
- [x] Error handling

### **Phase 3** 📋 (Planned)
- [ ] Multi-language support
- [ ] Plugin system
- [ ] Admin dashboard
- [ ] Analytics dashboard
- [ ] Performance optimization
- [ ] Mobile app
- [ ] API rate limiting
- [ ] Advanced AI features

## 📞 **Support**

- **Documentation**: 
  - README.md (489 lines) - Tài liệu chính
  - AI_MODELS_LIST.md - Danh sách 449 AI models
  - PROJECT_STATUS.md - Trạng thái dự án
  - CONFIGURATION.md - Hướng dẫn cấu hình
  - HUONG_DAN_CAI_DAT.md - Hướng dẫn cài đặt
  - DEPLOYMENT_SUMMARY.md - Tóm tắt triển khai
  - API_KEY_SETUP.md - Hướng dẫn API keys
- **Issues**: GitHub Issues
- **Email**: support@thuvienai.com
- **Community**: Thư Viện AI Discord

## 📄 **License**

MIT License - Xem file [LICENSE](LICENSE) để biết thêm chi tiết.

## 👥 **Team**

- **Lead Developer**: Thư Viện AI Team
- **Backend**: PHP, Authentication, APIs, AI Integration
- **Frontend**: HTML5, CSS3, JavaScript, Glass Effects
- **AI Integration**: Key4U API, Qwen AI, 449 Models
- **DevOps**: PowerShell, Batch Scripts, Deployment

---

**© 2025 Thư Viện AI. All rights reserved.**
<<<<<<< HEAD

---

## 🎯 **Quick Start Guide**

### **1. Khởi động nhanh:**
```bash
# Cách 1: Khởi động với AI models
.\start-ai.bat

# Cách 2: Khởi động chi tiết
.\start-powershell.bat
```

### **2. Truy cập ứng dụng:**
- **Frontend**: http://127.0.0.1:8001/index.html
- **Backend**: http://127.0.0.1:8000/test-simple.php

### **3. Sử dụng AI:**
1. **Chọn AI Model** - 449 models có sẵn
2. **Chọn ENSEMBLE** - Sử dụng Qwen AI miễn phí
3. **Chat** - Gửi tin nhắn và nhận response
4. **Upload Documents** - Phân tích tài liệu

### **4. Tính năng chính:**
- ✅ **449 AI Models** - GPT-4, Claude, Gemini, DALL-E, Midjourney...
- ✅ **Qwen AI Free** - Streaming AI miễn phí
- ✅ **ENSEMBLE Mode** - Kết hợp multiple AI
- ✅ **Multi-modal** - Text, Image, Audio, Video
- ✅ **Authentication** - Đăng ký/đăng nhập
- ✅ **Document Processing** - Upload và phân tích
- ✅ **Modern UI** - Glass effects và animations

**🚀 Sẵn sàng sử dụng ngay với 449 AI models!**
#
#                      _oo0oo_
#                     088888880
#                     88" . "88
#                     (| -_- |)
#                     0\  =  /0
#                   ___/`---'\___
#                 .' \\|     |// '.
#                / \\|||  :  |||// \
#               / _||||| -:- |||||- \
#              |   | \\\  -  /// |   |
#              | \_|  ''\---/''  |_/ |
#              \  .-\__  '-'  ___/-. /
#            ___'. .'  /--.--\  `. .'___
#         ."" '<  `.___\_<|>_/___.' >' "".
#        | | :  `- \`.;`\ _ /`;.`/ - ` : | |
#        \  \ `_.   \_ __\ /__ _/   .-` /  /
#    =====`-.____`.___ \_____/___.-`___.-'=====
#                      `=---='
#
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
#      Phật phù hộ, không bao giờ BUG
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
#                         \|/
#                        {   } 
#                     a di đà phật
=======
## Danh sách thành viên đóng góp cho dự án
| STT | Họ và Tên | MSSV | Vai Trò |
|--|--|--|--|
| 01 | <div align="center">Trần Hải Bằng</div> | <div align="center">000</div> | <div align="center">Nhóm Trưởng</div> |
| 02 | <div align="center">Lê Huy Hoàng</div> | <div align="center">077205003839</div> | <div align="center">Thành Viên</div> |
| 03 | <div align="center">Lương Thị Bích Hằng</div> | <div align="center">000</div> | <div align="center">Thành Viên</div> |
| 04 | <div align="center">Phan Minh Hòa</div> | <div align="center">000</div> | <div align="center">Thành Viên</div> |
| 05 | <div align="center">Hồ Ngọc Quyền</div> | <div align="center">000</div> | <div align="center">Thành Viên</div> |
>>>>>>> f5dc12ac1adc6b616da13bea29435aac97e9a94a

*Được xây dựng với ❤️ bằng PHP, JavaScript và modern web technologies.*