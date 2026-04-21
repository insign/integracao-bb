<?php

use insign\BB\Cobranca;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

beforeEach(function () {
  $this->cobranca = new Cobranca('clientId', 'clientSecret', 'developerKey', production: false);
  $this->cobrancaProd = new Cobranca('clientId', 'clientSecret', 'developerKey', production: true);
});

test('verifica se a URL do token está correta em ambiente sandbox', function () {
  expect($this->cobranca->getUrlToken())->toBe('https://oauth.sandbox.bb.com.br/oauth/token');
});

test('verifica se a URL do token está correta em ambiente produção', function () {
  expect($this->cobrancaProd->getUrlToken())->toBe('https://oauth.bb.com.br/oauth/token');
});

test('verifica se a URL da API está correta em ambiente sandbox', function () {
  expect($this->cobranca->getUrlApi())->toBe('https://api.sandbox.bb.com.br/');
});

test('verifica se a URL da API está correta em ambiente produção', function () {
  expect($this->cobrancaProd->getUrlApi())->toBe('https://api.bb.com.br/');
});

test('verifica se a hash básica é gerada corretamente', function () {
  $expectedHash = base64_encode('clientId:clientSecret');
  expect($this->cobranca->getBasicHash())->toBe($expectedHash);
});

test('verifica se o modo produção está desativado por padrão', function () {
  expect($this->cobranca->isProduction())->toBeFalse();
});

test('verifica se o modo produção está habilitado', function () {
  expect($this->cobrancaProd->isProduction())->toBeTrue();
});

test('lança exceção JSON em getTokenAccess com resposta não JSON', function () {
    $mock = new MockHandler([
        new Response(200, [], '<html>Bad Gateway</html>'),
    ]);
    $client = new Client(['handler' => HandlerStack::create($mock)]);
    $this->cobranca->setHttpClient($client);

    expect(fn() => $this->cobranca->getTokenAccess())->toThrow(JsonException::class);
});

test('lança exceção JSON em baixarBoleto com resposta não JSON', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'token', 'expires_in' => 3600])),
        new Response(200, [], '<html>Bad Gateway</html>'),
    ]);
    $client = new Client(['handler' => HandlerStack::create($mock)]);
    $this->cobranca->setHttpClient($client);

    expect(fn() => $this->cobranca->baixarBoleto(123, 456))->toThrow(JsonException::class);
});
