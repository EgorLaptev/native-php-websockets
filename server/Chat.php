<?php

class Chat
{

    public function sendHeaders($client_headers, $received_socket, $host, $port = 8080)
    {

        $received_headers   = [];

        foreach ( explode(PHP_EOL, $client_headers) as $header_line ) {

            if (count(explode(':', $header_line)) === 2) {

                $key   = trim(explode(':', $header_line)[0]);
                $value = trim(explode(':', $header_line)[1]);

                $received_headers[$key] = $value;

            }

        }

        $received_ws_key = $received_headers['Sec-WebSocket-Key'];
        $ws_key_const    = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
        $server_ws_key   = base64_encode(pack('H*', sha1($received_ws_key.$ws_key_const)));

        $sending_headers =
            "HTTP/1.1 101 Switching Protocols\r\n".
            "Upgrade: websocket\r\n".
            "Connection: Upgrade\r\n".
            "WebSocket-Origin: $host\r\n".
            "WebSocket-Location: ws://$host:$port/server/server.php\r\n".
            "Sec-WebSocket-Accept: $server_ws_key\r\n\r\n";

        socket_write($received_socket, $sending_headers, strlen($sending_headers));

    }

    public function newConnectionACK($client_id_address)
    {

        $message = "New client $client_id_address connected";
        $message_arr = [
            "message" => $message,
            "type" => "newConnectionACK"
        ];

        return $this->seal(json_encode($message_arr));

    }

    public function send($data, $client_socket_arr)
    {

        $data_length = strlen($data);

        foreach ($client_socket_arr as $client_socket) {
            @socket_write($client_socket, $data, $data_length);
        }

        return true;

    }

    public function seal($socket_str)
    {

        $b1 = 0x81;
        $length = strlen($socket_str);
        $header = "";

        if ($length <= 125) $header = pack('CC', $b1, $length);
        else if ($length > 125 && $length < 65536) $header = pack('CCn', $b1, 126, $length);
        else if ($length >= 65536) $header = pack('CCNN', $b1, 127, $length);

        return $header.$socket_str;

    }


    public function unseal($socketData)
    {
        $length = ord($socketData[1]) & 127;

        if ($length == 126) {
            $mask = substr($socketData, 4, 4);
            $data = substr($socketData, 8);
        } else if ($length == 127) {
            $mask = substr($socketData, 10, 4);
            $data = substr($socketData, 14);
        } else {
            $mask = substr($socketData, 2, 4);
            $data = substr($socketData, 6);
        }

        $socketStr = "";

        for ($i=0;$i<strlen($data);$i++)
            $socketStr .= $data[$i] ^ $mask[$i%4];

        return $socketStr;

    }

    public function create_chat_message($user_name, $message_str)
    {

        $message_arr = [
            'message' => $message_str
        ];

        return $this->seal(json_encode($message_arr));

    }

}