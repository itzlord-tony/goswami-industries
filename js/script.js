document.addEventListener('DOMContentLoaded', () => {
    // Loader
    const loader = document.getElementById('loader');
    if (loader) {
        setTimeout(() => {
            loader.style.opacity = '0';
            setTimeout(() => loader.style.display = 'none', 500);
        }, 500);
    }

    // Mobile Menu
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    }

    // Voice Search
    const micBtn = document.querySelector('.mic-btn');
    const searchInput = document.querySelector('.search-input');
    
    if (micBtn && searchInput) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (SpeechRecognition) {
            const recognition = new SpeechRecognition();
            recognition.continuous = false;
            recognition.lang = 'en-US';

            micBtn.addEventListener('click', (e) => {
                e.preventDefault();
                micBtn.classList.add('mic-active');
                recognition.start();
            });

            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                searchInput.value = transcript;
                micBtn.classList.remove('mic-active');
                searchInput.closest('form').submit();
            };

            recognition.onerror = (event) => {
                console.error(event.error);
                micBtn.classList.remove('mic-active');
            };
            
            recognition.onend = () => {
                micBtn.classList.remove('mic-active');
            }
        } else {
            micBtn.style.display = 'none'; // Hide if not supported
        }
    }

    // Chatbot UI
    const chatbotToggler = document.querySelector('.chatbot-toggler');
    const chatbotContainer = document.querySelector('.chatbot-container');
    const chatClose = document.querySelector('.chat-close');
    const chatBody = document.querySelector('.chat-body');
    const chatInput = document.querySelector('.chat-input input');
    const chatSend = document.querySelector('.chat-input button');

    if (chatbotToggler && chatbotContainer) {
        chatbotToggler.addEventListener('click', () => {
            chatbotContainer.classList.add('active');
        });
        chatClose.addEventListener('click', () => {
            chatbotContainer.classList.remove('active');
        });

        const handleChat = () => {
            let userText = chatInput.value.trim();
            if(!userText) return;

            // Append user message
            chatBody.innerHTML += `<div class="chat-msg user-msg">${userText}</div>`;
            chatInput.value = '';
            chatBody.scrollTop = chatBody.scrollHeight;

            // Simple bot logic
            setTimeout(() => {
                let botReply = "I am a simple bot. Contact support for more info.";
                const lowerText = userText.toLowerCase();
                
                if(lowerText.includes('hello') || lowerText.includes('hi')) {
                    botReply = "Hello! Welcome to Goswami Industry. How can I help you today?";
                } else if(lowerText.includes('order') || lowerText.includes('track')) {
                    botReply = "You can track your orders in your account dashboard.";
                } else if(lowerText.includes('return') || lowerText.includes('refund')) {
                    botReply = "We have a 7-day return policy. Please check our terms.";
                }

                chatBody.innerHTML += `<div class="chat-msg bot-msg">${botReply}</div>`;
                chatBody.scrollTop = chatBody.scrollHeight;
            }, 600);
        }

        chatSend.addEventListener('click', handleChat);
        chatInput.addEventListener('keypress', (e) => {
            if(e.key === 'Enter') handleChat();
        });
    }
});
