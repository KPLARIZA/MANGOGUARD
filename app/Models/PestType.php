<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PestType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'scientific_name',
        'description',
        'threshold_value',
        'control_methods',
        'image_path'
    ];

    protected $casts = [
        'threshold_value' => 'integer',
        'control_methods' => 'array'
    ];

    public function pestData()
    {
        return $this->hasMany(PestData::class);
    }

    public function alerts()
    {
        return $this->hasMany(PestAlert::class);
    }
} 