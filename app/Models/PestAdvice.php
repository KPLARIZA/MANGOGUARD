<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PestAdvice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'pest_type',
        'severity',
        'preventive_measures',
        'control_methods',
        'chemical_treatments',
        'organic_treatments',
        'monitoring_tips',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'info',
            default => 'secondary'
        };
    }

    public function getPestTypeColorAttribute()
    {
        return match($this->pest_type) {
            'cecid_fly' => 'danger',
            'fruit_fly' => 'warning',
            'unknown' => 'secondary',
            default => 'primary'
        };
    }

    public function scopeByPestType($query, $type)
    {
        return $query->where('pest_type', $type);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function getFormattedContentAttribute()
    {
        return nl2br(e($this->content));
    }

    public function getFormattedPreventiveMeasuresAttribute()
    {
        return nl2br(e($this->preventive_measures));
    }

    public function getFormattedControlMethodsAttribute()
    {
        return nl2br(e($this->control_methods));
    }

    public function getFormattedChemicalTreatmentsAttribute()
    {
        return $this->chemical_treatments ? nl2br(e($this->chemical_treatments)) : 'No chemical treatments recommended.';
    }

    public function getFormattedOrganicTreatmentsAttribute()
    {
        return $this->organic_treatments ? nl2br(e($this->organic_treatments)) : 'No organic treatments recommended.';
    }

    public function getFormattedMonitoringTipsAttribute()
    {
        return nl2br(e($this->monitoring_tips));
    }
} 