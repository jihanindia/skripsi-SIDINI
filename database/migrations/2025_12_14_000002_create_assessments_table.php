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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who performed the assessment
            
            // Pregnancy Information
            $table->integer('gravida'); // Number of pregnancies
            $table->integer('para'); // Number of births
            $table->integer('gestational_age'); // In weeks
            $table->boolean('multiple_pregnancy')->default(false);
            
            // Vital Signs
            $table->integer('systolic_bp');
            $table->integer('diastolic_bp');
            $table->integer('heart_rate')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            
            // Laboratory Results
            $table->enum('protein_urine', ['negative', 'trace', '+1', '+2', '+3', '+4'])->default('negative');
            $table->integer('platelets')->nullable(); // x1000/μL
            $table->integer('sgot')->nullable(); // U/L
            $table->integer('sgpt')->nullable(); // U/L
            $table->decimal('creatinine', 4, 2)->nullable(); // mg/dL
            $table->decimal('uric_acid', 4, 2)->nullable(); // mg/dL
            
            // Clinical Symptoms
            $table->boolean('severe_headache')->default(false);
            $table->boolean('blurred_vision')->default(false);
            $table->boolean('epigastric_pain')->default(false);
            $table->boolean('nausea_vomiting')->default(false);
            $table->boolean('facial_edema')->default(false);
            $table->boolean('hand_edema')->default(false);
            $table->boolean('decreased_consciousness')->default(false);
            $table->boolean('seizures')->default(false);
            
            // Risk History
            $table->boolean('chronic_hypertension')->default(false);
            $table->boolean('previous_preeclampsia')->default(false);
            $table->boolean('diabetes')->default(false);
            $table->boolean('kidney_disease')->default(false);
            $table->boolean('obesity')->default(false);
            $table->boolean('autoimmune_disease')->default(false);
            $table->boolean('family_history_preeclampsia')->default(false);
            
            // Additional Information
            $table->text('notes')->nullable();
            $table->timestamp('assessment_date');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
