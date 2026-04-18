<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contas_receber', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conta_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loja_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('categoria_financeira_id')->nullable()->constrained('categorias_financeiras')->nullOnDelete();
            $table->string('cliente_nome')->nullable();
            $table->string('descricao');
            $table->decimal('valor_total', 12, 2);
            $table->decimal('valor_recebido', 12, 2)->default(0);
            $table->date('vencimento');
            $table->date('recebimento_previsto_em')->nullable();
            $table->dateTime('recebido_em')->nullable();
            $table->enum('status', ['aberta', 'parcial', 'recebida', 'vencida', 'cancelada'])->default('aberta');
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['conta_id', 'status']);
            $table->index(['conta_id', 'vencimento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contas_receber');
    }
};
