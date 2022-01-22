'use strict';

const message       = document.querySelector('#message');
const messagesList  = document.querySelector('#messagesList');

const socket = new WebSocket('ws://192.168.1.42:8090/server/server.php');

socket.addEventListener('open',     ev => console.log('[WebSocket] Connected'));
socket.addEventListener('close',    ev => console.log('[WebSocket] Disconnected'));
socket.addEventListener('error',    ev => console.log('[WebSocket] Something went wrong'))
socket.addEventListener('message',  ev => sendMessage(ev.data) );

function sendMessage(data)
{

    const msg = JSON.parse(data)['message'];

    const messageDIV = document.createElement('div');
    messageDIV.className = 'messages__message';
    messageDIV.innerHTML = msg;

    messagesList.append(messageDIV);

}

document.forms.chat.addEventListener('submit', ev => {
    ev.preventDefault();;
    socket.send(JSON.stringify({ chat_message: message.value, chat_user: '' }))
    message.value = '';
});