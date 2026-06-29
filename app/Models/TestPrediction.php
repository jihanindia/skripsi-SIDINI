<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestPrediction extends Model
{
    protected $table = 'test_predictions';

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
