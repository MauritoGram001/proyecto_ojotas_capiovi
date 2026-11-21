<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->string('talle', 30)->index();
            $table->string('color', 50)->index();
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();

            $table->index(['producto_id', 'talle', 'color']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variantes');
    }
};
