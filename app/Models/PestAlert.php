<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PestAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trap_id',
        'title',
        'description',
        'severity',
        'location',
        'pest_type',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    public function trap()
    {
        return $this->belongsTo(Trap::class);
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

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => 'danger',
            'resolved' => 'success',
            default => 'secondary'
        };
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeByPestType($query, $type)
    {
        return $query->where('pest_type', $type);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }
} 