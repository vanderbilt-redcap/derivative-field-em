$( document ).ready(function() {
    insertButton(targetField, buttonHTML);
});
function populateResponse() {
    $.ajax({
        method: 'POST',
        url: ajax_url,
        data: { action: "process"},
        dataType: 'json'
    })
    .done(function(data) {
        if (data.status != 1) {
            alert("Something went wrong!");
        } else {
            //typeWriterEffect(chatElement.querySelector("p"), data.message, 5); // Type into 'myDiv' with 50ms delay per character
            $("input[name='"+targetField+"']").val(data.message);
        }
    })
    .fail(function(data) {

    })
    .always(function(data) {

    });
}

function handleChat(chatInput = '', chatbox = '', setupNum = '') {
    if (chatInput == '') {
        chatInput = $(".chat-input textarea");
        $("#send-btn").css("color", "#888");
    } else {
        chatInput.next('span').css("color", "#888");
    }
    if (chatbox == '') {
        chatbox = $(".chatbox");
    }
    userMessage = chatInput.val().trim(); // Get user entered message and remove extra whitespace
    if (!userMessage) return;

    // Clear the input textarea and set its height to default
    chatInput.val("");

    chatInput.height('auto');

    // Append the user's message to the chatbox
    chatbox.append(createChatLi(userMessage, "outgoing"));
    chatbox.scrollTop(chatbox[0].scrollHeight);

    // Display "Thinking..." message while waiting for the response
    var generateText = '<img alt="Generating..." src="' + app_path_images + 'progress_circle.gif">&nbsp; Generating, Please wait...';
    const incomingChatLi = createChatLi(generateText, "incoming");
    chatbox.append(incomingChatLi);
    chatbox.scrollTop(chatbox[0].scrollHeight);
    generateResponse(incomingChatLi, setupNum);
}

function insertButton(targetField, buttonHTML) {
    if ($('tr#'+targetField+'-tr').length > 0) { // Execute this script only if form contain that field
        if ($('tr#'+targetField+'-tr').find('td:first-child div:first').length > 0) {
            $('tr#'+targetField+'-tr').find('td:first-child div:first').append(buttonHTML);
        } else {
            $('tr#'+targetField+'-tr').find('td:nth-child(2) div:first').append(buttonHTML);
        }
    }
}
function askQuestion(name, setupNum) {
    handleChat($('tr#'+name+'-tr').find("#rc-user-input"), $('tr#'+name+'-tr').find(".rc-chatbox"), setupNum);
}
