<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->decimal('beratbadan', 5, 2)->nullable()->after('diastolic_bp');
            $table->integer('tinggibadan')->nullable()->after('beratbadan');
            $table->decimal('imt', 5, 2)->nullable()->after('tinggibadan');
            $table->decimal('hb', 4, 2)->nullable()->after('protein_urine');
            $table->integer('gds')->nullable()->after('hb');
            $table->boolean('riw_ht_keluarga')->default(false)->after('gds');
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn(['beratbadan', 'tinggibadan', 'imt', 'hb', 'gds', 'riw_ht_keluarga']);
        });
    }
};
