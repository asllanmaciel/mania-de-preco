<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contas', function (Blueprint $table) {
            $table->string('billing_provider')->nullable()->after('trial_ends_at');
            $table->string('billing_customer_id')->nullable()->after('billing_provider');
            $table->timestamp('billing_synced_at')->nullable()->after('billing_customer_id');
            $table->json('billing_metadata')->nullable()->after('billing_synced_at');
        });

        Schema::table('assinaturas', function (Blueprint $table) {
            $table->string('billing_provider')->nullable()->after('observacoes');
            $table->string('billing_subscription_id')->nullable()->after('billing_provider');
            $table->string('billing_checkout_url')->nullable()->after('billing_subscription_id');
            $table->string('billing_status')->nullable()->after('billing_checkout_url');
            $table->timestamp('billing_last_synced_at')->nullable()->after('billing_status');
            $table->json('billing_payload')->nullable()->after('billing_last_synced_at');
        });
    }

    public function down(): void
    {
        Schema::table('assinaturas', function (Blueprint $table) {
            $table->dropColumn([
                'billing_provider',
                'billing_subscription_id',
                'billing_checkout_url',
                'billing_status',
                'billing_last_synced_at',
                'billing_payload',
            ]);
        });

        Schema::table('contas', function (Blueprint $table) {
            $table->dropColumn([
                'billing_provider',
                'billing_customer_id',
                'billing_synced_at',
                'billing_metadata',
            ]);
        });
    }
};
