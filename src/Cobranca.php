<?php

namespace Verseles\BB;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use stdClass;

class Cobranca
{
  protected Client $httpClient;

  public function __construct(
    private string $clientID,
    private string $clientSecret,
    private string $developerKey,
    protected bool $production = false
  )
  {
    $this->httpClient = new Client([
      'base_uri' => $this->getUrlApi(),
      'verify'   => $this->isProduction(),
    ]);

  }

  public function getUrlToken(): string
  {
    return "https://oauth." . ($this->isProduction() ?: 'sandbox.') . "bb.com.br/oauth/token";
  }

  public function getUrlApi(): string
  {
    return "https://api." . ($this->isProduction() ?: 'sandbox.') . "bb.com.br/";
  }

  public function getTokenAccess()
  {
    $headers = [
      "Content-Type"  => "application/x-www-form-urlencoded",
      "Authorization" => "Basic " . $this->getBasicHash(),
    ];

    $body = [
      'grant_type' => "client_credentials",
      'scope'      => "cobrancas.boletos-info cobrancas.boletos-requisicao",
    ];

    $response = $this->httpClient->post(
      $this->getUrlToken(),
      [
        'headers'     => $headers,
        'form_params' => $body,
      ]
    );

    return json_decode($response->getBody()->getContents());
  }

  public function registrarBoleto(array $campos): stdClass
  {
    $response = $this->httpClient->post(
      uri    : "cobrancas/v2/boletos",
      options: [
        "headers" => $this->getAuthHeaders(),
        "json"    => $campos,
      ]
    );

    return $this->processAnswer($response);
  }

  public function alterarBoleto($id, array $campos): stdClass
  {
    $response = $this->httpClient->patch(
      "cobrancas/v2/boletos/{$id}",
      [
        "headers" => $this->getAuthHeaders(),
        "json"    => $campos,
      ]
    );

    return $this->processAnswer($response);
  }

  public function verBoleto(int|string $id, int|string $campos): stdClass
  {
    $response = $this->httpClient->get(
      "cobrancas/v2/boletos/{$id}?numeroConvenio={$campos}",
      [
        "headers" => $this->getAuthHeaders(),
      ]
    );

    return $this->processAnswer($response);
  }

  public function baixarBoleto(int|string $id, int|string $convenio): stdClass
  {
    $response = $this->httpClient->post(
      "cobrancas/v2/boletos/{$id}/baixar",
      [
        "headers" => $this->getAuthHeaders(),
        "json"    => ["numeroConvenio" => $convenio],
      ]
    );

    return json_decode($response->getBody()->getContents());
  }

  public function setProduction(bool $production): void
  {
    $this->production = $production;
  }

  public function isProduction(): bool
  {
    return $this->production;
  }

  public function getBasicHash(): string
  {
    return base64_encode("{$this->clientID}:{$this->clientSecret}");
  }

  public function getAuthHeaders(): array
  {
    return [
      "Authorization"               => "Bearer " . $this->getTokenAccess()->access_token,
      "Content-Type"                => "application/json",
      "X-Developer-Application-Key" => $this->developerKey,
    ];
  }

  public function processAnswer(ResponseInterface $response): stdClass
  {
    return json_decode($response->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);
  }

}
