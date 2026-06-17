<?php

declare(strict_types=1);

namespace App\Health\Checks;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

/**
 * Liveness check for the Laravel Reverb websocket server.
 *
 * Spatie ships no Reverb check, so we probe it ourselves: a plain TCP connect to
 * the address Reverb actually BINDS to (`reverb.servers.reverb`, default
 * `0.0.0.0:8080`, probed over loopback). We deliberately do NOT use the
 * client/publish endpoint (`broadcasting.connections.reverb.options` / REVERB_HOST)
 * — that can point at the public domain, where a TCP connect would hit nginx and
 * falsely report Reverb healthy. Probing the bind address tests the process
 * directly. Gated to forks that broadcast via Reverb (registered with `->if(...)`
 * in AppServiceProvider), so it is inert elsewhere.
 */
class ReverbCheck extends Check
{
    protected int $timeout = 2;

    /** Connection timeout in seconds. */
    public function timeout(int $seconds): self
    {
        $this->timeout = $seconds;

        return $this;
    }

    public function run(): Result
    {
        // Probe where Reverb BINDS (its own listener), not the publish/client
        // endpoint — see the class docblock for why.
        $host = (string) config('reverb.servers.reverb.host', '0.0.0.0');
        $port = (int) config('reverb.servers.reverb.port', 8080);

        // The server binds the 0.0.0.0 wildcard; probe it over loopback.
        if (in_array($host, ['0.0.0.0', ''], true)) {
            $host = '127.0.0.1';
        }

        $result = Result::make()->meta(['host' => $host, 'port' => $port]);

        $connection = @fsockopen($host, $port, $errno, $errstr, $this->timeout);

        if ($connection === false) {
            return $result
                ->failed("Reverb is not accepting connections on {$host}:{$port} ({$errstr})")
                ->shortSummary('Unreachable');
        }

        fclose($connection);

        return $result->ok()->shortSummary('Accepting connections');
    }
}
