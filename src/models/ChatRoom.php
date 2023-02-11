<?php

namespace Models;

use Ratchet\ConnectionInterface;
use Core\Database;

class ChatRoom
{
    private \SplObjectStorage $clients;

    public function __construct(private int $chat_room_id, Client $first_client)
    {
        $this->clients = new \SplObjectStorage;
        $this->clients->attach($first_client);
    }

    public function getId(): int
    {
        return $this->chat_room_id;
    }

    public function sendMessage(ConnectionInterface $from_conn, $text): bool
    {
        $user_id = $this->getClientIdFromConnection($from_conn);

        if ($user_id === false) {
            return false;
        }

        $text = trim($text);

        // send message with chat_id and user_id to bimboom database
        Database::insert('chat_line', ['chat_id' => $this->chat_room_id, 'sender_user_id' => $user_id, 'message' => $text]);

        // send message to all clients connected to this chat room
        foreach ($this->clients as $client) {
            if ($from_conn !== $client->getConnection()) {   // send to others, not self
                $client->sendMessage(json_encode([
                    'text' => htmlspecialchars($text),
                    'sent_at' => date('Y-m-d H:i:s')
                ]));
            }
        }

        return true;
    }

    public function addClient(Client $client_to_add): bool
    {
        $this->clients->attach($client_to_add);
        return true;
    }

    public function getClientIdFromConnection(ConnectionInterface $conn): int|false
    {
        foreach ($this->clients as $client) {
            if ($conn === $client->getConnection()) {
                return $client->getUserId();
            }
        }

        return false;
    }

    public function getClientIDs(): array
    {

        $client_ids = [];

        $count = 0;
        foreach ($this->clients as $client) {
            $client_ids[$count] = $client->getUserId();
            $count++;
        }

        return $client_ids;
    }

    public function hasClient(Client $client): bool
    {
        return $this->clients->contains($client);
    }

    public function hasClientWithConnection(ConnectionInterface $conn): bool
    {
        foreach ($this->clients as $client) {
            if ($client->getConnection() === $conn) {   // find if this room has the client with the given conneciton
                return true;
            }
        }

        return false;
    }

    public function removeClient(Client $client): bool
    {
        $this->clients->detach($client);
        return true;
    }

    public function removeClientWithConnection(ConnectionInterface $conn): bool
    {
        // find and delete the client with given connection
        foreach ($this->clients as $client) {
            if ($client->getConnection() === $conn) {
                $this->clients->detach($client);
                return true;
            }
        }

        return true;
    }

    public function getClientsCount(): int
    {
        return $this->clients->count();
    }
}
