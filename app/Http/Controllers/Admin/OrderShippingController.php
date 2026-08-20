<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\MelhorEnvioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// Importando as classes de notificação que criamos
use App\Notifications\OrderStatusUpdated;
use App\Notifications\OrderTrackingCode;

class OrderShippingController extends Controller
{
    protected $melhorEnvio;

    public function __construct(MelhorEnvioService $melhorEnvio)
    {
        $this->melhorEnvio = $melhorEnvio;
    }

    public function emitirEtiqueta(Request $request, $id)
    {
        $order = Order::with('user', 'items')->findOrFail($id);

        // Se já existe uma URL gravada, redireciona para o PDF da etiqueta
        if (!empty($order->url_etiqueta)) {
            return redirect()->away($order->url_etiqueta);
        }

        // Tenta pegar o serviço salvo no pedido, se não houver, usa 2 (SEDEX) como fallback
        $serviceId = $order->shipping_service_id ?? 2;

        try {
            $resultado = $this->melhorEnvio->gerarEtiquetaCompleta($order, $serviceId);

            if ($resultado['sucesso']) {
                // Atualiza o pedido com os dados do Melhor Envio
                $order->update([
                    'status'          => 'enviado', // Alterado para 'enviado' para alinhar com o painel
                    'url_etiqueta'    => $resultado['url_etiqueta'],
                    'melhor_envio_id' => $resultado['order_id_me'],
                    'codigo_rastreio' => $resultado['tracking_code'] ?? null
                ]);

                // --- INÍCIO: DISPARO DE E-MAIL AO CLIENTE ---
                if ($order->codigo_rastreio) {
                    // Se tiver código de rastreio, envia o e-mail completo de rastreamento
                    try {
                        $order->user->notify(new OrderTrackingCode($order));
                    } catch (\Exception $e) {
                        Log::error("Erro Notificação Rastreio: " . $e->getMessage());
                    }
                } else {
                    // Se por algum motivo o rastreio não veio, mas a etiqueta gerou, avisa que o status mudou
                    try {
                        $order->user->notify(new OrderStatusUpdated($order));
                    } catch (\Exception $e) {
                        Log::error("Erro Notificação Status Etiqueta: " . $e->getMessage());
                    }
                }
                // --- FIM: DISPARO DE E-MAIL AO CLIENTE ---

                return redirect()->away($resultado['url_etiqueta']);
            }

            // --- TRATAMENTO PROFISSIONAL E AMIGÁVEL DOS ERROS ---

            Log::error("Falha ao gerar etiqueta para o Pedido #{$id}. Erro retornado: " . json_encode($resultado['erro']));

            $mensagemErro = 'Não foi possível emitir a etiqueta junto ao Melhor Envio. Verifique o que está incorreto: ';
            $detalhes = [];
            $erroTratado = null;

            // Dicionário de tradução dos campos técnicos da API
            $tradutorCampos = [
                'from.document'     => 'CPF/CNPJ do Remetente (Sua Empresa)',
                'from.company_name' => 'Nome da Empresa Remetente',
                'to.document'       => 'CPF/CNPJ do Destinatário (Cliente)',
                'to.postal_code'    => 'CEP do Destinatário (Cliente)',
                'to.address'        => 'Endereço do Destinatário',
                'to.number'         => 'Número do Endereço',
                'service'           => 'Serviço de Entrega (Transportadora)',
                'volumes'           => 'Dados de Peso/Dimensões do pacote',
            ];

            // Força a conversão do erro para array caso venha como objeto ou string JSON mascarada
            if (is_string($resultado['erro'])) {
                if (str_contains($resultado['erro'], '{')) {
                    $posicaoJson = strpos($resultado['erro'], '{');
                    $jsonPuro = substr($resultado['erro'], $posicaoJson);
                    $erroTratado = json_decode($jsonPuro, true);
                }
            } else {
                $erroTratado = (array) $resultado['erro'];
            }

            if ($erroTratado && (isset($erroTratado['errors']) || isset($erroTratado['message']) || isset($erroTratado['error']))) {
                if (isset($erroTratado['errors']) && is_array($erroTratado['errors'])) {
                    foreach ($erroTratado['errors'] as $campoTecnico => $mensagens) {
                        $nomeAmigavel = $tradutorCampos[$campoTecnico] ?? $campoTecnico;
                        $textoErro = $mensagens[0] ?? 'Dado inválido.';

                        // Limpeza fina dos textos internos que a API devolve
                        $textoErro = str_replace(
                            ['from.document', 'to.document', 'to.postal_code', 'O to.postal code', 'postal code'],
                            ['CPF/CNPJ', 'CPF/CNPJ', 'CEP', 'O CEP', 'CEP'],
                            $textoErro
                        );

                        $detalhes[] = "<strong>{$nomeAmigavel}</strong>: {$textoErro}";
                    }
                } elseif (isset($erroTratado['error'])) {
                    $detalhes[] = $erroTratado['error'];
                } elseif (isset($erroTratado['message'])) {
                    $detalhes[] = $erroTratado['message'];
                }
            } else {
                $erroString = (string) $resultado['erro'];
                if (str_contains($erroString, 'Transportadora nao atende') || str_contains($erroString, 'trecho')) {
                    $detalhes[] = 'A transportadora selecionada não atende ao trecho configurado para este CEP.';
                } else {
                    $detalhes[] = $erroString;
                }
            }

            if (!empty($detalhes)) {
                $mensagemErro .= implode(' | ', $detalhes);
            }

            return redirect()->back()->with('error', $mensagemErro);

        } catch (\Exception $e) {
            Log::error("Erro crítico ao emitir etiqueta do Pedido #{$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocorreu um erro interno de comunicação ao processar o seu envio.');
        }
    }
}
