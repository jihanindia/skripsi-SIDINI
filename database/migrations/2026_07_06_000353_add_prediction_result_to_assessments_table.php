<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->string('prediction_result')->nullable()->after('gds');
        });

        // Sinkronisasi data lama dari tabel assessment_results
        DB::statement("
            UPDATE assessments a
            JOIN assessment_results ar ON ar.assessment_id = a.id
            SET a.prediction_result = CASE
                WHEN ar.risk_category IN ('high_risk', 'severe_preeclampsia') THEN 'Preeklampsia'
                ELSE 'Normal'
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('prediction_result');
        });
    }
};
