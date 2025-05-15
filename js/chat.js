const socket = io('http://localhost:3000'); 
function sendMessage() {
    const input = document.getElementById('messageInput');
    const text = input.value.trim();
    if (text === '') return;

    const msgBox = document.createElement('div');
    msgBox.classList.add('message');
    msgBox.textContent = text;

    document.getElementById('chatMessages').appendChild(msgBox);
    input.value = '';
  }
function sendMessage() {
    const message = document.getElementById('messageInput').value;
    socket.emit('chat message', message);
}

socket.on('chat message', function(msg) {
    const chatBox = document.getElementById('chatBox');
    const newMessage = document.createElement('div');
    newMessage.textContent = msg;
    chatBox.appendChild(newMessage);
});