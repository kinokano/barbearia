<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained('professionals')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 0 = Domingo, 6 = Sábado
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            $table->unique(['professional_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
