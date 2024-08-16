# Integração com a API de Cobranças do Banco do Brasil

## Instanciar a classe

```php
use Verseles\BB\Cobranca;

$cobranca = new Cobranca('clientId', 'clientSecret', 'developerKey', production: false);
```

## Gerar Token

> De forma geral, não é necessário gerar o token, pois o token é gerado automaticamente pela API.

```php
$token = $cobranca->getTokenAccess();
```

## Registrar Boleto

```php
$registro = [
    'numeroConvenio' => '',
    'numeroCarteira' => '17',
    'numeroVariacaoCarteira' => '35',
    'codigoModalidade' => '01', //Identifica a característica dos boletos dentro das modalidades de cobrança existentes no banco. Domínio: 01 - SIMPLES; 04 - VINCULADA
    'dataEmissao' => '30.03.2021', //Data de emissão do boleto (formato "dd.mm.aaaaa").
    'dataVencimento' => '31.03.2021', //Data de vencimento do boleto (formato "dd.mm.aaaaa").
    'valorOriginal' => '10', //Valor de cobrança > 0.00, emitido em Real (formato decimal separado por "."). Valor do boleto no registro. Deve ser maior que a soma dos campos “VALOR DO DESCONTO DO TÍTULO” e “VALOR DO ABATIMENTO DO TÍTULO”, se informados. Informação não passível de alteração após a criação. No caso de emissão com valor equivocado, sugerimos cancelar e emitir novo boleto.
    'valorAbatimento' => '0',
    'quantidadeDiasProtesto' => '',
    'quantidadeDiasNegativacao' => '',
    'orgaoNegativador' => '',
    'indicadorAceiteTituloVencido' => 'S',
    'numeroDiasLimiteRecebimento' => '90',
    'codigoAceite' => 'A',
    'codigoTipoTitulo' => '2',
    'descricaoTipoTitulo' => 'DUPLICATA MERCANTIL',
    'indicadorPermissaoRecebimentoParcial' => 'N',
    'numeroTituloBeneficiario' => '1',
    'campoUtilizacaoBeneficiario' => 'UM TEXTO',
    'numeroTituloCliente' => 'nossonumero',
    'mensagemBloquetoOcorrencia' => '',
    'desconto' => [
        'tipo' => '',
        'dataExpiracao' => '',
        'porcentagem' => '',
        'valor' => '',
    ],
    'segundoDesconto' => [
        'dataExpiracao' => '',
        'porcentagem' => '',
        'valor' => '',
    ],
    'terceiroDesconto' => [
        'dataExpiracao' => '',
        'porcentagem' => '',
        'valor' => '',
    ],
    'jurosMora' => [
        'tipo' => '',
        'porcentagem' => '',
        'valor' => '',
    ],
    'multa' => [
        'tipo' => '',
        'data' => '',
        'porcentagem' => '',
        'valor' => '',
    ],
    'pagador' => [
        'tipoInscricao' => '1',
        'numeroInscricao' => '',
        'nome' => '',
        'endereco' => '',
        'cep' => '',
        'cidade' => '',
        'bairro' => '',
        'uf' => '',
        'telefone' => '',
    ],
    'beneficiarioFinal' => [
        'tipoInscricao' => '1',
        'numeroInscricao' => '',
        'nome' => '',
    ],    
    'indicadorPix' => 'S'
]

$registrar = $cobranca->registrarBoleto($registro);
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

## Licença
