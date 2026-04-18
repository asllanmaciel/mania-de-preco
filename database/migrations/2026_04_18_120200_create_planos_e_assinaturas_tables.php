<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->text('descricao')->nullable();
            $table->decimal('valor_mensal', 10, 2)->default(0);
            $table->decimal('valor_anual', 10, 2)->default(0);
            $table->unsignedInteger('limite_usuarios')->nullable();
            $table->unsignedInteger('limite_lojas')->nullable();
            $table->unsignedInteger('limite_produtos')->nullable();
            $table->json('recursos')->nullable();
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->timestamps();
        });

        Schema::create('assinaturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conta_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plano_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['trial', 'ativa', 'inadimplente', 'cancelada', 'encerrada'])->default('trial');
            $table->enum('ciclo_cobranca', ['mensal', 'anual'])->default('mensal');
            $table->decimal('valor', 10, 2)->default(0);
            $table->date('inicia_em');
            $table->date('expira_em')->nullable();
            $table->timestamp('cancelada_em')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assinaturas');
        Schema::dropIfExists('planos');
    }
};
