<?php
/**
 * This file is part of the PocketIO package
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

use PocketIO\Client;
use PocketIO\Engine\SocketIO\SocketIO;

require __DIR__ . '/../../../vendor/autoload.php';

$client = new Client(new SocketIO('http://localhost:1337', [
    'headers' => [
        'X-My-Header: websocket rocks',
        'Authorization: Bearer 12b3c4d5e6f7g8h9i'
    ]
]));

$client->initialize();
$client->emit('broadcast', ['foo' => 'bar']);
$client->close();
