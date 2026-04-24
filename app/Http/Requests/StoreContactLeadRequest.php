<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactLeadRequest extends FormRequest
{
    /**
     * Cualquiera puede enviar este formulario (ruta pública).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación del formulario de contacto.
     */
    public function rules(): array
    {
        return [
            // Información del padre/tutor
            'parent_name'      => ['required', 'string', 'min:3', 'max:255'],
            'parent_email'     => ['required', 'email', 'max:255'],
            'parent_phone'     => ['required', 'string', 'min:8', 'max:30'],

            // Información del niño
            'child_name'       => ['required', 'string', 'min:2', 'max:255'],
            'child_age'        => ['required', 'integer', 'min:1', 'max:18'],
            'child_diagnosis'  => ['required', 'string', 'max:100'],

            // Señales de alerta (cuando no tiene diagnóstico)
            'alert_signs'      => ['nullable', 'array', 'max:3'],
            'alert_signs.*'    => ['nullable', 'string', 'max:255'],

            // Objetivos y origen
            'goals'            => ['required', 'string', 'min:10'],
            'how_found_us'     => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Mensajes de error personalizados en español.
     */
    public function messages(): array
    {
        return [
            'parent_name.required'     => 'El nombre del padre/tutor es requerido.',
            'parent_name.min'          => 'El nombre debe tener al menos 3 caracteres.',
            'parent_email.required'    => 'El correo electrónico es requerido.',
            'parent_email.email'       => 'Ingresa un correo electrónico válido.',
            'parent_phone.required'    => 'El teléfono es requerido.',
            'parent_phone.min'         => 'El teléfono debe tener al menos 8 dígitos.',
            'child_name.required'      => 'El nombre del niño es requerido.',
            'child_age.required'       => 'La edad del niño es requerida.',
            'child_age.min'            => 'La edad mínima es 1 año.',
            'child_age.max'            => 'La edad máxima es 18 años.',
            'child_diagnosis.required' => 'El diagnóstico es requerido.',
            'goals.required'           => 'Los objetivos son requeridos.',
            'goals.min'                => 'Describe los objetivos con al menos 10 caracteres.',
        ];
    }

    /**
     * Prepara y limpia los datos antes de validar.
     */
    protected function prepareForValidation(): void
    {
        // Filtrar señales de alerta vacías
        $alertSigns = array_filter([
            $this->input('alert_sign_1'),
            $this->input('alert_sign_2'),
            $this->input('alert_sign_3'),
        ], fn ($v) => !empty(trim((string) $v)));

        $this->merge([
            'alert_signs' => !empty($alertSigns) ? array_values($alertSigns) : null,
        ]);
    }
}
