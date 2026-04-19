<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacao_interacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conta_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contexto', 40);
            $table->string('escopo', 80);
            $table->string('chave', 160);
            $table->timestamp('lida_em')->nullable();
            $table->timestamp('dispensada_ate')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'contexto', 'escopo', 'chave'], 'notificacao_interacoes_unique');
            $table->index(['conta_id', 'contexto']);
            $table->index(['dispensada_ate', 'lida_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacao_interacoes');
    }
};
