<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use insign\BB\Cobranca;

test('token é reutilizado em chamadas subsequentes', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'token1', 'expires_in' => 3600])),
        new Response(200, [], json_encode(['access_token' => 'token2', 'expires_in' => 3600])),
    ]);

    $client = new Client(['handler' => HandlerStack::create($mock)]);

    $cobranca = new Cobranca('clientId', 'clientSecret', 'developerKey');
    $cobranca->setHttpClient($client);

    $token1 = $cobranca->getTokenAccess();
    $token2 = $cobranca->getTokenAccess();

    // Deve retornar o mesmo token (cache), sem consumir a segunda resposta
    expect($token1->access_token)->toBe('token1');
    expect($token2->access_token)->toBe('token1');
    expect($mock->count())->toBe(1);
});
