<?php declare(strict_types=1);
/**
 * This file is part of the PocketIO package
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace PocketIO;

use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use PocketIO\Exception\SocketException;

/**
 * Represents the IO Client which will send and receive the requests to the
 * websocket server.
 */
class Client
{
    /** @var EngineInterface */
    private EngineInterface $engine;

    /** @var LoggerInterface */
    private $logger;

    private bool $isConnected = false;

    /**
     * @param EngineInterface $engine
     * @param LoggerInterface|null $logger
     */
    public function __construct(EngineInterface $engine, LoggerInterface $logger = null)
    {
        $this->engine = $engine;
        $this->logger = $logger ?: new NullLogger;
    }

    public function __destruct()
    {
        if (!$this->isConnected) {
            return;
        }

        $this->close();
    }

    /**
     * Connects to the websocket
     *
     * @return $this
     */
    public function initialize(): Client
    {
        try {
            $this->logger->debug('Connecting to the websocket');
            $this->engine->connect();
            $this->logger->debug('Connected to the server');

            $this->isConnected = true;
        } catch (SocketException $e) {
            $this->logger->error('Could not connect to the server', ['exception' => $e]);

            throw $e;
        }

        return $this;
    }

    /**
     * Reads a message from the socket
     *
     * @return string Message read from the socket
     */
    public function read(): string
    {
        $this->logger->debug('Reading a new message from the socket');
        return $this->engine->read();
    }

    /**
     * Emits a message through the engine
     *
     * @param string $event
     * @param array $args
     *
     * @return $this
     */
    public function emit(string $event, array $args): Client
    {
        $this->logger->debug('Sending a new message', ['event' => $event, 'args' => $args]);
        $this->engine->emit($event, $args);

        return $this;
    }

    /**
     * Sets the namespace for the next messages
     *
     * @param string namespace the name of the namespace
     * @return $this
     */
    public function of($namespace): Client
    {
        $this->logger->debug('Setting the namespace', ['namespace' => $namespace]);
        $this->engine->of($namespace);

        return $this;
    }

    /**
     * Closes the connection
     *
     * @return $this
     */
    public function close(): Client
    {
        $this->logger->debug('Closing the connection to the websocket');
        $this->engine->close();

        $this->isConnected = false;

        return $this;
    }

    /**
     * Gets the engine used, for more advanced functions
     *
     * @return EngineInterface
     */
    public function getEngine(): EngineInterface
    {
        return $this->engine;
    }
}