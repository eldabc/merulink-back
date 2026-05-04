<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\DB;

class EventContactService
{
    public function syncContacts(Event $event, array $contactsData)
    {
            
        $event->contacts()->delete(); // Limpiar los contactos previos

        foreach ($contactsData as $cData) {
            // Crear el contacto
            $contact = $event->contacts()->create($cData); 

            // Registrar los teléfonos usando la relación polimórfica
            if (!empty($cData['phones'])) {
                foreach ($cData['phones'] as $pData) {
                    // Laravel detecta automáticamente el phoneable_id y phoneable_type
                    $contact->phones()->create([
                        'phone_number' => $pData['phone_number']
                    ]);
                }
            }
        }
    }
}