<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CropHarvest extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_type',
        'volume',
        'harvest_date',
        'farm_id',
    ];

    protected $casts = [
        'harvest_date' => 'date',
        'volume' => 'decimal:2',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
