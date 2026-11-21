<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cobros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha');
            $table->decimal('monto_efectivo', 12, 2)->default(0);
            $table->decimal('monto_transferencia', 12, 2)->default(0);
            $table->decimal('monto_cheque', 12, 2)->default(0);
            $table->string('numero_cheque', 50)->nullable();
            $table->decimal('total', 12, 2);
            $table->timestamps();

            $table->index(['cliente_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cobros');
    }
};
