<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('conta_id')->nullable()->constrained()->nullOnDelete();
            $table->string('evento', 120);
            $table->string('area', 40)->default('public');
            $table->nullableMorphs('sujeito');
            $table->json('metadata')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 1024)->nullable();
            $table->timestamp('ocorreu_em')->index();
            $table->timestamps();

            $table->index(['evento', 'ocorreu_em']);
            $table->index(['area', 'ocorreu_em']);
            $table->index(['conta_id', 'evento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
