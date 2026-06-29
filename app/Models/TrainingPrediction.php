<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingPrediction extends Model
{
    protected $table = 'training_predictions';

    protected $fillable = [
        'nama',
        'usia',
        'paritas',
        'tinggibadan',
        'beratbadan',
        'imt',
        'sistolik',
        'diastolik',
        'map',
        'gds',
        'protein_urine',
        'diagnosis',
        'prediksi_knn',
        'confidence',
    ];
}
