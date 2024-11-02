# Integração com a API de Cobranças do Banco do Brasil
![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/insign/integracao-bb/test.yml?style=for-the-badge&label=TEST)

## Instalação

```bash
composer require insign/integracao-bb
```

## Instanciar a classe

```php
use insign\BB\Cobranca;

$cobranca = new Cobranca('clientId', 'clientSecret', 'developerKey', production: false);
```

## Gerar Token

> De forma geral, não é necessário gerar o token, pois o token é gerado automaticamente pela API.

```php
$token = $cobranca->getTokenAccess();
```

## Registrar Boleto

```php
// https://apoio.developers.bb.com.br/referency/post/5f4fb7f5b71fb5001268ca44
$convenio = '3128557';
$idBoleto = '000' . $convenio . random_int(1000000000, 9999999999);

$registro = [
  'numeroConvenio'                       => $convenio,
  'numeroCarteira'                       => '17',
  'numeroVariacaoCarteira'               => '35',
  'codigoModalidade'                     => '1',
  'dataEmissao'                          => date('d.m.Y'),
  'dataVencimento'                       => '31.12.' . date('Y'),
  'valorOriginal'                        => '128.40',
  'indicadorAceiteTituloVencido'         => 'N',
  'codigoAceite'                         => 'A',
  'codigoTipoTitulo'                     => '2',
  'descricaoTipoTitulo'                  => 'DUPLICATA MERCANTIL',
  'indicadorPermissaoRecebimentoParcial' => 'N',
  'numeroTituloBeneficiario'             => '1',
  'campoUtilizacaoBeneficiario'          => 'UM TEXTO',
  'numeroTituloCliente'                  => $idBoleto,
  'mensagemBloquetoOcorrencia'           => '',
  'pagador'                              => [
    'tipoInscricao'   => '1',
    'numeroInscricao' => '97965940132',
    'nome'            => 'Teste Teste',
    'endereco'        => 'R. Teste',
    'cep'             => '10110000',
    'cidade'          => 'São Paulo',
    'bairro'          => 'Centro',
    'uf'              => 'SP',
    'telefone'        => '1112345678',
  ],
  //    'beneficiarioFinal' => [
  //        'tipoInscricao' => '1',
  //        'numeroInscricao' => '',
  //        'nome' => '',
  //    ],
  'indicadorPix'                         => 'S',
];
try {
  $boletoRegistrado = $cobranca->registrarBoleto($registro);
}
catch (\Exception $e) {
  echo "\nErro ao registrar o boleto: {$e->getMessage()}\n";
  echo json_encode($registro);
  return;
}

if ($idBoleto == $boletoRegistrado->numero) {
  echo "\nBoleto registrado com sucesso\n";
  echo json_encode($boletoRegistrado);
} else {
  echo "\nErro ao registrar o boleto";
  echo json_encode($boletoRegistrado);
  return;
}
```

## Consultar Boleto

```php
$id_boleto = 'nossonumero';
$numeroConvenio = '';
$listar = $cobranca->verBoleto($id_boleto, $numeroConvenio);
```

## Editar Boleto

```php
$id_boleto = 'nossonumero';
$data = [
    'numeroConvenio' =>  '3128557',
    'indicadorNovaDataVencimento' => 'S', 
    'alteracaoData' => [
        'novaDataVencimento' => '01.04.2021',
    ],
    'indicadorAtribuirDesconto' => 'N',
    'desconto' => [
        'tipoPrimeiroDesconto' => '',
        'valorPrimeiroDesconto' => '',
        'percentualPrimeiroDesconto' => '',
        'dataPrimeiroDesconto' => '',
        'tipoSegundoDesconto' => '',
        'valorSegundoDesconto' => '',
        'percentualSegundoDesconto' => '',
        'dataSegundoDesconto' => '',
        'tipoTerceiroDesconto' => '',
        'valorTerceiroDesconto' => '',
        'percentualTerceiroDesconto' => '',
        'dataTerceiroDesconto' => '',
    ],
    'indicadorAlterarDesconto' => 'N',
    'alteracaoDesconto' => [
        'tipoPrimeiroDesconto' => '',
        'novoValorPrimeiroDesconto' => '',
        'novoPercentualPrimeiroDesconto' => '',
        'novaDataLimitePrimeiroDesconto' => '',
        'tipoSegundoDesconto' => '',
        'novoValorSegundoDesconto' => '',
        'novoPercentualSegundoDesconto' => '',
        'novaDataLimiteSegundoDesconto' => '',
        'tipoTerceiroDesconto' => '',
        'novoValorTerceiroDesconto' => '',
        'novoPercentualTerceiroDesconto' => '',
        'novaDataLimiteTerceiroDesconto' => '',
    ],
    'indicadorAlterarDataDesconto' => 'N',
    'alteracaoDataDesconto' => [
        'novaDataLimitePrimeiroDesconto' => '',
        'novaDataLimiteSegundoDesconto' => '',
        'novaDataLimiteTerceiroDesconto' => '',
    ],
    'indicadorProtestar' => 'N',
    'protesto' => [
        'quantidadeDiasProtesto' => '',
    ],    
    'indicadorSustacaoProtesto' => 'N',
    'indicadorCancelarProtesto' => 'N',
    'indicadorIncluirAbatimento' => 'N',
    'abatimento' => [
        'valorAbatimento' => '',
    ],
    'indicadorCancelarAbatimento' => 'N',
    'alteracaoAbatimento' => [
        'novoValorAbatimento' => '',
    ],
    'indicadorCobrarJuros' => 'N',
    'juros' => [
        'tipoJuros' => '',
        'valorJuros' => '',
        'taxaJuros' => '',
    ],
    'indicadorDispensarJuros' => 'N',
    'indicadorCobrarMulta' => 'N',
    'multa' => [
        'tipoMulta' => '',
        'valorMulta' => '',
        'dataInicioMulta' => '',
        'taxaMulta' => '',
    ],
    'indicadorDispensarMulta' => 'N',
    'indicadorNegativar' => 'N',
    'negativacao' => [
        'quantidadeDiasNegativacao' => '',
        'tipoNegativacao' => '',
    ],
    'indicadorAlterarSeuNumero' => 'N',
    'alteracaoSeuNumero' => [
        'codigoSeuNumero' => '',
    ],
    'indicadorAlterarEnderecoPagador' => 'N',
    'alteracaoEndereco' => [
        'enderecoPagador' => '',
        'bairroPagador' => '',
        'cidadePagador' => '',
        'UFPagador' => '',
        'CEPPagador' => '',
    ],
    'indicadorAlterarPrazoBoletoVencido' => 'N',
    'alteracaoPrazo' => [
        'quantidadeDiasAceite' => '',
    ]
];


$alterar = $cobranca->alterarBoleto($id_boleto, $data);
```

## Baixar Boleto

```php
$id_boleto = 'nossonumero';
$convenio 'convenio'

$baixar = $cobranca->baixarBoleto($id_boleto, $convenio);
```
## Informações

Informações sobre a API utilizada nesta integração: https://apoio.developers.bb.com.br/referency/post/5f9c2149f39b8500120ab13c

## Contribuições

Contribua com esta integração no [GitHub](https://github.com/insign/integracao-bb)

Para enviar uma nova versão execute `make push` (nova tag patch com seus commits)

## Testes
Execute os testes com [Pest](https://pestphp.com/) executando: `make test`

## Licença
[GNU Affero General Public License v3.0](LICENSE)
