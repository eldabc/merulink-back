<?php 
    namespace App\Traits;

    use App\Models\History;
    use Illuminate\Support\Facades\Auth;

    trait HasHistory
    {
        /**
         * Acciones que NO son idempotentes.
         * Un modelo puede sobrescribir esta constante.
         */
        protected const NON_IDEMPOTENT_ACTIONS = ['viewed'];

        /**
         * Relación Polimórfica de Historiales.
         */
        public function histories()
        {
            return $this->morphMany(History::class, 'auditable')->latest();
        }

        /**
         * Método helper para registrar una acción en el historial.
         */
        public function recordHistory(string $action, ?string $description = null, ?array $payload = null, ?int $userId = null): ?History
        {
            // Si modelo tiene un campo status y está cerrado, no registra
            if (isset($this->status) && $this->status === 'closed') {
                return null;
            }

            return $this->histories()->create([
                'user_id'     => $userId ?? Auth::id(),
                'action'      => $action,
                'description' => $description,
                'payload'     => $payload,
            ]);
        }


        /**
         * Registra la acción solo si no es idéntica a la última registrada
         * (idempotencia: evita duplicar "editado", "revisado", "aprobado"...).
         *
         * Excepción: NON_IDEMPOTENT_ACTIONS se registran siempre.
         */
        public function recordHistoryOnce(string $action, ?string $description = null, ?array $payload = null, ?int $userId = null): ?History
        {
            if (!in_array($action, static::NON_IDEMPOTENT_ACTIONS, true)) {
                $last = $this->histories()->reorder('id', 'desc')->first();

                if ($last && $last->action === $action) {
                    return null;
                }
            }

            return $this->recordHistory($action, $description, $payload, $userId);
        }
    }