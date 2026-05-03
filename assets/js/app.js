$(document).ready(function() {
    const $chatWindow = $('#chatWindow');
    const $userInput = $('#userInput');
    const $sendBtn = $('#sendBtn');
    const $voiceBtn = $('#voiceBtn');
    const $voiceStatus = $('#voiceStatus');
    const $clearChat = $('#clearChat');

    // Speech Recognition Setup
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    let recognition;
    let isListening = false;

    if (SpeechRecognition) {
        recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.lang = 'en-US';
        recognition.interimResults = false;

        recognition.onstart = function() {
            isListening = true;
            $voiceBtn.addClass('active');
            $voiceStatus.removeClass('d-none');
        };

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            $userInput.val(transcript);
            sendMessage();
        };

        recognition.onerror = function(event) {
            console.error('Speech recognition error:', event.error);
            stopListening();
        };

        recognition.onend = function() {
            stopListening();
        };
    } else {
        $voiceBtn.hide();
        console.warn('Speech Recognition API not supported in this browser.');
    }

    function stopListening() {
        isListening = false;
        $voiceBtn.removeClass('active');
        $voiceStatus.addClass('d-none');
        if (recognition) recognition.stop();
    }

    // Speech Synthesis Setup
    function speak(text) {
        if ('speechSynthesis' in window) {
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.rate = 1.0;
            utterance.pitch = 1.0;
            window.speechSynthesis.speak(utterance);
        }
    }

    // Chat Functions
    function appendMessage(role, text) {
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const msgClass = role === 'user' ? 'user-msg' : 'bot-msg';
        const bubbleClass = role === 'user' ? 'bg-primary-gradient text-white border-0' : 'bg-white border';
        
        const html = `
            <div class="chat-msg ${msgClass} mb-4">
                <div class="msg-bubble shadow-sm p-3 rounded-4 ${bubbleClass}">
                    ${text}
                </div>
                <div class="text-muted extra-small mt-1 ${role === 'user' ? 'pe-2' : 'ps-2'}" style="font-size: 0.7rem;">${time}</div>
            </div>
        `;
        
        $chatWindow.append(html);
        $chatWindow.scrollTop($chatWindow[0].scrollHeight);
    }

    function sendMessage() {
        const message = $userInput.val().trim();
        if (message === '') return;

        // Append user message
        appendMessage('user', message);
        $userInput.val('');

        // Simulate AI Thinking
        const $loader = $('<div class="chat-msg bot-msg mb-4" id="botLoader"><div class="msg-bubble shadow-sm p-3 rounded-4 bg-white border"><div class="listening-indicator"><span></span><span></span><span></span></div></div></div>');
        $chatWindow.append($loader);
        $chatWindow.scrollTop($chatWindow[0].scrollHeight);

        // Call API
        $.ajax({
            url: 'api/chat.php',
            method: 'POST',
            data: { message: message },
            dataType: 'json',
            success: function(response) {
                $('#botLoader').remove();
                appendMessage('bot', response.reply);
                speak(response.reply);
            },
            error: function() {
                $('#botLoader').remove();
                appendMessage('bot', 'Sorry, I encountered an error processing your request.');
            }
        });
    }

    // Event Listeners
    $sendBtn.on('click', sendMessage);

    $userInput.on('keypress', function(e) {
        if (e.which === 13) {
            sendMessage();
        }
    });

    $voiceBtn.on('click', function() {
        if (isListening) {
            stopListening();
        } else {
            recognition.start();
        }
    });

    $clearChat.on('click', function() {
        if (confirm('Are you sure you want to clear the conversation?')) {
            $chatWindow.empty();
            appendMessage('bot', 'Conversation cleared. How can I help you?');
        }
    });
});
