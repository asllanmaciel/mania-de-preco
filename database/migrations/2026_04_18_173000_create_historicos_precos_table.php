<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historicos_precos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preco_id')->nullable()->constrained('precos')->nullOnDelete();
            $table->foreignId('produto_id')->nullable()->constrained('produtos')->nullOnDelete();
            $table->string('produto_nome');
            $table->foreignId('loja_id')->nullable()->constrained('lojas')->nullOnDelete();
            $table->string('loja_nome');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo_preco', 30)->nullable();
            $table->string('url_produto')->nullable();
            $table->enum('evento', ['criado', 'atualizado', 'removido']);
            $table->decimal('preco_anterior', 10, 2)->nullable();
            $table->decimal('preco_atual', 10, 2)->nullable();
            $table->decimal('variacao_valor', 10, 2)->nullable();
            $table->decimal('variacao_percentual', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['produto_id', 'created_at']);
            $table->index(['loja_id', 'created_at']);
            $table->index(['evento', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historicos_precos');
    }
};
