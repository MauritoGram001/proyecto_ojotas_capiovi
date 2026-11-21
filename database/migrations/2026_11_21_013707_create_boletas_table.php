<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boletas', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete();
            $table->string('concepto')->nullable();
            $table->decimal('monto', 12, 2);
            $table->decimal('saldo', 12, 2);
            $table->string('estado', 20)->default('Pendiente'); // Pendiente, Parcial, Pagada
            $table->date('fecha');
            $table->timestamps();

            $table->index(['cliente_id', 'estado']);
            $table->index(['cliente_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boletas');
    }
};
