<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userMessage = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($userMessage)) {
        echo json_encode(['reply' => 'I didn\'t catch that. Could you repeat?']);
        exit;
    }

    // Simple mock logic for demonstration
    $messageLower = strtolower($userMessage);
    $reply = '';

    if (strpos($messageLower, 'hello') !== false || strpos($messageLower, 'hi') !== false) {
        $reply = "Hello! I am your voice-enabled Gemini assistant. How can I help you today?";
    } elseif (strpos($messageLower, 'time') !== false) {
        $reply = "The current server time is " . date('h:i A') . ".";
    } elseif (strpos($messageLower, 'weather') !== false) {
        $reply = "I'm currently in a sandbox mode, so I can't check the live weather, but it looks like a great day for automation!";
    } elseif (strpos($messageLower, 'report') !== false) {
        $reply = "I have successfully exported this chat interface as a standalone PHP application. You can now use it in your reports.";
    } elseif (strpos($messageLower, 'who are you') !== false) {
        $reply = "I am a Gemini-powered voice assistant export, designed to demonstrate the power of AI in web interfaces.";
    } else {
        $reply = "That's interesting! I've received your message: '$userMessage'. How else can I assist you?";
    }

    // Add a slight delay to simulate processing
    usleep(800000); 

    echo json_encode(['reply' => $reply]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
?>
