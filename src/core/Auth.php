<?php

namespace Core;

use Core\AESCipher;
use Ratchet\ConnectionInterface;

class Auth
{

    /**
     * Authentiates the connection of the user who is tryin to join
     * 
     * @param   ConnectionInterface $conn   The connection of the client with auth details in query parameter
     * 
     * @return  array|false                 Decrypted details of the authentication such as the user id and chat id of the record in the database
     */
    public static function connection(ConnectionInterface $conn): array|false
    {
        // TODO: get the self signed auth cookie set by your website and authenticate using that
        // $cookiesRaw = $conn->httpRequest->getHeader('Cookie');
        // if (empty($cookiesRaw)) {
        //     $cookiesArr = \GuzzleHttp\Psr7\parse_header($cookiesRaw)[0];
        // }

        // get the query parameters
        $query_string = $conn->httpRequest->getUri()->getQuery();

        if (empty($query_string)) {
            $conn->close();
            Console::out("No query given.", Console::COLOR_RED);
            return false;
        }

        parse_str($query_string, $query_array);

        // validate authentication details
        if (empty($query_array['auth_details'])) {
            $conn->close();
            Console::out("No auth details given.", Console::COLOR_RED);
            return false;
        }

        $cipher = new AESCipher(Config::get('CHAT_SOCKET_SERVER_CIPHER_KEY'));

        $auth_details_decrypted = $cipher->decrypt(base64_decode($query_array['auth_details']));

        // json to object
        $auth_details = json_decode($auth_details_decrypted);

        if (!$auth_details) {
            $conn->close();
            Console::out("Auth details is invalid json.", Console::COLOR_RED);
            return false;
        }

        // in the auth details we expect user_id, chat_id and auth_token
        if (empty($auth_details->user_id) || empty($auth_details->chat_id) || empty($auth_details->auth_token)) {
            $conn->close();
            Console::out("Not all details were given.", Console::COLOR_RED);
            return false;
        }

        $user_id = $auth_details->user_id;
        $chat_id = $auth_details->chat_id;
        $auth_token = $auth_details->auth_token;

        // auth token must be 32 chars length
        if (strlen($auth_token) !== 32) {
            $conn->close();
            Console::out("Auth token invalid.", Console::COLOR_RED);
            return false;
        }

        // TODO: self authenticate without the need of the website
        // authenticate with token
        if (!Database::row("SELECT id FROM chat_auth_token WHERE token=? AND used=0", [$auth_token])) {
            $conn->close();
            Console::out("Token is used.", Console::COLOR_RED);
            return false;
        }

        // set token to used
        Database::update('chat_auth_token', ['used' => 1], ['token' => $auth_token]);

        return ['user_id' => $user_id, 'chat_id' => $chat_id];
    }
}
