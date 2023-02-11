<?php

namespace Models;

use Ratchet\ConnectionInterface;

class Client
{
    public function __construct(private ConnectionInterface $conn, private int $user_id)
    {
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->conn;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function sendMessage(string $text)
    {
        $this->conn->send($text);
    }
}
