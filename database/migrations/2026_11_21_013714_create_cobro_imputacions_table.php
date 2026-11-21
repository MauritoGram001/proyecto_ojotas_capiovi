<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cobro_imputaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cobro_id')->constrained('cobros')->cascadeOnDelete();
            $table->foreignId('boleta_id')->constrained('boletas')->cascadeOnDelete();
            $table->decimal('monto', 12, 2);
            $table->timestamps();

            $table->index(['boleta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cobro_imputaciones');
    }
};
