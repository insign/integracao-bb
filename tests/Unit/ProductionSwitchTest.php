<?php

use insign\BB\Cobranca;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

test('setProduction updates HTTP client base_uri and clears token', function () {
    $cobranca = new Cobranca('clientId', 'clientSecret', 'developerKey', false);

    // Set a fake token
    $mock = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'sandbox_token', 'expires_in' => 3600])),
    ]);
    $client = new Client(['handler' => HandlerStack::create($mock), 'base_uri' => $cobranca->getUrlApi()]);
    $cobranca->setHttpClient($client);

    // Get token to cache it
    $token = $cobranca->getTokenAccess();
    expect($token->access_token)->toBe('sandbox_token');

    // Switch to production
    $cobranca->setProduction(true);

    $client = $cobranca->getHttpClient();
    $config = $client->getConfig();
    expect((string)$config['base_uri'])->toBe('https://api.bb.com.br/');

    // Token should be cleared, so next call would normally fetch a new one
    // We mock a new response for production
    $mockProd = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'prod_token', 'expires_in' => 3600])),
    ]);
    // The previously updated client now has no handler for our fake test, so we re-inject the handler stack.
    $config['handler'] = HandlerStack::create($mockProd);
    $clientProd = new Client($config);
    $cobranca->setHttpClient($clientProd);

    $tokenProd = $cobranca->getTokenAccess();
    expect($tokenProd->access_token)->toBe('prod_token');
});
