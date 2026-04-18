<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alertas_precos', function (Blueprint $table) {
            $table->foreignId('loja_id_referencia')->nullable()->after('produto_id')->constrained('lojas')->nullOnDelete();
            $table->decimal('preco_base', 10, 2)->nullable()->after('preco_desejado');
            $table->decimal('ultimo_preco_menor', 10, 2)->nullable()->after('preco_base');
            $table->decimal('menor_preco_historico', 10, 2)->nullable()->after('ultimo_preco_menor');
            $table->decimal('variacao_desde_ativacao', 10, 2)->nullable()->after('menor_preco_historico');
            $table->decimal('variacao_percentual_desde_ativacao', 10, 2)->nullable()->after('variacao_desde_ativacao');
            $table->timestamp('disparado_em')->nullable()->after('status');
            $table->timestamp('ultima_avaliacao_em')->nullable()->after('disparado_em');
        });
    }

    public function down(): void
    {
        Schema::table('alertas_precos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('loja_id_referencia');
            $table->dropColumn([
                'preco_base',
                'ultimo_preco_menor',
                'menor_preco_historico',
                'variacao_desde_ativacao',
                'variacao_percentual_desde_ativacao',
                'disparado_em',
                'ultima_avaliacao_em',
            ]);
        });
    }
};
