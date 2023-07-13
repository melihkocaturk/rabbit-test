<?php

require('config.php');
require('vendor/autoload.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;

$url = parse_url(AMQP_URI);
$vhost = substr($url['path'], 1);

$connection = new AMQPStreamConnection($url['host'], 5672, $url['user'], $url['pass'], $vhost);

$channel = $connection->channel();

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
};

$channel->basic_consume('test_queue', '', false, true, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
