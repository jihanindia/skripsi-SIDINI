<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $results = DB::table('assessment_results')->get();
        foreach ($results as $result) {
            $recs = json_decode($result->recommendations, true);
            if (is_array($recs)) {
                $updated = false;
                foreach ($recs as $key => $rec) {
                    if ($rec === 'Segera konsultasi dokter spesialis obstetri') {
                        $recs[$key] = 'Segera rujuk pasien ke rumah sakit';
                        $updated = true;
                    }
                }
                if ($updated) {
                    DB::table('assessment_results')
                        ->where('id', $result->id)
                        ->update(['recommendations' => json_encode($recs)]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $results = DB::table('assessment_results')->get();
        foreach ($results as $result) {
            $recs = json_decode($result->recommendations, true);
            if (is_array($recs)) {
                $updated = false;
                foreach ($recs as $key => $rec) {
                    if ($rec === 'Segera rujuk pasien ke rumah sakit') {
                        $recs[$key] = 'Segera konsultasi dokter spesialis obstetri';
                        $updated = true;
                    }
                }
                if ($updated) {
                    DB::table('assessment_results')
                        ->where('id', $result->id)
                        ->update(['recommendations' => json_encode($recs)]);
                }
            }
        }
    }
};
