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
            'no_risk' => 'Normal',
            'low_risk' => 'Risiko Rendah',
            'moderate_risk' => 'Risiko Sedang',
            'high_risk' => 'Preeklampsia',
            'severe_preeclampsia' => 'Preeklampsia Berat',
            default => 'Tidak Diketahui',
        };
    }

    public function getPredictionLabelAttribute(): string
    {
        return in_array($this->risk_category, ['high_risk', 'severe_preeclampsia'], true)
            ? 'Preeklampsia'
            : 'Normal';
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

    /**
     * Get recommendations array with dynamic text replacement
     */
    public function getRecommendationsAttribute($value)
    {
        $recs = $this->castAttribute('recommendations', $value);
        if (is_array($recs)) {
            // Replace old preeclampsia recommendation
            $recs = array_map(function($rec) {
                return $rec === 'Segera konsultasi dokter spesialis obstetri'
                    ? 'Segera rujuk pasien ke rumah sakit'
                    : $rec;
            }, $recs);

            // Add check for normal but high blood pressure (systolic > 140 or diastolic > 90)
            if ($this->risk_category === 'no_risk' && !in_array('Cek tekanan darah secara berkala', $recs, true)) {
                $assessment = $this->assessment;
                if ($assessment && ($assessment->systolic_bp > 140 || $assessment->diastolic_bp > 90)) {
                    $recs[] = 'Cek tekanan darah secara berkala';
                }
            }
        }
        return $recs;
    }
}
