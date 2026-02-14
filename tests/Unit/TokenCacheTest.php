<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use insign\BB\Cobranca;

test('token is cached for subsequent calls', function () {
    // Mock responses:
    // 1. First call returns token1
    // 2. Second call (if not cached) would return token2
    $mock = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'token1', 'expires_in' => 3600])),
        new Response(200, [], json_encode(['access_token' => 'token2', 'expires_in' => 3600])),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $cobranca = new Cobranca('clientId', 'clientSecret', 'developerKey');
    $cobranca->setHttpClient($client);

    // First call: Should hit the API (consuming first response)
    $token1 = $cobranca->getTokenAccess();
    expect($token1->access_token)->toBe('token1');

    // Second call: Should return cached token (not consuming second response)
    $token2 = $cobranca->getTokenAccess();
    expect($token2->access_token)->toBe('token1');

    // Verify that only one request was made (mock still has 1 item)
    expect($mock->count())->toBe(1);
});

test('token refreshes after expiration (simulated by short expires_in)', function () {
    // Mock responses:
    // 1. First call returns token1 with very short expiration (so it expires immediately due to buffer)
    // 2. Second call returns token2
    $mock = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'token1', 'expires_in' => 50])), // 50s - 60s buffer = expired 10s ago
        new Response(200, [], json_encode(['access_token' => 'token2', 'expires_in' => 3600])),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $cobranca = new Cobranca('clientId', 'clientSecret', 'developerKey');
    $cobranca->setHttpClient($client);

    // First call: gets token1. Expiration calculation: time() + 50 - 60 = time() - 10.
    $token1 = $cobranca->getTokenAccess();
    expect($token1->access_token)->toBe('token1');

    // Second call: Check if time() < time() - 10 -> False.
    // Should hit API again (consuming second response).
    $token2 = $cobranca->getTokenAccess();
    expect($token2->access_token)->toBe('token2');

    // Verify that both requests were made (mock is empty)
    expect($mock->count())->toBe(0);
});
