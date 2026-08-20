<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ContaAzulService
{
    protected $baseUrl = 'https://api.contaazul.com/v1';

    public function getProducts()
    {
        // Aqui precisaremos do seu Access Token (armazenado no banco ou .env)
        $token = config('services.conta_azul.token');

        $response = Http::withToken($token)->get("{$this->baseUrl}/products", [
            'status' => 'ACTIVE'
        ]);

        return $response->json();
    }
}
