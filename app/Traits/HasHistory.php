<?php 
    namespace App\Traits;

    use App\Models\History;
    use Illuminate\Support\Facades\Auth;

    trait HasHistory
    {
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
        public function recordHistory(string $action, ?string $description = null, ?array $payload = null, ?int $userId = null): History
        {
            return $this->histories()->create([
                'user_id'     => $userId ?? Auth::id(),
                'action'      => $action,
                'description' => $description,
                'payload'     => $payload,
            ]);
        }
    }