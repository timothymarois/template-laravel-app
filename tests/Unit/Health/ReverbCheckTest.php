<?php

declare(strict_types=1);

use App\Health\Checks\ReverbCheck;

/*
|--------------------------------------------------------------------------
| ReverbCheck — TCP liveness probe for the Reverb websocket server
|--------------------------------------------------------------------------
|
| Verifies the check probes the address Reverb *binds* to (reverb.servers.reverb)
| rather than the client/publish endpoint, rewrites the 0.0.0.0 wildcard to
| loopback, and reports ok/failed by actually opening a socket against a real
| ephemeral TCP server.
|
*/

it('probes the reverb server bind address, not the publish endpoint', function () {
    // Publish/client endpoint points at a public host on 443 — a TCP connect there
    // would hit nginx and give a false positive. The check must ignore it.
    config()->set('broadcasting.connections.reverb.options', ['host' => 'ws.example.com', 'port' => 443]);
    // The server actually binds here.
    config()->set('reverb.servers.reverb.host', '0.0.0.0');
    config()->set('reverb.servers.reverb.port', 8080);

    $result = (new ReverbCheck)->run();

    // 0.0.0.0 wildcard rewritten to loopback; port taken from the server config.
    expect($result->meta)->toMatchArray(['host' => '127.0.0.1', 'port' => 8080]);
});

it('passes when the reverb port is accepting connections', function () {
    // Stand in for Reverb with a real ephemeral TCP server on a free port.
    $server = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
    $port = (int) explode(':', (string) stream_socket_get_name($server, false))[1];

    config()->set('reverb.servers.reverb.host', '127.0.0.1');
    config()->set('reverb.servers.reverb.port', $port);

    $result = (new ReverbCheck)->run();

    fclose($server);

    expect($result->status->value)->toBe('ok')
        ->and($result->getShortSummary())->toBe('Accepting connections');
});

it('fails when nothing is listening on the reverb port', function () {
    // Bind then immediately release to obtain a port guaranteed not to be listening.
    $server = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
    $port = (int) explode(':', (string) stream_socket_get_name($server, false))[1];
    fclose($server);

    config()->set('reverb.servers.reverb.host', '127.0.0.1');
    config()->set('reverb.servers.reverb.port', $port);

    $result = (new ReverbCheck)->run();

    expect($result->status->value)->toBe('failed')
        ->and($result->getShortSummary())->toBe('Unreachable')
        ->and($result->meta)->toMatchArray(['host' => '127.0.0.1', 'port' => $port]);
});
