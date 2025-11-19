<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('quarto_id')->constrained('quartos')->onDelete('cascade');
            $table->date('data_entrada');
            $table->date('data_saida');
            $table->enum('status', ['pendente', 'confirmada', 'cancelada', 'checkin', 'checkout'])->default('pendente');
            $table->decimal('valor_total', 10, 2)->default(0);
            $table->enum('tipo_pagamento', ['dinheiro', 'cartao'])->nullable();
            $table->foreignId('criado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('checkin_por')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('checkout_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmado_em')->nullable();
            $table->timestamp('checkin_em')->nullable();
            $table->timestamp('checkout_em')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
