# 🤖 Danh sách AI Models - Thư Viện AI

## 📋 Tổng quan

Hệ thống Thư Viện AI hỗ trợ nhiều loại AI models từ các nhà cung cấp khác nhau, được phân loại theo chức năng và tích hợp qua Key4U API và Qwen AI.

## 🎯 Phân loại AI Models

### 1. **Chat/Text Models** (147 models)
Các mô hình chuyên về chat và xử lý văn bản:

#### **OpenAI Models:**
- `gpt-4o` - GPT-4 Omni (Multimodal)
- `gpt-4o-mini` - GPT-4 Omni Mini
- `gpt-4-turbo` - GPT-4 Turbo
- `gpt-4-turbo-preview` - GPT-4 Turbo Preview
- `gpt-4` - GPT-4 Standard
- `gpt-3.5-turbo` - GPT-3.5 Turbo
- `gpt-3.5-turbo-16k` - GPT-3.5 Turbo 16K
- `gpt-3.5-turbo-instruct` - GPT-3.5 Turbo Instruct

#### **Anthropic Models:**
- `claude-3-5-sonnet-20241022` - Claude 3.5 Sonnet (Latest)
- `claude-3-5-haiku-20241022` - Claude 3.5 Haiku
- `claude-3-opus-20240229` - Claude 3 Opus
- `claude-3-sonnet-20240229` - Claude 3 Sonnet
- `claude-3-haiku-20240307` - Claude 3 Haiku

#### **Google Models:**
- `gemini-1.5-pro` - Gemini 1.5 Pro
- `gemini-1.5-flash` - Gemini 1.5 Flash
- `gemini-pro` - Gemini Pro
- `gemini-pro-vision` - Gemini Pro Vision

#### **Qwen Models:**
- `qwen3-235b-a22b` - Qwen 3 235B (ENSEMBLE Mode)
- `qwen2.5-72b-instruct` - Qwen 2.5 72B Instruct
- `qwen2.5-32b-instruct` - Qwen 2.5 32B Instruct
- `qwen2.5-14b-instruct` - Qwen 2.5 14B Instruct
- `qwen2.5-7b-instruct` - Qwen 2.5 7B Instruct

#### **Other Models:**
- `llama-3.1-405b-instruct` - Llama 3.1 405B
- `llama-3.1-70b-instruct` - Llama 3.1 70B
- `llama-3.1-8b-instruct` - Llama 3.1 8B
- `mixtral-8x7b-instruct` - Mixtral 8x7B
- `deepseek-coder-33b-instruct` - DeepSeek Coder 33B

### 2. **Image Models** (95 models)
Các mô hình tạo và xử lý hình ảnh:

#### **DALL-E Models:**
- `dall-e-3` - DALL-E 3 (Latest)
- `dall-e-2` - DALL-E 2
- `dall-e-2-hd` - DALL-E 2 HD

#### **Midjourney Models:**
- `midjourney-v6` - Midjourney V6
- `midjourney-v5.2` - Midjourney V5.2
- `midjourney-v5.1` - Midjourney V5.1
- `midjourney-niji-6` - Niji V6
- `midjourney-niji-5` - Niji V5

#### **Stable Diffusion Models:**
- `stable-diffusion-xl` - Stable Diffusion XL
- `stable-diffusion-2.1` - Stable Diffusion 2.1
- `stable-diffusion-1.5` - Stable Diffusion 1.5

#### **Other Image Models:**
- `kandinsky-2.2` - Kandinsky 2.2
- `realistic-vision-v5` - Realistic Vision V5
- `dreamshaper-v8` - DreamShaper V8

### 3. **Audio Models** (31 models)
Các mô hình xử lý âm thanh:

#### **Text-to-Speech:**
- `tts-1` - OpenAI TTS-1
- `tts-1-hd` - OpenAI TTS-1 HD
- `eleven-labs-voice` - ElevenLabs Voice
- `azure-speech` - Azure Speech Services

#### **Speech-to-Text:**
- `whisper-1` - OpenAI Whisper
- `whisper-large-v3` - Whisper Large V3
- `azure-speech-to-text` - Azure Speech-to-Text

#### **Music Generation:**
- `jukebox` - Jukebox Music Generation
- `musiclm` - MusicLM
- `stable-audio` - Stable Audio

### 4. **Video Models** (19 models)
Các mô hình tạo và xử lý video:

#### **Video Generation:**
- `runway-gen-3` - Runway Gen-3
- `pika-labs-1.0` - Pika Labs 1.0
- `stable-video-diffusion` - Stable Video Diffusion
- `zeroscope-v2` - ZeroScope V2

#### **Video Editing:**
- `runway-edit` - Runway Video Editor
- `pika-edit` - Pika Video Editor
- `capcut-ai` - CapCut AI Editor

