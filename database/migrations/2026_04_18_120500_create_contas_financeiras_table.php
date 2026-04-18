<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contas_financeiras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conta_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loja_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nome');
            $table->enum('tipo', ['caixa', 'banco', 'cartao', 'carteira_digital'])->default('caixa');
            $table->string('instituicao')->nullable();
            $table->string('agencia')->nullable();
            $table->string('numero')->nullable();
            $table->decimal('saldo_inicial', 12, 2)->default(0);
            $table->decimal('saldo_atual', 12, 2)->default(0);
            $table->boolean('ativa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contas_financeiras');
    }
};
