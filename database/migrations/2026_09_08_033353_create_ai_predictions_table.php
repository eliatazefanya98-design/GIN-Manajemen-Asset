<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_predictions', function (Blueprint $table) {
        $table->id();

        $table->foreignId('asset_id')
            ->constrained('assets')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();

        $table->boolean('prediction');

        $table->decimal('risk_score', 5, 4);

        $table->string('risk_category');

        $table->json('factors')->nullable();

        $table->text('recommendation')->nullable();

        $table->string('model_name')->nullable();

        $table->string('model_version')->nullable();

        $table->timestamp('predicted_at');

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_predictions');
    }
};
