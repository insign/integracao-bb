<?php

use insign\BB\Cobranca;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

beforeEach(function () {
    $this->cobranca = new Cobranca('clientId', 'clientSecret', 'developerKey', production: false);
});

test('baixarBoleto lança JsonException se API retornar JSON inválido', function () {
    $mock = new MockHandler([
        new Response(200, [], '{"access_token": "token123", "expires_in": 3600}'), // Token response (valido)
        new Response(200, [], 'json invalido'), // baixarBoleto response (invalido)
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $this->cobranca->setHttpClient($client);

    $this->cobranca->baixarBoleto(1, 1);
})->throws(JsonException::class);

test('getTokenAccess lança JsonException se API retornar JSON inválido', function () {
    $mock = new MockHandler([
        new Response(200, [], 'json invalido'), // Token response (invalido)
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $this->cobranca->setHttpClient($client);

    $this->cobranca->getTokenAccess();
})->throws(JsonException::class);
