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
        Schema::create('training_data', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // <-- tambahan di sini

            // =========================
            // FITUR KNN
            // =========================
            $table->integer('usia');
            $table->integer('paritas');
            $table->decimal('beratbadan', 5, 2);
            $table->integer('tinggibadan');
            $table->decimal('imt', 5, 2); // 
            $table->integer('sistolik');
            $table->integer('diastolik');
            $table->boolean('riw_ht_keluarga');
            $table->decimal('hb', 4, 2); // 
            $table->integer('gds');
            $table->integer('protein_urine');

            // =========================
            // LABEL
            // =========================
            $table->enum('diagnosis', [
                'normal',
                'preeklampsia'
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_data');
    }
};