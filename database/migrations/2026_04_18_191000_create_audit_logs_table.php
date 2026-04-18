<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conta_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('area', 80);
            $table->string('acao', 80);
            $table->string('entidade_tipo')->nullable();
            $table->unsignedBigInteger('entidade_id')->nullable();
            $table->string('descricao');
            $table->json('metadados')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 1024)->nullable();
            $table->timestamps();

            $table->index(['conta_id', 'area']);
            $table->index(['conta_id', 'acao']);
            $table->index(['entidade_tipo', 'entidade_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
