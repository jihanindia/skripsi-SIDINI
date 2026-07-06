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
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn([
                'gestational_age',
                'gravida',
                'multiple_pregnancy',
                'heart_rate',
                'temperature',
                'platelets',
                'sgot',
                'sgpt',
                'creatinine',
                'uric_acid',
                'severe_headache',
                'blurred_vision',
                'epigastric_pain',
                'nausea_vomiting',
                'facial_edema',
                'hand_edema',
                'decreased_consciousness',
                'seizures',
                'chronic_hypertension',
                'previous_preeclampsia',
                'diabetes',
                'kidney_disease',
                'obesity',
                'autoimmune_disease',
                'family_history_preeclampsia',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            // Pregnancy Information
            $table->integer('gravida')->nullable()->after('user_id');
            $table->integer('gestational_age')->nullable()->after('gravida');
            $table->boolean('multiple_pregnancy')->default(false)->after('gestational_age');

            // Vital Signs
            $table->integer('heart_rate')->nullable()->after('diastolic_bp');
            $table->decimal('temperature', 4, 1)->nullable()->after('heart_rate');

            // Laboratory Results
            $table->integer('platelets')->nullable()->after('protein_urine');
            $table->integer('sgot')->nullable()->after('platelets');
            $table->integer('sgpt')->nullable()->after('sgot');
            $table->decimal('creatinine', 4, 2)->nullable()->after('sgpt');
            $table->decimal('uric_acid', 4, 2)->nullable()->after('creatinine');

            // Clinical Symptoms
            $table->boolean('severe_headache')->default(false)->after('uric_acid');
            $table->boolean('blurred_vision')->default(false)->after('severe_headache');
            $table->boolean('epigastric_pain')->default(false)->after('blurred_vision');
            $table->boolean('nausea_vomiting')->default(false)->after('epigastric_pain');
            $table->boolean('facial_edema')->default(false)->after('nausea_vomiting');
            $table->boolean('hand_edema')->default(false)->after('facial_edema');
            $table->boolean('decreased_consciousness')->default(false)->after('hand_edema');
            $table->boolean('seizures')->default(false)->after('decreased_consciousness');

            // Risk History
            $table->boolean('chronic_hypertension')->default(false)->after('seizures');
            $table->boolean('previous_preeclampsia')->default(false)->after('chronic_hypertension');
            $table->boolean('diabetes')->default(false)->after('previous_preeclampsia');
            $table->boolean('kidney_disease')->default(false)->after('diabetes');
            $table->boolean('obesity')->default(false)->after('kidney_disease');
            $table->boolean('autoimmune_disease')->default(false)->after('obesity');
            $table->boolean('family_history_preeclampsia')->default(false)->after('autoimmune_disease');
        });
    }
};
