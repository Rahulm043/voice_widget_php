# 🚀 Laravel Handover Guide

This widget is a portable Gemini Voice Assistant. Follow these 3 steps to implement it:

### 1. Place the Files
- Move `voice-assistant.js` to your `public/js/` directory.
- Move `voice-assistant.blade.php` to `resources/views/components/`.

### 2. Configure API Key
Add your Gemini API Key to your `.env` file:
```env
GEMINI_API_KEY=your_key_here
```
And in `config/services.php`:
```php
'gemini' => [
    'key' => env('GEMINI_API_KEY'),
],
```

### 3. Usage
Include the component in any Blade template (e.g., `layouts/app.blade.php`):
```html
<x-voice-assistant api-key="{{ config('services.gemini.key') }}" />
```

---
**Dependencies**: The component automatically loads Tailwind CSS and Lucide Icons via CDN for simplicity.
