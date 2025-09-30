# 🧠 Thư Viện AI - Hệ thống AI Chat & Authentication

[![PHP Version](https://img.shields.io/badge/PHP-8.4+-blue.svg)](https://php.net)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-yellow.svg)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![HTML5](https://img.shields.io/badge/HTML5-5.0-orange.svg)](https://developer.mozilla.org/en-US/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-3.0-blue.svg)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Hệ thống AI Chat hiện đại với authentication hoàn chỉnh, hỗ trợ đăng nhập/đăng ký và quản lý trạng thái người dùng thông minh.

## 🎯 **Tổng quan dự án**

**Thư Viện AI** là một hệ thống web application hoàn chỉnh bao gồm:
- ✅ **Authentication System** - Đăng nhập/đăng ký với localStorage
- ✅ **Real AI Chat Interface** - Chat với AI models thật (GPT-4, Claude, Gemini...)
- ✅ **Key4U API Integration** - Kết nối với AI models thật qua Key4U API
- ✅ **Qwen API Integration** - Tích hợp Qwen AI với streaming response
- ✅ **ENSEMBLE Mode** - Chế độ kết hợp nhiều AI models
- ✅ **User Management** - Quản lý trạng thái người dùng
- ✅ **Responsive Design** - Giao diện thích ứng mọi thiết bị
- ✅ **Clean Architecture** - Kiến trúc gọn gàng, dễ bảo trì

## 🏗️ **Kiến trúc hệ thống**

```
┌─────────────────────────────────────────────────────────────────┐
│                        THƯ VIỆN AI                            │
├─────────────────────────────────────────────────────────────────┤
│  Frontend (Port 8001)     │  Backend PHP (Port 8000)          │
│  ┌─────────────────────┐  │  ┌─────────────────────────────┐   │
│  │ • index.html        │  │  │ • auth-login.php           │   │
│  │ • login.html        │  │  │ • auth-register.php        │   │
│  │ • register.html     │  │  │ • auth.php                 │   │
│  │ • script-backend.js │  │  │ • documents.php            │   │
│  │ • style.css         │  │  │ • health.php               │   │
│  │ • config.js         │  │  │ • index.php (Router)       │   │
│  └─────────────────────┘  │  └─────────────────────────────┘   │
│                            │                                    │
│  Authentication Flow:      │  API Endpoints:                   │
│  ┌─────────────────────┐  │  ┌─────────────────────────────┐   │
│  │ 1. User đăng ký     │◄─┼──│ POST /api/auth-register.php │   │
│  │ 2. Lưu vào localStorage│ │  POST /api/auth-login.php    │   │
│  │ 3. Ẩn nút đăng nhập  │  │  GET  /api/health.php        │   │
│  │ 4. Hiện thông tin user│ │  POST /api/documents.php     │   │
│  └─────────────────────┘  │  └─────────────────────────────┘   │
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
ThuVienAI/
├── 📁 assets/                     # Tài nguyên tĩnh
├── 📁 config/                     # Cấu hình toàn cục
│   └── 📄 config.js               # Cấu hình frontend
├── 📁 data/                       # Database & uploads
│   ├── 📁 database/
│   │   └── 📄 thuvien_ai.db      # SQLite database
│   └── 📁 uploads/               # File uploads
├── 📁 src/
│   ├── 📁 php-backend/           # Backend PHP
│   │   ├── 📁 api/               # API Endpoints (7 files)
│   │   │   ├── 📄 auth-login.php    # API đăng nhập
│   │   │   ├── 📄 auth-register.php # API đăng ký
│   │   │   ├── 📄 auth.php          # API auth chính
│   │   │   ├── 📄 documents.php     # API tài liệu
│   │   │   ├── 📄 health.php        # Health check
│   │   │   ├── 📄 index.php         # Router chính
│   │   │   └── 📄 test-simple.php   # Test endpoint
│   │   ├── 📁 config/            # Cấu hình backend
│   │   │   ├── 📄 Config.php         # Main config
│   │   │   └── 📄 Database.php       # Database connection
│   │   ├── 📁 middleware/        # Middleware
│   │   │   └── 📄 AuthMiddleware.php # Authentication
│   │   ├── 📁 models/            # Data Models
│   │   │   ├── 📄 AIQueryHistory.php # Chat history
│   │   │   ├── 📄 Document.php       # Document model
│   │   │   ├── 📄 Log.php            # Logging model
│   │   │   └── 📄 User.php           # User model
│   │   ├── 📁 services/          # Business Logic
│   │   │   ├── 📄 AIService.php      # AI processing
│   │   │   ├── 📄 DocumentService.php # Document processing
│   │   │   ├── 📄 Key4UService.php   # Key4U API service
│   │   │   ├── 📄 QwenService.php    # Qwen AI API service
│   │   │   └── 📄 UserService.php    # User management
│   │   ├── 📄 composer.json      # PHP dependencies
│   │   └── 📄 index.php          # Main entry point
│   └── 📁 web/                   # Frontend
│       ├── 📄 index.html         # Trang chủ (với auth logic)
│       ├── 📄 login.html         # Trang đăng nhập
│       ├── 📄 register.html      # Trang đăng ký
│       ├── 📄 script-backend.js  # JavaScript chính
│       ├── 📄 style.css          # CSS styling
│       ├── 📄 config.js          # Frontend config
│       ├── 📄 background.webp    # Background image
│       └── 📄 favicon.ico        # Site icon
├── 📄 README.md                  # Tài liệu này
├── 📄 CONFIGURATION.md           # Hướng dẫn cấu hình
├── 📄 HUONG_DAN_CAI_DAT.md       # Hướng dẫn cài đặt
├── 📄 DEPLOYMENT_SUMMARY.md      # Tóm tắt triển khai
├── 📄 CLEANUP_REPORT.md          # Báo cáo dọn dẹp
├── 📄 start-powershell.bat       # Script khởi động chính
├── 📄 start.bat                  # Script khởi động đơn giản
├── 📄 startfull.bat              # Script khởi động đầy đủ
├── 📄 config.env                 # Environment variables
└── 📄 config.env.example         # Environment template
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

### **3. AI Chat System**
- ✅ **Multiple AI Models** - Support for various AI providers (Key4U, Qwen)
- ✅ **Real-time Chat** - Instant messaging interface with streaming
- ✅ **Document Processing** - Upload and analyze documents
- ✅ **Processing Modes** - Single, ensemble, distributed
- ✅ **ENSEMBLE Mode** - Combine multiple AI responses
- ✅ **Qwen Integration** - Direct Qwen AI API with streaming
- ✅ **History Tracking** - Chat history and user queries

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
# Khởi động nhanh với AI models thật
.\start-ai.bat
```

### **Cách 2: PowerShell Launcher (Chi tiết)**

```bash
# Khởi động với thông tin chi tiết
.\start-powershell.bat
```

### **Cách 3: Khởi động thủ công**

```powershell
# Terminal 1: Backend PHP
cd src\php-backend
php -S 127.0.0.1:8000 -t .

# Terminal 2: Frontend
cd src\web
python -m http.server 8001
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

### **Utility APIs**
| Endpoint | Method | Mô tả | Response |
|----------|--------|-------|----------|
| `/api/health.php` | GET | Health check | `{status: "ok"}` |
| `/api/documents.php` | POST | Upload documents | `{success: true}` |
| `/api/index.php` | GET/POST | Main router | Depends on route |

### **Request/Response Examples**

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
    "created_at": "2025-01-29 10:30:00"
  }
}
```

#### **Login Request:**
```json
POST /api/auth-login.php
{
  "username": "testuser",
  "password": "password123"
}
```

#### **Login Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "user": {
    "id": 1234,
    "username": "testuser",
    "credits": 100,
    "role": "user"
  },
  "token": "jwt_token_here",
  "expires_in": 86400
}
```

## 🎨 **Giao diện người dùng**

### **Trang chủ (index.html)**
- **Sidebar** - Model selection, user info, document management
- **Chat Area** - Real-time messaging interface
- **Authentication UI** - Dynamic login/register buttons
- **User Dashboard** - Credits, profile, logout

### **Authentication Pages**
- **Login (login.html)** - Clean login form with validation
- **Register (register.html)** - Registration form with password confirmation
- **Auto-redirect** - Seamless navigation after auth

### **Responsive Features**
- **Mobile-first** - Optimized for mobile devices
- **Flexible Layout** - Adapts to different screen sizes
- **Touch-friendly** - Large buttons, easy navigation
- **Fast Loading** - Optimized assets, minimal dependencies

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
- [x] AI chat integration (Key4U + Qwen)
- [x] ENSEMBLE mode
- [x] Qwen API streaming
- [x] Document processing
- [x] User dashboard
- [x] Advanced features

### **Phase 3** 📋 (Planned)
- [ ] Multi-language support
- [ ] Plugin system
- [ ] Admin dashboard
- [ ] Analytics
- [ ] Performance optimization

## 📞 **Support**

- **Documentation**: README.md, CONFIGURATION.md
- **Issues**: GitHub Issues
- **Email**: support@thuvienai.com
- **Community**: Thư Viện AI Discord

## 📄 **License**

MIT License - Xem file [LICENSE](LICENSE) để biết thêm chi tiết.

## 👥 **Team**

- **Lead Developer**: Thư Viện AI Team
- **Backend**: PHP, Authentication, APIs
- **Frontend**: HTML5, CSS3, JavaScript
- **DevOps**: PowerShell, Batch Scripts

---

**© 2025 Thư Viện AI. All rights reserved.**
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

*Được xây dựng với ❤️ bằng PHP, JavaScript và modern web technologies.*