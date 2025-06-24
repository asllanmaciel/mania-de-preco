<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('planos_assinatura', function (Blueprint $table) {
        $table->id();
        $table->foreignId('loja_id')->constrained()->onDelete('cascade');
        $table->enum('nome_plano', ['gratis', 'basico', 'premium', 'top'])->default('gratis');
        $table->decimal('valor', 10, 2)->default(0);
        $table->date('validade')->nullable();
        $table->enum('status', ['ativo', 'expirado', 'cancelado'])->default('ativo');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planos_assinatura');
    }
};
