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
        Schema::create('assessment_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            
            // KNN Algorithm Results
            $table->enum('risk_category', [
                'no_risk',
                'low_risk',
                'moderate_risk',
                'high_risk',
                'severe_preeclampsia'
            ]);
            
            $table->decimal('risk_score', 5, 2); // KNN confidence score (0-100)
            $table->integer('k_value')->default(5); // K value used in KNN
            
            // Classification Details
            $table->enum('severity_level', ['none', 'mild', 'moderate', 'severe']);
            $table->json('supporting_indicators'); // Array of criteria met
            $table->json('knn_neighbors'); // Store nearest neighbors data
            
            // Recommendations
            $table->json('recommendations'); // Array of recommended actions
            $table->text('education_notes')->nullable();
            $table->enum('urgency_level', ['routine', 'monitor', 'urgent', 'emergency']);
            
            // Follow-up
            $table->date('next_checkup_date')->nullable();
            $table->boolean('requires_referral')->default(false);
            $table->text('referral_notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_results');
    }
};
