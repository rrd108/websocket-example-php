<?php

namespace Rrd108\WsPhp;

use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

require dirname(__DIR__) . '/vendor/autoload.php';

class BiddingService implements MessageComponentInterface
{
    protected $clients;
    protected $lastBid;

    public function __construct()
    {
        echo "Starting BiddingService...\n";
        $this->clients = new \SplObjectStorage;
        $this->lastBid = $this->getLastBid();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        echo "New connection opened {$conn->resourceId}\n";
        $this->clients->attach($conn);
        $conn->send(json_encode(['type' => 'lastBid', 'data' => $this->lastBid]));
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        echo "Message received from {$from->resourceId}: $msg\n";

        echo '🆘 TODO: SECURITY check if the message is a valid bid';      // TODO 
        echo '⚠️ TODO check if the bid is bigger then $this->lastBid[bid] and refuse if it is not'; // TODO 

        $this->saveBid($from->resourceId, $msg);

        foreach ($this->clients as $client) {
            if ($from != $client) {
                $client->send(json_encode(['type' => 'lastBid', 'data' => $this->lastBid]));
                echo "Message sent to client {$client->resourceId}\n";
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection with {$conn->resourceId} closed\n";
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        echo "An error has occured: {$e->getMessage()}\n";
        $conn->close();
    }

    private function getLastBid()
    {
        echo "Getting last bid\n";
        $bidsFile = dirname(__DIR__) . '/bids.txt';
        if (file_exists($bidsFile)) {
            $lines = file($bidsFile);
            $lasLine = array_pop($lines);
            list($timestamp, $bidder, $bid) = explode(';', $lasLine);
            echo "Last bid: $bid\n";
            return ['timestamp' => $timestamp, 'bidder' => $bidder, 'bid' => $bid];
        }
    }

    private function saveBid($from, $bid)
    {
        $timestamp = time();
        $data = "\n$timestamp;$from;$bid";
        file_put_contents(dirname(__DIR__) . '/bids.txt', $data, FILE_APPEND);
        $this->lastBid = ['timestamp' => $timestamp, 'bidder' => $from, 'bid' => $bid];
    }
}
