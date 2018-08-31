<?php

class socket{
    public $master;
    public $users = array();
    public $sockets = array();

    protected $port = '';
    protected $address = '';
    protected $secure_key = '';

    const R_N = "\r\n";
    const SOCKET_KEY = "/Sec-WebSocket-Key: (.*)\r\n/";
    const MAGIC_KEY = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';

    function __construct($address, $secure_key = ''){

        list($address, $port) = explode(':', $address);

        if (preg_match('/^[\d\.]*$/is', $address)) {
            $this->address = long2ip(ip2long($address));
        } else {
            $this->address = $address;
        }

        if (is_numeric($port) && intval($port) > 1024 && intval($port) < 65536) {
            $this->port = $port;
        } else {
            $message = 'invalid port: ' . $port;
            exit($message);
        }

        $this->createSocket();
        $this->secure_key = $secure_key;
        array_push($this->sockets, $this->master);
    }

    function run(){
        while (true) {
            $write = null;
            $except = null;
            $sockets = $this->sockets;
            socket_select($sockets, $write, $except, null);
            foreach ($sockets as $socket) {
                if ($socket == $this->master) {
                    $client = socket_accept($this->master);
                    if ($client < 0) {
                        continue;
                    } else {
                        array_push($this->users, (object)['socket' => $client,'handShake' => false]);
                        array_push($this->sockets, $client);
                    }
                } else {
                    $bytes = socket_recv($socket, $buffer, 2048, 0);
                    if ($bytes == 0) {
                        $this->disconnect($socket);
                    } else {
                        $user = $this->searchUser($socket);
                        if ($user->handShake) {
                            $message = $this->unwrap($user->socket, $buffer);
                            if (strpos($message, 'token:') === 0) {
                                $token = str_replace('token:', '', $message);
                                if (strpos($token, ':')) {
                                    list($user->id, $user->group) = explode(':', $token);
                                } else {
                                    $user->id = $token;
                                }
                            } else {
                                //Notice 双方互发
                            }
                        } else {
                            $this->doHandShake($user, $buffer);
                        }
                    }
                }
            }
        }
    }

    protected function send($user, $message){
        $message = self::wrap($message);
        socket_write($user->socket, $message, strlen($message));
    }

    private function createSocket(){
        $this->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $result = socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1);
        $result += socket_bind($this->master, $this->address, $this->port);
        $result += socket_listen($this->master, 64);
        if (!$this->master || $result < 3) {
            exit('create socket fail');
        }
    }

    private function doHandShake($user, $buffer){
        $key = preg_match(self::SOCKET_KEY, $buffer, $match) ? $match[1] : '';
        $acceptKey = base64_encode(sha1($key . self::MAGIC_KEY, true));
        $upgrade = 'HTTP/1.1 101 Switching Protocol' . self::R_N;
        $upgrade .= 'Upgrade: websocket' . self::R_N;
        $upgrade .= 'Connection: Upgrade' . self::R_N;
        $upgrade .= 'Sec-WebSocket-Accept: ' . $acceptKey . self::R_N . self::R_N;
        socket_write($user->socket, $upgrade, strlen($upgrade));
        $user->id = $acceptKey;
        $user->handShake = true;

        //服务端推送数据
        if (strpos($buffer, 'secure_key')) {
            $this->pushData($buffer);
        }
        return true;
    }

    private function pushData($buffer){
        $message = json_decode($buffer, true);
        if (isset($message['secure_key']) && $message['secure_key'] === $this->secure_key) {
            if (isset($message['mode'])) {
                if ($message['mode'] === 'group') {
                    foreach ($this->users as $client) {
                        if (isset($client->group) && isset($message[$client->group])) {
                            $msg = json_encode($message[$client->group]);
                            $this->send($client, $msg);
                        }
                    }
                } elseif ($message['mode'] === 'user') {
                    foreach ($this->users as $client) {
                        if (isset($client->id) && isset($message[$client->id])) {
                            $msg = json_encode($message[$client->id]);
                            $this->send($client, $msg);
                        }
                    }
                }
            } else {
                foreach ($this->users as $client) {
                    $msg = json_encode($message[0]);
                    $this->send($client, $msg);
                }
            }
        }
    }

    function disconnect($clientSocket){
        $found = null;
        $n = count($this->users);
        for ($i = 0; $i < $n; $i++) {
            if ($this->users[$i]->socket == $clientSocket) {
                $found = $i;
                break;
            }
        }
        $index = array_search($clientSocket, $this->sockets);
        if (!is_null($found)) {
            array_splice($this->users, $found, 1);
            array_splice($this->sockets, $index, 1);
            socket_close($clientSocket);
        }
    }

    private function searchUser($socket){
        foreach ($this->users as $user) {
            if ($user->socket == $socket) {
                return $user;
            }
        }
    }

    static function wrap($message = '', $opCode = 0x1){
        $firstByte = 0x80 | $opCode;
        $encodeData = null;
        $len = strlen($message);
        if (0 <= $len && $len <= 125) {
            $encodeData = chr(0x81) . chr($len) . $message;
        } else {
            if (126 <= $len && $len <= 0xFFFF) {
                $low = $len & 0x00FF;
                $high = ($len & 0xFF00) >> 8;
                $encodeData = chr($firstByte) . chr(0x7E) . chr($high) . chr($low) . $message;
            }
        }
        return $encodeData;
    }

    protected function unwrap($clientSocket, $message = ''){
        $maskKey = $oriData = $decodeData = '';
        $opCode = ord(substr($message, 0, 1)) & 0x0F;
        $payLoadLen = ord(substr($message, 1, 1)) & 0x7F;
        $isMask = (ord(substr($message, 1, 1)) & 0x80) >> 7;

        if ($isMask != 1 || $opCode == 0x8) {
            $this->disconnect($clientSocket);
            return null;
        }

        if ($payLoadLen <= 125 && $payLoadLen >= 0) {
            $maskKey = substr($message, 2, 4);
            $oriData = substr($message, 6);
        } else {
            if ($payLoadLen == 126) {
                $maskKey = substr($message, 4, 4);
                $oriData = substr($message, 8);
            } else {
                if ($payLoadLen == 127) {
                    $maskKey = substr($message, 10, 4);
                    $oriData = substr($message, 14);
                }
            }
        }
        $len = strlen($oriData);
        for ($i = 0; $i < $len; $i++) {
            $decodeData .= $oriData[$i] ^ $maskKey[$i % 4];
        }
        return $decodeData;
    }

    static function log($message = ''){
        file_put_contents('log/topic/websocket.log', $message . PHP_EOL, FILE_APPEND);
    }

}