<?php

use insign\BB\Cobranca;

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
