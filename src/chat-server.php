<?php

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Loop;
use React\Socket\SocketServer;
use Core\Config;
use Core\App;

require dirname(__DIR__) . '/vendor/autoload.php';

$loop = Loop::get();
$wsServer = new WsServer(new App());
$wsServer->enableKeepAlive($loop, 10);
$app = new HttpServer($wsServer);
$secureSocketServer = new SocketServer(Config::get('SERVER_URI'), [
    'tls' => [
        'local_cert' => '/etc/letsencrypt/live/chat.bimboom.ru/fullchain.pem',
        'local_pk' => '/etc/letsencrypt/live/chat.bimboom.ru/privkey.pem',
        'allow_self_signed' => false,
        'verify_peer' => FALSE
    ]
], $loop);

$wss = new IoServer($app, $secureSocketServer, $loop);
$wss->run();
