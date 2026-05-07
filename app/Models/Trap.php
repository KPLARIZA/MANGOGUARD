<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trap extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'status',
        'type',
        'battery_level',
        'storage_volume',
        'storage_threshold',
        'last_maintenance',
        'notes'
    ];

    protected $casts = [
        'last_maintenance' => 'datetime',
        'storage_threshold' => 'float',
        'storage_volume' => 'float',
        'battery_level' => 'float'
    ];

    public function pestData()
    {
        return $this->hasMany(PestData::class);
    }

    public function pestAlerts()
    {
        return $this->hasMany(PestAlert::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    public function getBatteryStatusAttribute()
    {
        if ($this->battery_level >= 80) {
            return ['status' => 'good', 'color' => 'success'];
        } elseif ($this->battery_level >= 30) {
            return ['status' => 'medium', 'color' => 'warning'];
        } else {
            return ['status' => 'low', 'color' => 'danger'];
        }
    }

    public function getStorageStatusAttribute()
    {
        if ($this->storage_volume >= $this->storage_threshold) {
            return ['status' => 'full', 'color' => 'danger'];
        } elseif ($this->storage_volume >= ($this->storage_threshold * 0.8)) {
            return ['status' => 'almost_full', 'color' => 'warning'];
        } else {
            return ['status' => 'ok', 'color' => 'success'];
        }
    }

    public function needsMaintenance()
    {
        return $this->battery_level < 20 || 
               $this->storage_volume >= $this->storage_threshold ||
               $this->status === 'maintenance';
    }
} 