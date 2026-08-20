<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditObserver
{
    /**
     * Captura quando um registro é criado
     */
    public function created(Model $model): void
    {
        $this->logAction($model, 'create', null, $model->getAttributes());
    }

    /**
     * Captura quando um registro é atualizado (e guarda apenas o que mudou de verdade)
     */
    public function updated(Model $model): void
    {
        // Pega apenas as colunas que mudaram
        $dirty = $model->getDirty();

        $old = [];
        $new = [];

        foreach ($dirty as $key => $value) {
            // Ignora colunas de data de atualização nas diferenças de log
            if (in_array($key, ['updated_at', 'remember_token'])) continue;

            $old[$key] = $model->getOriginal($key);
            $new[$key] = $value;
        }

        // Se nada relevante mudou, não gera log
        if (empty($new)) return;

        $this->logAction($model, 'update', $old, $new);
    }

    /**
     * Captura quando um registro é deletado
     */
    public function deleted(Model $model): void
    {
        $this->logAction($model, 'delete', $model->getAttributes(), null);
    }

    /**
     * Salva o Log na Tabela
     */
    private function logAction(Model $model, string $action, ?array $oldValues, ?array $newValues): void
    {
        $user = Auth::user();
        $userName = $user ? $user->name : 'Sistema/Webhook';

        // Descobre o nome do modelo limpo (ex: "Product" em vez de "App\Models\Product")
        $className = class_basename($model);

        // Tenta pegar alguma propriedade identificável do modelo (nome, título ou ID)
        $identifier = $model->name ?? $model->title ?? $model->id;

        // Monta a frase amigável para o Admin ler
        $description = sprintf(
            'Usuário [%s] realizou a ação [%s] no registro [%s] (%s)',
            $userName,
            strtoupper($action),
            $identifier,
            $className
        );

        AuditLog::create([
            'user_id'        => $user ? $user->id : null,
            'action'         => $action,
            'auditable_type' => get_class($model),
            'auditable_id'   => $model->id,
            'description'    => $description,
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'ip_address'     => Request::ip(),
            'user_agent'     => Request::userAgent(),
        ]);
    }
}
