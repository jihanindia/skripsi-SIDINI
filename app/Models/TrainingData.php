<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingData extends Model
{
    use HasFactory;

    protected $table = 'training_data';

    protected $fillable = [
        'nama',
        'usia',
        'paritas',
        'beratbadan',
        'tinggibadan',
        'imt',
        'sistolik',
        'diastolik',
        'map',
        'gds',
        'protein_urine',
        'diagnosis',
        'prediksi_knn',
    ];

    protected $casts = [
        'protein_urine' => 'string',
        'usia' => 'integer',
        'paritas' => 'integer',
        'beratbadan' => 'float',
        'tinggibadan' => 'integer',
        'imt' => 'float',
        'sistolik' => 'integer',
        'diastolik' => 'integer',
        'map' => 'float',
        'gds' => 'integer',
    ];
}
