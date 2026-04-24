<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactLeadRequest;
use App\Mail\NewContactLeadMail;
use App\Models\ContactLead;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Guardar un nuevo lead del formulario de contacto.
     *
     * POST /api/v1/contact
     *
     * @param StoreContactLeadRequest $request
     * @return JsonResponse
     */
    public function store(StoreContactLeadRequest $request): JsonResponse
    {
        $lead = ContactLead::create([
            'parent_name'     => $request->parent_name,
            'parent_email'    => $request->parent_email,
            'parent_phone'    => $request->parent_phone,
            'child_name'      => $request->child_name,
            'child_age'       => $request->child_age,
            'child_diagnosis' => $request->child_diagnosis,
            'alert_signs'     => $request->alert_signs,
            'goals'           => $request->goals,
            'how_found_us'    => $request->how_found_us,
        ]);

        // Enviar notificación por email al equipo
        $notificationEmail = config('mail.notification_address');

        if ($notificationEmail) {
            try {
                Mail::to($notificationEmail)->send(new NewContactLeadMail($lead));
            } catch (\Throwable $e) {
                Log::error('Error al enviar notificación de lead: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => '¡Gracias! Hemos recibido tu solicitud. Nos pondremos en contacto contigo pronto.',
            'data'    => [
                'id' => $lead->id,
            ],
        ], 201);
    }
}
