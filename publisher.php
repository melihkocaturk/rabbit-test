<?php

require('config.php');
require('vendor/autoload.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$url = parse_url(AMQP_URI);
$vhost = substr($url['path'], 1);

$connection = new AMQPStreamConnection($url['host'], 5672, $url['user'], $url['pass'], $vhost);

$channel = $connection->channel();
$channel->exchange_declare('test_exchange', 'direct', false, false, false);
$channel->queue_declare('test_queue', false, false, false, false);
$channel->queue_bind('test_queue', 'test_exchange', 'test_key');

$msg = new AMQPMessage('Test Message');
$channel->basic_publish($msg, 'test_exchange', 'test_key');

echo " [x] Sent 'Test Message' to test_exchange / test_queue.\n";

$channel->close();
$connection->close();
