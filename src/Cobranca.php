<?php

namespace insign\BB;

use stdClass;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class Cobranca
{
  protected Client $httpClient;
  protected ?object $token = null;
  protected int $tokenExpiresAt = 0;

  public function __construct(
    private string $clientID,
    private string $clientSecret,
    private string $developerKey,
    protected bool $production = FALSE
  )
  {
    $this->setHttpClient(new Client([
                                      'base_uri' => $this->getUrlApi(),
                                      'verify'   => $this->isProduction(),
                                    ]));

  }

  public function getUrlToken(): string
  {
    return "https://oauth." . ($this->isProduction() ? '' : 'sandbox.') . "bb.com.br/oauth/token";
  }

  public function getUrlApi(): string
  {
    return "https://api." . ($this->isProduction() ? '' : 'sandbox.') . "bb.com.br/";
  }

  public function getTokenAccess()
  {
    if ($this->token && time() < $this->tokenExpiresAt) {
      return $this->token;
    }

    $headers = [
      "Content-Type"  => "application/x-www-form-urlencoded",
      "Authorization" => "Basic " . $this->getBasicHash(),
    ];

    $body = [
      'grant_type' => "client_credentials",
      'scope'      => "cobrancas.boletos-info cobrancas.boletos-requisicao",
    ];

    $response = $this->getHttpClient()->post(
      $this->getUrlToken(),
      [
        'headers'     => $headers,
        'form_params' => $body,
      ]
    );

    $this->token = json_decode($response->getBody()->getContents());

    if (is_object($this->token)) {
      $expiresIn = $this->token->expires_in ?? 3600;
      $this->tokenExpiresAt = time() + $expiresIn - 60;
    }

    return $this->token;
  }

  public function registrarBoleto(array $campos): stdClass
  {
    $response = $this->getHttpClient()->post(
      uri    : "cobrancas/v2/boletos?gw-dev-app-key={$this->developerKey}",
      options: [
                 "headers" => $this->getAuthHeaders(),
                 "json"    => $campos,
               ]
    );

    return $this->processAnswer($response);
  }

  public function alterarBoleto($id, array $campos): stdClass
  {
    $response = $this->getHttpClient()->patch(
      "cobrancas/v2/boletos/{$id}?gw-dev-app-key={$this->developerKey}",
      [
        "headers" => $this->getAuthHeaders(),
        "json"    => $campos,
      ]
    );

    return $this->processAnswer($response);
  }

  public function verBoleto(int|string $id, int|string $convenio): stdClass
  {
    $response = $this->getHttpClient()->get(
      "cobrancas/v2/boletos/{$id}?gw-dev-app-key={$this->developerKey}&numeroConvenio={$convenio}",
      [
        "headers" => $this->getAuthHeaders(),
      ]
    );

    return $this->processAnswer($response);
  }

  public function baixarBoleto(int|string $id, int|string $convenio): stdClass
  {
    $response = $this->getHttpClient()->post(
      "cobrancas/v2/boletos/{$id}/baixar?gw-dev-app-key={$this->developerKey}",
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
    return json_decode($response->getBody()->getContents(), FALSE, 512, JSON_THROW_ON_ERROR);
  }

  public function getHttpClient(): Client
  {
    return $this->httpClient;
  }

  public function setHttpClient(Client $httpClient): void
  {
    $this->httpClient = $httpClient;
  }

}
