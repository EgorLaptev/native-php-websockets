<?php

require_once 'Chat.php';

define('PORT', 8090);
define('HOST', '192.168.1.42');

$chat = new Chat();

$client_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($client_socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($client_socket, HOST, PORT);
socket_listen($client_socket);

$client_socket_arr = [$client_socket];

while (true) {

    $new_socket_arr = $client_socket_arr;

    $nullA = [];
    $nullB = [];

    socket_select($new_socket_arr, $nullA, $nullB, 0, 10);

    if (in_array($socket, $new_socket_arr)) {
        $received_socket = socket_accept($socket);
        $client_socket_arr[] = $received_socket;

        $header = socket_read($received_socket, 1024);
        $chat->sendHeaders($header, $received_socket, HOST.'/server', PORT);

        socket_getpeername($received_socket, $client_ip_address);
        $connectionASK = $chat->newConnectionACK($client_ip_address);

        $chat->send($connectionASK, $client_socket_arr);

        $new_socket_arr_index = array_search($socket, $new_socket_arr);
        unset($new_socket_arr[$new_socket_arr_index]);

    }

    foreach ($new_socket_arr as $new_socket_resource) {

        while (socket_recv($new_socket_resource, $socket_data, 1024, 0) >= 1) {
            $socket_message = $chat->unseal($socket_data);
            $message_obj = json_decode($socket_message);
            $chat_message = $chat->create_chat_message($message_obj->chat_user, $message_obj->chat_message);
            $chat->send($chat_message, $client_socket_arr);
            break 2;
        }

    }

}

socket_close($socket);