<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimentacoes_financeiras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conta_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loja_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('conta_financeira_id')->constrained('contas_financeiras')->cascadeOnDelete();
            $table->foreignId('categoria_financeira_id')->nullable()->constrained('categorias_financeiras')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('tipo', ['receita', 'despesa', 'transferencia'])->default('despesa');
            $table->enum('origem', ['manual', 'venda', 'pagamento', 'ajuste'])->default('manual');
            $table->string('descricao');
            $table->decimal('valor', 12, 2);
            $table->dateTime('data_movimentacao');
            $table->enum('status', ['prevista', 'realizada', 'cancelada'])->default('realizada');
            $table->text('observacoes')->nullable();
            $table->json('metadados')->nullable();
            $table->timestamps();

            $table->index(['conta_id', 'data_movimentacao']);
            $table->index(['conta_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimentacoes_financeiras');
    }
};
