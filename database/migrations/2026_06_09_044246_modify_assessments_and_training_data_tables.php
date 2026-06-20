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
            $table->dropColumn(['riw_ht_keluarga', 'hb']);
            $table->decimal('map', 5, 2)->nullable()->after('diastolic_bp');
        });

        Schema::table('training_data', function (Blueprint $table) {
            $table->dropColumn(['riw_ht_keluarga', 'hb']);
            $table->decimal('map', 5, 2)->nullable()->after('diastolik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->boolean('riw_ht_keluarga')->default(false)->after('gds');
            $table->decimal('hb', 4, 2)->nullable()->after('protein_urine');
            $table->dropColumn('map');
        });

        Schema::table('training_data', function (Blueprint $table) {
            $table->string('riw_ht_keluarga')->nullable()->after('diastolik');
            $table->decimal('hb', 4, 2)->nullable()->after('riw_ht_keluarga');
            $table->dropColumn('map');
        });
    }
};
