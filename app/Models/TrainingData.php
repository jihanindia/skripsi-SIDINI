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
        'riw_ht_keluarga',
        'hb',
        'gds',
        'protein_urine',
        'diagnosis',
        'prediksi_knn',
    ];

    protected $casts = [
        'riw_ht_keluarga' => 'string',
        'protein_urine' => 'string',
        'usia' => 'integer',
        'paritas' => 'integer',
        'beratbadan' => 'float',
        'tinggibadan' => 'integer',
        'imt' => 'float',
        'sistolik' => 'integer',
        'diastolik' => 'integer',
        'hb' => 'float',
        'gds' => 'integer',
    ];
}
