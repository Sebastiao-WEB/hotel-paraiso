<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Cria a tabela de estadias diretas (walk-in check-ins)
     * Armazena check-ins realizados sem reserva prévia
     */
    public function up(): void
    {
        Schema::create('stays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained('clientes')->onDelete('cascade')->comment('ID do hóspede/cliente');
            $table->foreignId('room_id')->constrained('quartos')->onDelete('cascade')->comment('ID do quarto');
            $table->datetime('check_in_at')->comment('Data e hora exata da entrada');
            $table->datetime('expected_check_out_at')->comment('Data e hora prevista de saída');
            $table->datetime('actual_check_out_at')->nullable()->comment('Data e hora real de saída (null se ainda ativo)');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active')->comment('Status da estadia');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('Funcionário que registrou o check-in');
            $table->foreignId('checked_out_by')->nullable()->constrained('users')->onDelete('set null')->comment('Funcionário que realizou o check-out');
            $table->decimal('total_amount', 10, 2)->default(0)->comment('Valor total da estadia');
            $table->enum('payment_type', ['dinheiro', 'cartao'])->nullable()->comment('Tipo de pagamento');
            $table->text('notes')->nullable()->comment('Observações adicionais');
            $table->timestamps();
            
            // Índices para melhor performance
            $table->index('status');
            $table->index('check_in_at');
            $table->index('expected_check_out_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stays');
    }
};
