<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'risk_category',
        'risk_score',
        'k_value',
        'severity_level',
        'supporting_indicators',
        'knn_neighbors',
        'recommendations',
        'education_notes',
        'urgency_level',
        'next_checkup_date',
        'requires_referral',
        'referral_notes',
    ];

    protected $casts = [
        'supporting_indicators' => 'array',
        'knn_neighbors' => 'array',
        'recommendations' => 'array',
        'next_checkup_date' => 'date',
        'requires_referral' => 'boolean',
    ];

    /**
     * Get the assessment that owns the result
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get formatted risk category
     */
    public function getRiskCategoryLabelAttribute()
    {
        return match($this->risk_category) {
            'no_risk' => 'Tidak Ada Risiko',
            'low_risk' => 'Risiko Rendah',
            'moderate_risk' => 'Risiko Sedang',
            'high_risk' => 'Risiko Tinggi',
            'severe_preeclampsia' => 'Preeklampsia Berat',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Get risk category color for UI
     */
    public function getRiskColorAttribute()
    {
        return match($this->risk_category) {
            'no_risk' => 'green',
            'low_risk' => 'blue',
            'moderate_risk' => 'yellow',
            'high_risk' => 'orange',
            'severe_preeclampsia' => 'red',
            default => 'gray',
        };
    }
}
