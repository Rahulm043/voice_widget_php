const express = require('express');
const path = require('path');
const fs = require('fs');
const app = express();
const port = 8000;

// Function to read .env from root directory manually (to avoid extra dependencies)
function getEnvKey() {
    try {
        const rootEnvPath = path.join(__dirname, '..', '.env');
        if (fs.existsSync(rootEnvPath)) {
            const envContent = fs.readFileSync(rootEnvPath, 'utf8');
            const match = envContent.match(/VITE_GEMINI_API_KEY=(.*)/);
            return match ? match[1].trim() : '';
        }
    } catch (e) {
        console.error("Error reading .env file:", e);
    }
    return '';
}

const API_KEY = getEnvKey();

// Force the MIME type for .php files
app.use(express.static(__dirname, {
    setHeaders: function (res, filePath) {
        if (filePath.endsWith('.php')) {
            res.setHeader('Content-Type', 'text/html');
        }
    }
}));

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Serve test-widget.html and inject the API key
app.get('/', (req, res) => {
    let content = fs.readFileSync(path.join(__dirname, 'test-widget.html'), 'utf8');
    
    // Inject the API key into the window object so the frontend can find it
    const injection = `
    <script>
        window.INJECTED_GEMINI_API_KEY = "${API_KEY}";
        console.log("API Key successfully injected from root .env");
    </script>
    `;
    content = content.replace('<head>', '<head>' + injection);
    
    res.type('html').send(content);
});

// Mock endpoint for any remaining PHP requests
app.post('/api/chat.php', (req, res) => {
    res.json({ reply: "This widget now uses the direct Gemini Live API." });
});

app.listen(port, () => {
    console.log(`\n🚀 Gemini Voice Chat running at http://localhost:${port}`);
    console.log(`🔑 API Key found in root .env: ${API_KEY ? 'YES (Hidden for safety)' : 'NO'}`);
    console.log(`📂 Serving files from: ${__dirname}`);
});
