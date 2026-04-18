<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contas', function (Blueprint $table) {
            $table->id();
            $table->string('nome_fantasia');
            $table->string('slug')->unique();
            $table->string('razao_social')->nullable();
            $table->string('documento', 20)->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->enum('status', ['trial', 'ativo', 'inadimplente', 'cancelado'])->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('conta_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conta_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('papel', 30)->default('owner');
            $table->boolean('ativo')->default(true);
            $table->timestamp('ultimo_acesso_em')->nullable();
            $table->timestamps();

            $table->unique(['conta_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conta_user');
        Schema::dropIfExists('contas');
    }
};
