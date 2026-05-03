# TryAutomate Gemini Voice Assistant Widget

A standalone, portable voice assistant widget built with the Gemini Live API. Designed for seamless integration into PHP Laravel applications.

## 🚀 Key Features
- **Real-time Voice Interaction**: Low-latency 24kHz audio streaming.
- **Natural Conversations**: Supports interruptions and human-like conversational traits.
- **Brand Guardrails**: Pre-configured with TryAutomate context and strict conversational steering.
- **Laravel Optimized**: Includes a ready-to-use Blade component.
- **Responsive UI**: Floating widget with real-time audio visualizers.

## 📂 Repository Structure
- `voice-assistant.js`: Core logic and audio processing.
- `voice-assistant.blade.php`: Laravel Blade component.
- `HANDOVER_GUIDE.md`: Full technical integration instructions.
- `test-widget.html`: Standalone HTML file for instant testing.
- `server.cjs`: Local Node.js server for testing.

## 🛠 Quick Start (Test Environment)
1. Clone this repository.
2. Create a `.env` file in the root with your Gemini API Key:
   ```env
   GEMINI_API_KEY=your_api_key_here
   ```
3. Install dependencies:
   ```bash
   npm install
   ```
4. Start the test server:
   ```bash
   npm start
   ```
5. Open `http://localhost:8000` in your browser.

## 🏗 Laravel Integration
Follow the steps in [HANDOVER_GUIDE.md](./HANDOVER_GUIDE.md) to integrate the widget into your Laravel project.

---
© 2026 TryAutomate.ai
