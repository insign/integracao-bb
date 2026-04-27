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

test('verifica se parâmetros de query string são codificados corretamente em verBoleto', function () {
    $container = [];
    $history = \GuzzleHttp\Middleware::history($container);

    $mock = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'token', 'expires_in' => 3600])),
        new Response(200, [], json_encode(['status' => 'ok'])),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push($history);

    $client = new Client(['handler' => $handlerStack]);

    // Configura a Cobranca com uma developerKey contendo caracteres especiais
    $cobranca = new Cobranca('clientId', 'clientSecret', 'key&123=456+789');
    $cobranca->setHttpClient($client);

    // Chama o método verBoleto passando um convênio com caracteres especiais
    $cobranca->verBoleto(123, 'convenio&123');

    $request = $container[1]['request'];
    $query = $request->getUri()->getQuery();

    // Verifica se os parâmetros foram encodados corretamente de acordo com a RFC 3986
    expect($query)->toContain('gw-dev-app-key=key%26123%3D456%2B789');
    expect($query)->toContain('numeroConvenio=convenio%26123');
});
