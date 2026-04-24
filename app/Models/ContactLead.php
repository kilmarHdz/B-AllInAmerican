<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactLead extends Model
{
    use HasFactory;

    /**
     * Campos que se pueden asignar de forma masiva.
     */
    protected $fillable = [
        'parent_name',
        'parent_email',
        'parent_phone',
        'child_name',
        'child_age',
        'child_diagnosis',
        'alert_signs',
        'goals',
        'how_found_us',
        'status',
        'notes',
    ];

    /**
     * Casts de tipos de datos.
     */
    protected $casts = [
        'alert_signs' => 'array',
        'child_age'   => 'integer',
    ];

    /**
     * Valores por defecto.
     */
    protected $attributes = [
        'status' => 'new',
    ];
}
