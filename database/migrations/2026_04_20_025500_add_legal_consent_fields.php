<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('termos_aceitos_em')->nullable()->after('remember_token');
            $table->string('termos_versao', 20)->nullable()->after('termos_aceitos_em');
            $table->string('privacidade_versao', 20)->nullable()->after('termos_versao');
            $table->string('consentimento_ip', 45)->nullable()->after('privacidade_versao');
            $table->string('consentimento_user_agent', 1024)->nullable()->after('consentimento_ip');
        });

        Schema::table('chamados_suporte', function (Blueprint $table) {
            $table->timestamp('termos_aceitos_em')->nullable()->after('user_agent');
            $table->string('termos_versao', 20)->nullable()->after('termos_aceitos_em');
            $table->string('privacidade_versao', 20)->nullable()->after('termos_versao');
        });
    }

    public function down(): void
    {
        Schema::table('chamados_suporte', function (Blueprint $table) {
            $table->dropColumn([
                'termos_aceitos_em',
                'termos_versao',
                'privacidade_versao',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'termos_aceitos_em',
                'termos_versao',
                'privacidade_versao',
                'consentimento_ip',
                'consentimento_user_agent',
            ]);
        });
    }
};
