<?php

namespace Models;

use Ratchet\ConnectionInterface;
use Core\Database;

class ChatRoom
{
    private array $clients;

    public function __construct(private int $chat_room_id, Client $first_client)
    {
        $this->clients = [];
        $this->clients[$first_client->getUserId()] = $first_client;
    }

    public function getId(): int
    {
        return $this->chat_room_id;
    }

    /**
     * Sends a message to all clients in the chat room except the sender.
     * 
     * @param ConnectionInterface $from_conn The connection of the sender.
     * @param string $text The message text.
     * @return bool True on success, false on failure.
     */
    public function sendMessage(ConnectionInterface $from_conn, string $text): bool
    {
        $user_id = $this->getClientIdFromConnection($from_conn);

        if ($user_id === false) {
            return false;
        }

        $text = trim($text);

        Database::insert('chat_line', ['chat_id' => $this->chat_room_id, 'sender_user_id' => $user_id, 'message' => $text]);

        foreach ($this->clients as $client_id => $client) {
            if ($client->getConnection() !== $from_conn) {
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
        $this->clients[$client_to_add->getUserId()] = $client_to_add;
        return true;
    }

    public function getClientIdFromConnection(ConnectionInterface $conn): int|false
    {
        foreach ($this->clients as $client_id => $client) {
            if ($conn === $client->getConnection()) {
                return $client_id;
            }
        }

        return false;
    }

    public function getClientIDs(): array
    {
        return array_keys($this->clients);
    }

    public function hasClient(Client $client): bool
    {
        return isset($this->clients[$client->getUserId()]);
    }

    public function hasClientWithConnection(ConnectionInterface $conn): bool
    {
        return $this->getClientIdFromConnection($conn) !== false;
    }

    public function removeClient(Client $client): bool
    {
        unset($this->clients[$client->getUserId()]);
        return true;
    }

    public function removeClientWithConnection(ConnectionInterface $conn): bool
    {
        $user_id = $this->getClientIdFromConnection($conn);

        if ($user_id !== false) {
            unset($this->clients[$user_id]);
            return true;
        }

        return false;
    }

    public function getClientsCount(): int
    {
        return count($this->clients);
    }
}
