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
        Schema::create('training_predictions', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->integer('usia');
            $table->integer('paritas');
            $table->decimal('beratbadan', 5, 2);
            $table->integer('tinggibadan');
            $table->decimal('imt', 5, 2);
            $table->integer('sistolik');
            $table->integer('diastolik');
            $table->decimal('map', 5, 2)->nullable();
            $table->integer('gds');
            $table->string('protein_urine')->nullable();
            $table->string('diagnosis')->nullable(); // status asli jika ada
            $table->string('prediksi_knn');
            $table->decimal('confidence', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_predictions');
    }
};
