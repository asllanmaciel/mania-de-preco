<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contas', function (Blueprint $table) {
            $table->string('site')->nullable()->after('telefone');
            $table->string('instagram')->nullable()->after('site');
            $table->string('segmento')->nullable()->after('instagram');
            $table->string('porte')->nullable()->after('segmento');
            $table->string('endereco')->nullable()->after('porte');
            $table->string('numero', 50)->nullable()->after('endereco');
            $table->string('bairro')->nullable()->after('numero');
            $table->string('cidade')->nullable()->after('bairro');
            $table->string('uf', 2)->nullable()->after('cidade');
            $table->string('cep', 20)->nullable()->after('uf');
            $table->string('logo')->nullable()->after('cep');
            $table->string('cor_marca', 20)->nullable()->after('logo');
            $table->text('descricao_publica')->nullable()->after('cor_marca');
            $table->string('timezone')->nullable()->after('descricao_publica');
            $table->json('preferencias')->nullable()->after('timezone');
        });
    }

    public function down(): void
    {
        Schema::table('contas', function (Blueprint $table) {
            $table->dropColumn([
                'site',
                'instagram',
                'segmento',
                'porte',
                'endereco',
                'numero',
                'bairro',
                'cidade',
                'uf',
                'cep',
                'logo',
                'cor_marca',
                'descricao_publica',
                'timezone',
                'preferencias',
            ]);
        });
    }
};
