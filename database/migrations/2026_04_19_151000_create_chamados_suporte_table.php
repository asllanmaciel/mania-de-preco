<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chamados_suporte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conta_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('protocolo')->unique();
            $table->string('nome');
            $table->string('email');
            $table->string('telefone')->nullable();
            $table->string('empresa')->nullable();
            $table->string('categoria', 60)->default('outros');
            $table->string('prioridade', 40)->default('normal');
            $table->string('status', 40)->default('novo');
            $table->string('assunto');
            $table->text('mensagem');
            $table->string('origem_url')->nullable();
            $table->text('observacao_interna')->nullable();
            $table->timestamp('respondido_em')->nullable();
            $table->timestamp('resolvido_em')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 1024)->nullable();
            $table->timestamps();

            $table->index(['status', 'prioridade']);
            $table->index(['categoria', 'status']);
            $table->index(['conta_id', 'status']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chamados_suporte');
    }
};
