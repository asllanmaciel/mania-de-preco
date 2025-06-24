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
    Schema::create('lojas', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('cnpj')->unique()->nullable();
        $table->string('telefone')->nullable();
        $table->string('whatsapp')->nullable();
        $table->string('email')->nullable();
        $table->string('site')->nullable();
        $table->string('instagram')->nullable();
        $table->string('facebook')->nullable();
        $table->string('endereco')->nullable();
        $table->string('numero')->nullable();
        $table->string('bairro')->nullable();
        $table->string('cidade')->nullable();
        $table->string('uf', 2)->nullable();
        $table->string('cep')->nullable();
        $table->decimal('latitude', 10, 8)->nullable();
        $table->decimal('longitude', 11, 8)->nullable();
        $table->enum('tipo_loja', ['fisica', 'online', 'mista'])->default('fisica');
        $table->enum('status', ['ativo', 'inativo'])->default('ativo');
        $table->string('logo')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lojas');
    }
};
