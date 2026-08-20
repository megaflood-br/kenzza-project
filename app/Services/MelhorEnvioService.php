<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MelhorEnvioService
{
    protected $token;
    protected $url;

    public function __construct()
    {
        $this->token = env('MELHORENVIO_TOKEN');
        $this->url = env('MELHORENVIO_URL');
    }

    /**
     * Cabeçalhos padronizados para as requisições de geração de etiqueta
     */
    private function headers()
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
            // Adicionando um User-Agent mais específico e um cabeçalho de identificação
            'User-Agent' => 'KenzzaEcommerce/1.0 (contato@kenzza.com.br)',
            'X-Requested-With' => 'XMLHttpRequest'
        ];
    }

    /**
     * Cálculo de Frete (Carrinho) com tratamento de contingência local contra quedas e timeouts
     */
    public function calcularFrete($cepDestino, $produtos)
    {
        try {
            // Define um timeout agressivo de 3 segundos para o checkout do cliente não travar esperando o Sandbox instável
            $response = Http::timeout(3)
                ->withToken($this->token)
                ->acceptJson()
                ->post($this->url . '/api/v2/me/shipment/calculate', [
                    'from' => [
                        'postal_code' => env('CEP_ORIGEM')
                    ],
                    'to' => [
                        'postal_code' => $cepDestino
                    ],
                    'products' => $produtos, // Array com id, quantity, price, weight, width, height, length

                    // CORREÇÃO: Restringe o cálculo APENAS para Correios e Jadlog
                    // 1: PAC | 2: SEDEX | 3: Jadlog Package | 4: Jadlog .Com
                    'services' => '1,2,3,4'
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            // Força a queda no catch caso o status code retornado seja de erro (ex: 500 ou 400)
            throw new Exception('A API do Melhor Envio retornou status de erro: ' . $response->status());

        } catch (Exception $e) {
            // Grava o aviso no log para você saber que a contingência entrou em ação
            Log::warning('Melhor Envio instável ou indisponível. Aplicando Frete Fixo de contingência: ' . $e->getMessage());

            // --- FLUXO DE CONTINGÊNCIA COMPLETO (Injetado company e delivery_range) ---
            return [
                [
                    "id" => 1,
                    "name" => "PAC",
                    "price" => "15.00",
                    "custom_price" => "15.00",
                    "delivery_time" => 7,
                    "error" => null,
                    "delivery_range" => [
                        "min" => 5,
                        "max" => 8
                    ],
                    "company" => [
                        "id" => 1,
                        "name" => "Correios",
                        "picture" => ""
                    ]
                ],
                [
                    "id" => 2,
                    "name" => "SEDEX",
                    "price" => "25.00",
                    "custom_price" => "25.00",
                    "delivery_time" => 3,
                    "error" => null,
                    "delivery_range" => [
                        "min" => 1,
                        "max" => 3
                    ],
                    "company" => [
                        "id" => 1,
                        "name" => "Correios",
                        "picture" => ""
                    ]
                ],
                [
                    "id" => 3,
                    "name" => "Jadlog Package",
                    "price" => "18.90",
                    "custom_price" => "18.90",
                    "delivery_time" => 5,
                    "error" => null,
                    "delivery_range" => [
                        "min" => 3,
                        "max" => 6
                    ],
                    "company" => [
                        "id" => 2,
                        "name" => "Jadlog",
                        "picture" => ""
                    ]
                ]
            ];
        }
    }

    /**
     * Fluxo completo: Adiciona ao Carrinho, Faz Checkout, Gera Etiqueta e Retorna a URL do PDF
     */
    public function gerarEtiquetaCompleta($order, $serviceId)
    {
        try {
            // 1. Adicionar ao Carrinho
            $cartResponse = $this->addToCart($order, $serviceId);
            $orderIdME = $cartResponse['id'];

            // 2. Checkout (Compra da etiqueta usando o saldo da sua carteira no Melhor Envio)
            $this->checkout($orderIdME);

            // 3. Gerar a Etiqueta na Transportadora
            $this->generateLabel($orderIdME);

            // 4. Solicitar a URL de Impressão (PDF)
            $printResponse = $this->printLabel($orderIdME);

            return [
                'sucesso' => true,
                'url_etiqueta' => $printResponse['url'],
                'order_id_me' => $orderIdME
            ];

        } catch (Exception $e) {
            Log::error('Erro Melhor Envio (Pedido #' . $order->id . '): ' . $e->getMessage());
            return [
                'sucesso' => false,
                'erro' => $e->getMessage()
            ];
        }
    }

    /**
     * Passo 1: Insere os dados do pedido no carrinho do Melhor Envio
     */
    private function addToCart($order, $serviceId)
    {
        // Tratamento "à prova de balas" para garantir que nenhum dado chegue vazio (null) na API
        $address = $order->user->logradouro ?? $order->user->address ?? $order->user->street ?? 'Rua não informada';
        $number = $order->user->numero ?? $order->user->number ?? 'S/N';
        $district = $order->user->bairro ?? $order->user->neighborhood ?? 'Centro';
        $city = $order->user->cidade ?? $order->user->city ?? 'Atibaia';
        $state = strtoupper($order->user->estado ?? $order->user->state ?? 'SP');
        $zipCode = preg_replace('/\D/', '', $order->user->cep ?? $order->user->zip_code ?? env('CEP_ORIGEM', '12940000'));
        $document = preg_replace('/\D/', '', $order->user->document ?? $order->user->cpf ?? '00000000000');
        $phone = preg_replace('/\D/', '', $order->user->telefone ?? $order->user->phone ?? '11999999999');

        // Limpeza dos dados da K'enzza para garantir que a API aceite (apenas números)
        $kenzzaPhone = preg_replace('/\D/', '', env('KENZZA_PHONE', '11937525151'));
        $kenzzaCnpj = preg_replace('/\D/', '', env('KENZZA_CNPJ', '46185207000189'));
        // IMPORTANTE: O Melhor Envio exige um CPF válido de uma pessoa física no remetente, mesmo sendo empresa.
        $kenzzaCpfResponsavel = preg_replace('/\D/', '', env('KENZZA_CPF', '11122233344'));

        $payload = [
            "service" => $serviceId,
            "agency" => env('MELHOR_ENVIO_AGENCY_ID', 1),
            "from" => [
                "name" => "Kenzza Professional",
                "phone" => $kenzzaPhone,
                "email" => env('KENZZA_EMAIL', "mkt@kenzza.com.br"),
                "document" => $kenzzaCpfResponsavel, // AQUI VAI O CPF DO RESPONSÁVEL (11 dígitos reais)
                "company_document" => $kenzzaCnpj,   // AQUI VAI O CNPJ DA EMPRESA (14 dígitos)
                "address" => env('KENZZA_RUA', "Nelson Francisco de Almeida"),
                "number" => env('KENZZA_NUMERO', "10570"),
                "district" => env('KENZZA_BAIRRO', "Terra Preta"),
                "city" => env('KENZZA_CIDADE', "Mairiporã"),
                "state_abbr" => env('KENZZA_ESTADO', "SP"),
                "country_id" => "BR",
                "postal_code" => preg_replace('/\D/', '', env('CEP_ORIGEM', '12951110'))
            ],
            "to" => [
                "name" => $order->user->name ?? 'Cliente Kenzza',
                "phone" => $phone,
                "email" => $order->user->email ?? 'mkt@kenzza.com.br',
                "document" => $document,
                "address" => $address,
                "number" => $number,
                "complement" => $order->user->complemento ?? $order->user->complement ?? "",
                "district" => $district,
                "city" => $city,
                "state_abbr" => $state,
                "country_id" => "BR",
                "postal_code" => $zipCode
            ],
            "products" => [
                [
                    "name" => "Produtos Kenzza - Pedido #" . $order->id,
                    "quantity" => 1,
                    "unitary_value" => $order->total
                ]
            ],
            "volumes" => [
                [
                    // ATENÇÃO: As medidas aqui devem ser idênticas ou muito próximas
                    // das usadas no carrinho para o preço não dar diferença!
                    "height" => 16,  // Estava 15
                    "width" => 11,   // Estava 20
                    "length" => 20,  // Estava 30
                    "weight" => 0.5  // Estava 1.5kg
                ]
            ],
            "options" => [
                // Se você não embutiu o valor do seguro no cálculo do frete do carrinho,
                // declarar o valor aqui vai deixar a etiqueta mais cara que o frete pago.
                // Para igualar o valor exato, você pode colocar 0, ou assumir o custo do seguro.
                "insurance_value" => $order->total,
                "receipt" => false,
                "own_hand" => false,
                "reverse" => false,
                "non_commercial" => true
            ]
        ];

        $response = \Illuminate\Support\Facades\Http::withHeaders($this->headers())->post($this->url . '/api/v2/me/cart', $payload);

        if ($response->failed()) {
            throw new \Exception('Erro Carrinho: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Passo 2: Finaliza a compra da etiqueta com o saldo da carteira
     */
    private function checkout($orderIdME)
    {
        $payload = ["orders" => [(string) $orderIdME]];
        $response = Http::withHeaders($this->headers())->post($this->url . '/api/v2/me/shipment/checkout', $payload);

        if ($response->failed()) {
            throw new Exception('Erro Checkout: ' . $response->body());
        }
        return $response->json();
    }

    /**
     * Passo 3: Solicita a geração do código de rastreio e etiqueta final
     */
    private function generateLabel($orderIdME)
    {
        $payload = ["orders" => [(string) $orderIdME]];
        $response = Http::withHeaders($this->headers())->post($this->url . '/api/v2/me/shipment/generate', $payload);

        if ($response->failed()) {
            throw new Exception('Erro Geração: ' . $response->body());
        }
        return $response->json();
    }

    /**
     * Passo 4: Retorna a URL pública para impressão em PDF
     */
    private function printLabel($orderIdME)
    {
        $payload = [
            "mode" => "public", // Link direto para o PDF
            "orders" => [(string) $orderIdME]
        ];
        $response = Http::withHeaders($this->headers())->post($this->url . '/api/v2/me/shipment/print', $payload);

        if ($response->failed()) {
            throw new Exception('Erro Impressão: ' . $response->body());
        }
        return $response->json();
    }
}
