# 🔑 Hướng dẫn cấu hình API Key cho AI Models thật

## 📋 Tổng quan

Để sử dụng AI models thật (GPT-4, Claude, Gemini...), bạn cần cấu hình Key4U API key trong file `config.env`.

## 🚀 Cách cấu hình

### **Bước 1: Lấy Key4U API Key**
1. Truy cập: https://api.key4u.shop
2. Đăng ký tài khoản
3. Lấy API key từ dashboard

### **Bước 2: Cập nhật config.env**
Mở file `config.env` và thay đổi dòng:
```env
# Thay đổi từ:
KEY4U_API_KEY=your_key4u_api_key_here

# Thành:
KEY4U_API_KEY=sk-your-actual-api-key-here
```

### **Bước 3: Restart server**
```bash
# Dừng server hiện tại (Ctrl+C trong terminal backend)
# Sau đó chạy lại:
.\start-powershell.bat
```

## 🧪 Test API Key

### **Test với API key thật:**
```bash
# Test API
php -r "
\$data = json_encode(['message' => 'Xin chào', 'model' => 'gpt-4-turbo']);
\$options = ['http' => ['header' => 'Content-Type: application/json', 'method' => 'POST', 'content' => \$data]];
\$context = stream_context_create(\$options);
\$result = file_get_contents('http://127.0.0.1:8000/api/chat-real.php', false, \$context);
echo \$result;
"
```

### **Kết quả mong đợi:**
```json
{
  "success": true,
  "data": {
    "content": "Xin chào! Tôi có thể giúp gì cho bạn?",
    "model": "gpt-4-turbo",
    "source": "key4u",
    "timestamp": "2025-01-29 10:30:00"
  }
}
```

## 🤖 AI Models được hỗ trợ

### **OpenAI Models:**
- `gpt-4-turbo` - GPT-4 Turbo
- `gpt-4` - GPT-4
- `gpt-3.5-turbo` - GPT-3.5 Turbo
- `o3` - OpenAI o3
- `o3-mini` - OpenAI o3 Mini

### **Anthropic Models:**
- `claude-3-5-sonnet` - Claude 3.5 Sonnet
- `claude-3-5-haiku` - Claude 3.5 Haiku
- `claude-3-opus` - Claude 3 Opus

### **Google Models:**
- `gemini-pro` - Gemini Pro
- `gemini-ultra` - Gemini Ultra
- `gemini-2-5-pro` - Gemini 2.5 Pro

### **Other Models:**
- `grok-2` - Grok-2
- `llama-3-3-70b` - Llama 3.3 70B
- `mistral-large` - Mistral Large
- `qwen-2-5-72b` - Qwen 2.5 72B
- `deepseek-v3` - DeepSeek-V3

## 🔄 Chế độ hoạt động

### **1. Chế độ mô phỏng (Không có API key):**
- Sử dụng response được lập trình sẵn
- Không tốn tiền API
- Phù hợp cho demo và test

### **2. Chế độ thật (Có API key):**
- Kết nối thật đến AI models
- Response thật từ AI
- Tốn phí API theo usage

## 🛠️ Troubleshooting

### **Lỗi "Invalid API key":**
```bash
# Kiểm tra API key trong config.env
Get-Content config.env | Select-String "KEY4U"
```

### **Lỗi "Failed to connect":**
```bash
# Kiểm tra kết nối internet
ping api.key4u.shop
```

### **Lỗi "Rate limit exceeded":**
- API key đã hết quota
- Cần nạp thêm tiền vào tài khoản Key4U

### **Lỗi "Model not available":**
- Model không được hỗ trợ
- Thử model khác trong danh sách

## 💰 Chi phí

### **Key4U Pricing:**
- GPT-4 Turbo: ~$0.01/1K tokens
- Claude 3.5 Sonnet: ~$0.003/1K tokens
- Gemini Pro: ~$0.001/1K tokens

### **Ước tính chi phí:**
- 1 tin nhắn trung bình: ~$0.001-0.01
- 100 tin nhắn: ~$0.1-1.0
- 1000 tin nhắn: ~$1-10

## 🎯 Kết quả sau khi cấu hình

### **Trước (Mô phỏng):**
```
User: "Giải thích AI là gì?"
AI: "AI (Artificial Intelligence) là công nghệ..."
Source: simulated
```

### **Sau (Thật):**
```
User: "Giải thích AI là gì?"
AI: "Trí tuệ nhân tạo (AI) là lĩnh vực khoa học máy tính..."
Source: key4u
Model: gpt-4-turbo
```

## 🚀 Sử dụng

### **1. Cấu hình API key**
### **2. Restart server**
### **3. Test chat với AI models thật**
### **4. Tận hưởng trải nghiệm AI thật!**

---

**💡 Lưu ý:** Luôn kiểm tra chi phí API và sử dụng có trách nhiệm!
