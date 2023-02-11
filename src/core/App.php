<?php

namespace Core;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Models\ChatRoom;
use Models\Client;

class App implements MessageComponentInterface
{
    protected \SplObjectStorage $rooms;
    private Database $db;

    public function __construct()
    {
        $this->rooms = new \SplObjectStorage;
        $this->db = new Database(Config::get('DB_HOST'), Config::get('DB_NAME'), Config::get('DB_USER'), Config::get('DB_PASS'));
    }

    public function onOpen(ConnectionInterface $conn)
    {

        $data = Auth::connection($conn);
        if (!$data) {
            return;
        }

        $user_id = $data['user_id'];
        $chat_id = $data['chat_id'];

        Console::out("New connection: {$conn->resourceId}", Console::COLOR_GREEN);

        // if chat room exists then connect the user to it, if not then create the room
        $found_room = $this->roomExists($chat_id);
        if ($found_room !== false) {
            $found_room->addClient(new Client($conn, $user_id));
            Console::out("Connection {$conn->resourceId} with user_id {$user_id} connected to EXISTING room with chat_id {$chat_id}.");
        } else {    // else create the room
            $this->createRoom($chat_id, new Client($conn, $user_id));
            Console::out("Connection {$conn->resourceId} with user_id {$user_id} connected to NEW room with chat_id {$chat_id}.");
        }

        return;
    }

    public function onMessage(ConnectionInterface $from_conn, $msg)
    {
        if (empty($msg)) {
            return;
        }

        // check if user is authenticated and send connection
        foreach ($this->rooms as $room) {
            if ($room->hasClientWithConnection($from_conn)) {

                $room->sendMessage($from_conn, $msg);
                return;
            }
        }

        return;
    }

    public function onClose(ConnectionInterface $conn)
    {

        // remove client from chat room
        foreach ($this->rooms as $room) {
            $room->removeClientWithConnection($conn);
            // check if chat room empty, if so then remove the chat room
            if ($room->getClientsCount() === 0) {
                $this->rooms->detach($room);
            }
        }

        $conn->close();

        Console::out("Connection {$conn->resourceId} was disconnected", Console::COLOR_RED);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }

    public function createRoom(int $chat_id, Client $first_client)
    {
        $this->rooms->attach(new ChatRoom($chat_id, $first_client));
    }

    public function roomExists(int $chat_id): ChatRoom|false
    {
        foreach ($this->rooms as $room) {
            if ($room->getId() === $chat_id) {
                return $room;
            }
        }

        return false;
    }

    public function deleteRoom(int $chat_id): bool
    {
        foreach ($this->rooms as $room) {
            if ($room->getId() === $chat_id) {
                $this->rooms->detach($room);
                return true;
            }
        }

        return false;
    }
}
