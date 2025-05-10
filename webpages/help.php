<?php
 session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Help</title>
  <base href="/Dc_Characters/">
  <link rel="icon" type="image/png" href="images/dc_logo.png">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/navbar.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/responsive.css">
  <link rel="stylesheet" href="css/modal.css">
  <link rel="stylesheet" href="css/help.css"> </head>

<body>
  <div class="container">
    <?php include 'navbar.php'; ?>
    <main class="help-main">
      <h1>How Can We Help You?</h1>
      <p>If you have any questions or need assistance, feel free to reach out to our chatbot by clicking the icon below.</p>

      <div class="faq-container">
        <h2>Frequently Asked Questions</h2>
        <div class="faq-item">
          <h3>What is DC Comics?</h3>
          <p>DC Comics is a comic book publisher known for its iconic characters like Superman, Batman, and Wonder Woman.</p>
        </div>
        <div class="faq-item">
          <h3>How can I contact customer support?</h3>
          <p>You can reach out to us through the chatbot or email us at <a href="webpages/contact.php" style="text-decoration: none; color: #0056b3;">https://support.dcuniverse.com/hc/en-us​..</a>.</p>
        </div>
        <div class="faq-item">
          <h3>Where can I find more information about DC characters?</h3>
          <p>Visit our character profiles section for detailed information about your favorite heroes and villains.</p>
        </div>
        <div class="faq-item">
          <h3>Are there any upcoming events?</h3>
          <p>Stay tuned to our news section for updates on upcoming events and releases!</p>
        </div>
      </div>
    </main>

    <div class="chatbot-icon" onclick="toggleChatbot()">
      <img src="images/chatbot.png" alt="Chatbot Icon" />
    </div>

    <div class="chatbot-popup" id="chatbotPopup">
      <div class="chatbot-header">
        <h3 id="chatbotTitle">Chat with The Flash!</h3>
        <span class="close-btn" onclick="toggleChatbot()">×</span>
      </div>

        <div class="character-select-container">
            <label for="characterSelect">Talk to:</label>
            <select id="characterSelect">
                <option value="flash">The Flash</option>
                <option value="batman">Batman</option>
                <option value="superman">Superman</option>
                <option value="wonderwoman">Wonder Woman</option>
                <option value="joker">The Joker</option>
            </select>
        </div>

      <div class="chatbot-messages" id="chatbotMessages">
        </div>
      <div class="send-input">
        <input type="text" class="chatbot-input" placeholder="Type your message here..." />
        <button class="chatbot-send">Send</button>
      </div>

    </div>
  </div>
  <?php include 'footer.php' ?>

  <div id="authModal" class="modal">
    <div class="modal-content">
        <span id="closeModal" class="close">&times;</span>
        <h2 id="modalTitle">Log In</h2>
        <div id="modalContent"></div>
    </div>
  </div>

  <script>
    const sendButton = document.querySelector('.chatbot-send');
    const inputField = document.querySelector('.chatbot-input');
    const messagesContainer = document.getElementById('chatbotMessages'); // Use ID for clarity
    const chatbotTitle = document.getElementById('chatbotTitle');
    const characterSelect = document.getElementById('characterSelect');

    const API_KEY = "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"; 

    // Mapping of character values to their prompts, initial messages, and image paths
    const characters = {
        'flash': {
            prompt: "Answer briefly and act like The Flash of DC. Be fast-paced and slightly impatient.",
            initialMessage: "Whoa there! Need help in a flash? Ask away!",
            image: 'images/img9.jpg'  
        },
        'batman': {
            prompt: "Answer briefly and act like Batman of DC. Be serious, direct, and a little gruff.",
            initialMessage: "Speak. What do you need?",
            image: 'images/img6.jpg'  
        },
        'superman': {
            prompt: "Answer briefly and act like Superman of DC. Be hopeful, kind, and inspiring.",
            initialMessage: "Hello there. I'm here to help if I can.",
            image: 'images/img5.jpg'  
        },
        'wonderwoman': {
            prompt: "Answer briefly and act like Wonder Woman of DC. Be wise, compassionate, and strong.",
            initialMessage: "Greetings. How may I lend assistance?",
            image: 'images/img7.jpg'  
        },
        'joker': {
            prompt: "Answer briefly and act like The Joker of DC. Be chaotic, unpredictable, and make jokes. Don't be helpful.", // Note: Joker is for fun, maybe not helpful
            initialMessage: "Why so serious? Ready for some laughs?",
            image: 'images/joker.png'  
        }
    };

    let currentCharacter = 'flash'; // Default character

    // Function to update chatbot based on selected character
    function updateChatbotCharacter() {
        currentCharacter = characterSelect.value;
        const charData = characters[currentCharacter];

        chatbotTitle.textContent = `Chat with ${characterSelect.options[characterSelect.selectedIndex].text}!`;

        // Clear previous messages
        messagesContainer.innerHTML = '';

        // Add the new initial message with profile picture
        appendBotMessage(charData.initialMessage, charData.image);

    }

    // Function to append a bot message with a profile picture
    function appendBotMessage(text, imageUrl) {
         const botMessageWrapper = document.createElement('div');
         botMessageWrapper.classList.add('message', 'bot-message');

         const profilePic = document.createElement('img');
         profilePic.classList.add('bot-profile-pic');
         profilePic.src = imageUrl;
         profilePic.alt = 'Bot Profile Picture';

         const messageContent = document.createElement('div');
         messageContent.classList.add('message-content');
         messageContent.innerHTML = text; // Use innerHTML to allow bold/italic formatting

         botMessageWrapper.appendChild(profilePic);
         botMessageWrapper.appendChild(messageContent);
         messagesContainer.appendChild(botMessageWrapper);

         // Scroll to the latest message
         messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

     // Function to append a user message
    function appendUserMessage(text) {
         const userMessageWrapper = document.createElement('div');
         userMessageWrapper.classList.add('message', 'user-message');

         const messageContent = document.createElement('div');
         messageContent.classList.add('message-content');
         messageContent.textContent = text;

         userMessageWrapper.appendChild(messageContent);
         messagesContainer.appendChild(userMessageWrapper);

         // Scroll to the latest message
         messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }


    // Event listener for character selection change
    characterSelect.addEventListener('change', updateChatbotCharacter);

    // Initial setup when the page loads
    document.addEventListener('DOMContentLoaded', () => {
        updateChatbotCharacter(); // Set up the chatbot with the default character
    });


    sendButton.addEventListener('click', async () => {
        const userMessage = inputField.value;
        if (userMessage.trim()) { // Use trim() to prevent sending empty messages
            // Display user message
            appendUserMessage(userMessage);
            inputField.value = '';


            // Show bot is typing (append a temporary message element)
            const typingMessageElement = document.createElement('div');
            typingMessageElement.classList.add('message', 'bot-message');
             // Create the inner HTML structure for the typing indicator with image and text
            typingMessageElement.innerHTML = `
                <img src="${characters[currentCharacter].image}" alt="Bot Profile Picture" class="bot-profile-pic">
                <div class="message-content">Typing...</div>
            `;
            messagesContainer.appendChild(typingMessageElement);

             // Scroll to show "Typing..."
             messagesContainer.scrollTop = messagesContainer.scrollHeight;

            try {
                const charData = characters[currentCharacter];
                 // Fetch AI response from OpenRouter
                 const response = await fetch("https://openrouter.ai/api/v1/chat/completions", {
                     method: "POST",
                     headers: {
                         "Content-Type": "application/json",
                         "Authorization": `Bearer ${API_KEY}`
                     },
                     body: JSON.stringify({
                         model: "meta-llama/llama-4-maverick:free", // Or other models if needed
                         messages: [{ role: "user", content: `${charData.prompt} Message: ${userMessage}` }] // Use the dynamic prompt
                     })
                 });

                 // Check for API errors before processing response
                 if (!response.ok) {
                     const errorBody = await response.text(); // Get error body for more info
                     throw new Error(`API Error: ${response.status} - ${response.statusText} - ${errorBody}`);
                 }

                 const data = await response.json();
                 let aiReply = data.choices?.[0]?.message?.content || "Error: No response from AI.";

                 // Convert markdown to HTML (bold and italics)
                 aiReply = aiReply.replace(/\*(.*?)\*/g, "<b>$1</b>"); // *text*
                 aiReply = aiReply.replace(/\*\*(.*?)\*\*/g, "<b>$1</b>"); // **text**
                 aiReply = aiReply.replace(/_(.*?)_/g, "<i>$1</i>"); // _text_


                 // Remove the "Typing..." message
                 messagesContainer.removeChild(typingMessageElement);

                 // Add the actual bot response
                 appendBotMessage(aiReply, charData.image);


             } catch (error) {
                 console.error("API Error:", error); // Log error details in the console
                 // Remove typing indicator and show error message
                 if(messagesContainer.contains(typingMessageElement)) { // Check if the typing element still exists before trying to remove
                     messagesContainer.removeChild(typingMessageElement);
                 }
                 appendBotMessage("Error connecting to AI.", characters[currentCharacter].image); // Show error with current character's pic
             }

            // Scroll to the latest message after response (handled within appendBotMessage)
        }
    });

    // Allow sending message with Enter key
    inputField.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent default form submission
            sendButton.click(); // Trigger the send button click
        }
    });


    // Function to toggle the chatbot popup
    function toggleChatbot() {
        const chatbotPopup = document.getElementById('chatbotPopup');
        chatbotPopup.classList.toggle('active'); // Toggle the active class to show/hide the popup
         // If showing the chatbot, ensure the character selection is set up and scroll to bottom
        if(chatbotPopup.classList.contains('active')) {
             updateChatbotCharacter(); // Initialize or re-initialize chat based on selection
             // Scroll to bottom is handled by appendBotMessage
        }
    }

</script>
<script src="script/script.js">
</script>

</body>

</html>