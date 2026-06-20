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
        'gravida',
        'para',
        'gestational_age',
        'multiple_pregnancy',
        'systolic_bp',
        'diastolic_bp',
        'beratbadan',
        'tinggibadan',
        'imt',
        'heart_rate',
        'temperature',
        'protein_urine',
        'map',
        'gds',
        'platelets',
        'sgot',
        'sgpt',
        'creatinine',
        'uric_acid',
        'severe_headache',
        'blurred_vision',
        'epigastric_pain',
        'nausea_vomiting',
        'facial_edema',
        'hand_edema',
        'decreased_consciousness',
        'seizures',
        'chronic_hypertension',
        'previous_preeclampsia',
        'diabetes',
        'kidney_disease',
        'obesity',
        'autoimmune_disease',
        'family_history_preeclampsia',
        'notes',
        'assessment_date',
    ];

    protected $casts = [
        'multiple_pregnancy' => 'boolean',
        'severe_headache' => 'boolean',
        'blurred_vision' => 'boolean',
        'epigastric_pain' => 'boolean',
        'nausea_vomiting' => 'boolean',
        'facial_edema' => 'boolean',
        'hand_edema' => 'boolean',
        'decreased_consciousness' => 'boolean',
        'seizures' => 'boolean',
        'chronic_hypertension' => 'boolean',
        'previous_preeclampsia' => 'boolean',
        'diabetes' => 'boolean',
        'kidney_disease' => 'boolean',
        'obesity' => 'boolean',
        'autoimmune_disease' => 'boolean',
        'family_history_preeclampsia' => 'boolean',
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
