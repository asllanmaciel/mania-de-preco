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
    Schema::create('precos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('produto_id')->constrained()->onDelete('cascade');
        $table->foreignId('loja_id')->constrained()->onDelete('cascade');
        $table->decimal('preco', 10, 2);
        $table->enum('tipo_preco', ['dinheiro', 'pix', 'boleto', 'cartao', 'parcelado'])->default('dinheiro');
        $table->string('url_produto')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precos');
    }
};
