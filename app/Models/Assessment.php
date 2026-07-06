<?php

namespace App\Models;

use App\Models\Concerns\ScopesAssessmentByPuskesmas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory, ScopesAssessmentByPuskesmas;

    protected $fillable = [
        'patient_id',
        'user_id',
        'puskesmas',
        'para',
        'systolic_bp',
        'diastolic_bp',
        'beratbadan',
        'tinggibadan',
        'imt',
        'protein_urine',
        'map',
        'gds',
        'prediction_result',
        'notes',
        'assessment_date',
    ];

    protected $casts = [
        'assessment_date' => 'datetime',
    ];

    /**
     * Get the patient that owns the assessment
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the user who performed the assessment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the assessment result
     */
    public function result()
    {
        return $this->hasOne(AssessmentResult::class);
    }
}
