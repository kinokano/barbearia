<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_phone', 20);
            $table->date('client_birth_date')->nullable();
            $table->foreignId('professional_id')->constrained('professionals')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['pendente', 'agendado', 'cancelado'])->default('pendente');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['professional_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
