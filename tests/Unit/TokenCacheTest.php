<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use insign\BB\Cobranca;

test('getTokenAccess chama a API apenas uma vez quando em cache', function () {
    // Create a mock for the Client class
    $mockClient = $this->createMock(Client::class);

    // Expect post to be called exactly once
    $mockClient->expects($this->once())
        ->method('post')
        ->willReturn(new Response(200, [], json_encode(['access_token' => 'mock_token', 'expires_in' => 3600])));

    $cobranca = new Cobranca('clientId', 'clientSecret', 'developerKey');
    $cobranca->setHttpClient($mockClient);

    // Call twice
    $token1 = $cobranca->getTokenAccess();
    $token2 = $cobranca->getTokenAccess();

    expect($token1->access_token)->toBe('mock_token');
    expect($token2->access_token)->toBe('mock_token');
});

test('getTokenAccess chama a API novamente quando forceRefresh é true', function () {
    $mockClient = $this->createMock(Client::class);

    $mockClient->expects($this->exactly(2))
        ->method('post')
        ->willReturnOnConsecutiveCalls(
            new Response(200, [], json_encode(['access_token' => 'token1', 'expires_in' => 3600])),
            new Response(200, [], json_encode(['access_token' => 'token2', 'expires_in' => 3600]))
        );

    $cobranca = new Cobranca('clientId', 'clientSecret', 'developerKey');
    $cobranca->setHttpClient($mockClient);

    $token1 = $cobranca->getTokenAccess();
    $token2 = $cobranca->getTokenAccess(true); // Force refresh

    expect($token1->access_token)->toBe('token1');
    expect($token2->access_token)->toBe('token2');
});
