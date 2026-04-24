<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabla para guardar los leads del formulario de contacto.
     */
    public function up(): void
    {
        Schema::create('contact_leads', function (Blueprint $table) {
            $table->id();

            // ─── Información del padre/tutor ───────────────────────
            $table->string('parent_name');
            $table->string('parent_email');
            $table->string('parent_phone', 30);

            // ─── Información del niño ───────────────────────────────
            $table->string('child_name');
            $table->unsignedTinyInteger('child_age');
            $table->string('child_diagnosis');

            // ─── Señales de alerta (solo si no tiene diagnóstico) ───
            $table->json('alert_signs')->nullable();

            // ─── Objetivos y origen ─────────────────────────────────
            $table->text('goals');
            $table->string('how_found_us')->nullable();

            // ─── Gestión interna ────────────────────────────────────
            $table->enum('status', ['new', 'contacted', 'enrolled'])->default('new');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_leads');
    }
};
