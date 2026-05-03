# TryAutomate Gemini Voice Assistant - Laravel Handover Guide

This guide explains how to integrate the Gemini-powered voice assistant into a PHP Laravel website.

## Prerequisites
- **Tailwind CSS**: The UI uses standard Tailwind classes.
- **Lucide Icons**: Used for buttons and status indicators.
- **Gemini API Key**: A valid key from Google AI Studio.

## 1. Asset Placement

### JavaScript Logic
Copy `voice-assistant.js` to your project's public JavaScript directory:
- **Path**: `public/js/voice-assistant.js`

### Blade Component
Copy `voice-assistant.blade.php` to your components directory:
- **Path**: `resources/views/components/voice-assistant.blade.php`

## 2. Configuration

Add your Gemini API key to your Laravel environment:

**`.env`**
```env
GEMINI_API_KEY=your_api_key_here
```

**`config/services.php`**
```php
'gemini' => [
    'key' => env('GEMINI_API_KEY'),
],
```

## 3. Usage

In your main layout file (e.g., `app.blade.php`), simply include the component. It is recommended to place it just before the closing `</body>` tag.

```html
<x-voice-assistant 
    :api-key="config('services.gemini.key')" 
    context="TryAutomate is an AI automation brand..."
/>
```

### Component Props
- `api-key`: (Required) Your Google Gemini API Key.
- `context`: (Optional) The custom knowledge base for the AI to follow.
- `instruction`: (Optional) The system prompt for the AI's personality.

## 4. How it Works
1. **Frontend**: The Blade component renders a floating button and a hidden chat window.
2. **Audio**: It uses a custom `AudioStreamer` class to handle PCM audio recording (16kHz) and playback (24kHz).
3. **Connection**: It uses the `@google/genai` library (loaded via ESM) to establish a direct WebSocket connection to Gemini's real-time voice model.
4. **Integration**: No backend PHP processing is required for the voice session, as the connection is direct from the browser to Google's servers.

## Security Note
The API key is passed to the frontend. For production environments, ensure your Gemini API key has **referral restrictions** set in the Google Cloud Console to prevent unauthorized use on other domains.
