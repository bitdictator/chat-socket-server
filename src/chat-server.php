<?php

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Loop;
use React\Socket\SocketServer;
use Core\Config;
use Core\App;

require __DIR__ . '../vendor/autoload.php';

date_default_timezone_set(Config::get('TIMEZONE'));

$loop = Loop::get();

$context = [
    'tls' => [
        'local_cert'  => __DIR__ . '../letsencrypt/fullchain1.pem',
        'local_pk' => __DIR__ . '../letsencrypt/privkey1.pem',
        'verify_peer' => false
    ]
];

$server = new SocketServer(Config::get('SERVER_URI'), $context, $loop);

$httpServer = new HttpServer(
    new WsServer(
        new App()
    )
);

$ioServer = new IoServer($httpServer, $server, $loop);

$ioServer->run();
