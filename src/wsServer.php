<?php

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Rrd108\WsPhp\BiddingService;

require dirname(__DIR__) . '/vendor/autoload.php';

$port = 16108;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new BiddingService()
        )
    ),
    $port
);

echo "Webscoket server starting on port {$port}...\n";
$server->run();
