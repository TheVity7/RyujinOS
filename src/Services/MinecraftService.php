<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Queries a Minecraft server using the Server List Ping protocol to read the
 * online player count and version shown on the homepage / footer.
 */
final class MinecraftService
{
    /**
     * @return array{online:bool, players:int, max:int, version:string}
     */
    public static function status(?string $host = null, ?int $port = null): array
    {
        $host = $host ?? (string) config('minecraft.host');
        $port = $port ?? (int) config('minecraft.port');

        $default = ['online' => false, 'players' => 0, 'max' => 0, 'version' => (string) config('minecraft.version', '1.21.x')];

        $errno = 0;
        $errstr = '';
        $socket = @fsockopen($host, $port, $errno, $errstr, 2.0);
        if ($socket === false) {
            return $default;
        }
        stream_set_timeout($socket, 2);

        try {
            // Handshake packet (next state = status).
            $handshake = "\x00" . self::varint(-1)
                . self::varint(strlen($host)) . $host
                . pack('n', $port) . self::varint(1);
            fwrite($socket, self::varint(strlen($handshake)) . $handshake);
            // Status request.
            fwrite($socket, self::varint(1) . "\x00");

            self::readVarint($socket);     // total length
            self::readVarint($socket);     // packet id
            $jsonLen = self::readVarint($socket);
            $json = '';
            while (strlen($json) < $jsonLen) {
                $chunk = fread($socket, $jsonLen - strlen($json));
                if ($chunk === false || $chunk === '') {
                    break;
                }
                $json .= $chunk;
            }
            $data = json_decode($json, true);
            if (!is_array($data)) {
                return $default;
            }
            return [
                'online'  => true,
                'players' => (int) ($data['players']['online'] ?? 0),
                'max'     => (int) ($data['players']['max'] ?? 0),
                'version' => (string) ($data['version']['name'] ?? $default['version']),
            ];
        } catch (\Throwable) {
            return $default;
        } finally {
            fclose($socket);
        }
    }

    private static function varint(int $value): string
    {
        $out = '';
        $value &= 0xFFFFFFFF;
        do {
            $byte = $value & 0x7F;
            $value = ($value >> 7) & 0x01FFFFFF;
            if ($value !== 0) {
                $byte |= 0x80;
            }
            $out .= chr($byte);
        } while ($value !== 0);
        return $out;
    }

    private static function readVarint($socket): int
    {
        $value = 0;
        $position = 0;
        while (true) {
            $byte = fread($socket, 1);
            if ($byte === false || $byte === '') {
                break;
            }
            $byte = ord($byte);
            $value |= ($byte & 0x7F) << $position;
            if (($byte & 0x80) === 0) {
                break;
            }
            $position += 7;
            if ($position >= 32) {
                break;
            }
        }
        return $value;
    }
}
