<?php

namespace Mithos\Socket;

class Socket {

    private $_sock;

    public function connect($host = null, $port = null) {
        $this->_sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!$connect = socket_connect($this->_sock, $host, $port)) {
            throw new SocketException('Unable to connect socket ' . $host);
        }
        return $connect;
    }

    public function send($data) {
        if (socket_write($this->_sock, $data) == false) {
            throw new SocketException('Failed to send packet');
        }
    }

    public function read($length = 2048) {
        return socket_read($this->_sock, $length);
    }

    public function kill() {
        socket_close($this->_sock);
    }

    public function __destruct() {
        $this->kill();
    }
}