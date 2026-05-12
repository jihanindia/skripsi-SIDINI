<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingData extends Model
{
    use HasFactory;

    protected $table = 'training_data';

    protected $fillable = [
        'age',
        'gravida',
        'para',
        'gestational_age',
        'multiple_pregnancy',
        'systolic_bp',
        'diastolic_bp',
        'protein_urine_level',
        'platelets',
        'sgot',
        'sgpt',
        'creatinine',
        'severe_headache',
        'blurred_vision',
        'epigastric_pain',
        'edema',
        'chronic_hypertension',
        'previous_preeclampsia',
        'diabetes',
        'obesity',
        'diagnosis',
        'source',
        'is_validated',
    ];

    protected $casts = [
        'multiple_pregnancy' => 'boolean',
        'severe_headache' => 'boolean',
        'blurred_vision' => 'boolean',
        'epigastric_pain' => 'boolean',
        'edema' => 'boolean',
        'chronic_hypertension' => 'boolean',
        'previous_preeclampsia' => 'boolean',
        'diabetes' => 'boolean',
        'obesity' => 'boolean',
        'is_validated' => 'boolean',
    ];
}