### 5. **Embedding Models** (31 models)
Các mô hình tạo vector embeddings:

#### **OpenAI Embeddings:**
- `text-embedding-3-large` - Text Embedding 3 Large
- `text-embedding-3-small` - Text Embedding 3 Small
- `text-embedding-ada-002` - Text Embedding Ada 002

#### **Other Embeddings:**
- `sentence-transformers` - Sentence Transformers
- `cohere-embed` - Cohere Embed
- `huggingface-embeddings` - Hugging Face Embeddings

### 6. **Moderation Models** (19 models)
Các mô hình kiểm duyệt nội dung:

#### **Content Moderation:**
- `openai-moderation` - OpenAI Moderation
- `perspective-api` - Perspective API
- `huggingface-moderation` - Hugging Face Moderation

### 7. **Special Models** (107 models)
Các mô hình đặc biệt:

#### **Code Generation:**
- `github-copilot` - GitHub Copilot
- `codex` - OpenAI Codex
- `deepseek-coder` - DeepSeek Coder

#### **Mathematical:**
- `wolfram-alpha` - Wolfram Alpha
- `math-gpt` - Math GPT

## 🚀 Cách sử dụng

### **1. Chọn Model trong Frontend:**
```html
<select id="model-select">
  <option value="ensemble">🤖 Tất cả AI (Ensemble)</option>
  <option value="gpt-4o">GPT-4 Omni</option>
  <option value="claude-3-5-sonnet">Claude 3.5 Sonnet</option>
  <option value="gemini-1.5-pro">Gemini 1.5 Pro</option>
  <option value="qwen3-235b-a22b">Qwen 3 235B</option>
  <!-- ... nhiều models khác -->
</select>
```

### **2. API Call:**
```javascript
const response = await fetch('/api/chat-real.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    message: 'Hello, how are you?',
    model: 'gpt-4o',
    mode: 'single'
  })
});
```

### **3. ENSEMBLE Mode:**
```javascript
// Chọn ENSEMBLE để sử dụng Qwen AI
const response = await fetch('/api/chat-real.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    message: 'Hello, how are you?',
    model: 'ensemble',
    mode: 'single'
  })
});
```

## 🔧 Cấu hình

### **Key4U API (Premium Models):**
```env
KEY4U_API_KEY=sk-your-key4u-api-key-here
```

### **Qwen AI (Free Models):**
```env
# Không cần API key - đã tích hợp sẵn
# Cookies và headers được cấu hình trong QwenService.php
```

## 📊 Thống kê Models

| Loại Model | Số lượng | Nhà cung cấp chính | Trạng thái |
|------------|----------|-------------------|------------|
| **Chat/Text** | 147 | OpenAI, Anthropic, Google | ✅ Hoạt động |
| **Image** | 95 | DALL-E, Midjourney, SD | ✅ Hoạt động |
| **Audio** | 31 | OpenAI, ElevenLabs | ✅ Hoạt động |
| **Video** | 19 | Runway, Pika Labs | ✅ Hoạt động |
| **Embedding** | 31 | OpenAI, Cohere | ✅ Hoạt động |
| **Moderation** | 19 | OpenAI, Perspective | ✅ Hoạt động |
| **Special** | 107 | GitHub, Wolfram | ✅ Hoạt động |
| **Tổng cộng** | **449** | **Multiple** | **✅ Active** |

## 🎯 Khuyến nghị sử dụng

### **Cho Chat thông thường:**
- **Miễn phí**: `qwen3-235b-a22b` (ENSEMBLE mode)
- **Premium**: `gpt-4o`, `claude-3-5-sonnet`

### **Cho tạo hình ảnh:**
- **Tốt nhất**: `dall-e-3`, `midjourney-v6`
- **Miễn phí**: `stable-diffusion-xl`

### **Cho xử lý âm thanh:**
- **TTS**: `tts-1-hd`, `eleven-labs-voice`
- **STT**: `whisper-large-v3`

### **Cho tạo video:**
- **Tốt nhất**: `runway-gen-3`, `pika-labs-1.0`

## 🔄 Cập nhật Models

Models được cập nhật thường xuyên. Để cập nhật danh sách:

1. **Kiểm tra Key4U API** cho models mới
2. **Cập nhật Key4UService.php** với models mới
3. **Test models** trước khi deploy
4. **Cập nhật frontend** với options mới

## 📞 Hỗ trợ

Nếu cần thêm models mới hoặc gặp vấn đề:
- Kiểm tra API documentation
- Test với curl commands
- Xem logs trong error.log
- Liên hệ support team

---

**📅 Cập nhật lần cuối**: 30/09/2025  
**🔢 Tổng số models**: 449  
**✅ Trạng thái**: Hoạt động bình thường
