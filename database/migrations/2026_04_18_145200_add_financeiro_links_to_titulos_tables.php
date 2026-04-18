<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->foreignId('conta_financeira_id')
                ->nullable()
                ->after('loja_id')
                ->constrained('contas_financeiras')
                ->nullOnDelete();

            $table->foreignId('movimentacao_financeira_id')
                ->nullable()
                ->after('conta_financeira_id')
                ->constrained('movimentacoes_financeiras')
                ->nullOnDelete();
        });

        Schema::table('contas_receber', function (Blueprint $table) {
            $table->foreignId('conta_financeira_id')
                ->nullable()
                ->after('loja_id')
                ->constrained('contas_financeiras')
                ->nullOnDelete();

            $table->foreignId('movimentacao_financeira_id')
                ->nullable()
                ->after('conta_financeira_id')
                ->constrained('movimentacoes_financeiras')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contas_receber', function (Blueprint $table) {
            $table->dropConstrainedForeignId('movimentacao_financeira_id');
            $table->dropConstrainedForeignId('conta_financeira_id');
        });

        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->dropConstrainedForeignId('movimentacao_financeira_id');
            $table->dropConstrainedForeignId('conta_financeira_id');
        });
    }
};
