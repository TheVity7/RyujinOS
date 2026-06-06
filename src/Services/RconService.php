<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Minimal implementation of the Source RCON protocol used by Minecraft
 * servers (Spigot/Paper). Used to deliver purchased products by running
 * console commands such as "lp user {player} parent add vip".
 */
final class RconService
{
    private const SERVERDATA_AUTH = 3;
    private const SERVERDATA_EXECCOMMAND = 2;

    /** @var resource|null */
    private $socket = null;
    private int $requestId = 0;

    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $password,
        private readonly float $timeout = 3.0,
    ) {
    }

    public static function fromConfig(): self
    {
        return new self(
            (string) config('minecraft.rcon_host'),
            (int) config('minecraft.rcon_port'),
            (string) config('minecraft.rcon_password'),
        );
    }

    /**
     * Connect and authenticate. Returns true on success.
     */
    public function connect(): bool
    {
        $errno = 0;
        $errstr = '';
        $socket = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        if ($socket === false) {
            return false;
        }
        stream_set_timeout($socket, (int) $this->timeout);
        $this->socket = $socket;

        $id = $this->send(self::SERVERDATA_AUTH, $this->password);
        $response = $this->read();
        // Auth failure is signalled by request id == -1.
        return $response !== null && $response['id'] === $id && $id !== -1;
    }

    /**
     * Execute a single command and return the server response text.
     */
    public function command(string $command): ?string
    {
        if ($this->socket === null) {
            return null;
        }
        $id = $this->send(self::SERVERDATA_EXECCOMMAND, $command);
        $response = $this->read();
        return $response !== null && $response['id'] === $id ? $response['body'] : null;
    }

    /**
     * Run a list of commands (newline separated string or array). Returns
     * true if every command was dispatched without a socket error.
     *
     * @param string|list<string> $commands
     */
    public function runCommands(string|array $commands, array $placeholders = []): bool
    {
        if (is_string($commands)) {
            $commands = preg_split('/\r\n|\r|\n/', $commands) ?: [];
        }
        $ok = true;
        foreach ($commands as $command) {
            $command = trim($command);
            if ($command === '') {
                continue;
            }
            $command = strtr($command, $placeholders);
            $command = ltrim($command, '/');
            if ($this->command($command) === null) {
                $ok = false;
            }
        }
        return $ok;
    }

    public function disconnect(): void
    {
        if ($this->socket !== null) {
            fclose($this->socket);
            $this->socket = null;
        }
    }

    private function send(int $type, string $body): int
    {
        $id = ++$this->requestId;
        $packet = pack('VV', $id, $type) . $body . "\x00\x00";
        $packet = pack('V', strlen($packet)) . $packet;
        fwrite($this->socket, $packet);
        return $id;
    }

    /**
     * @return array{id:int, type:int, body:string}|null
     */
    private function read(): ?array
    {
        $sizeData = fread($this->socket, 4);
        if ($sizeData === false || strlen($sizeData) < 4) {
            return null;
        }
        $size = unpack('V', $sizeData)[1];
        $data = '';
        while (strlen($data) < $size) {
            $chunk = fread($this->socket, $size - strlen($data));
            if ($chunk === false || $chunk === '') {
                break;
            }
            $data .= $chunk;
        }
        $id = unpack('V', substr($data, 0, 4))[1];
        $type = unpack('V', substr($data, 4, 4))[1];
        // PHP unpack treats V as unsigned; convert auth-failure -1 back.
        if ($id === 0xFFFFFFFF) {
            $id = -1;
        }
        $body = substr($data, 8, -2);
        return ['id' => $id, 'type' => $type, 'body' => $body];
    }
}
