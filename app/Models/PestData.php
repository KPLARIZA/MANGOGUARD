<?php

namespace App\Models;

use App\Services\PestAlertService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PestData extends Model
{
    use HasFactory;

    protected $fillable = [
        'pest_type_id',
        'trap_id',
        'count',
        'location',
        'image_path',
        'detected_at',
        'temperature',
        'humidity',
        'notes'
    ];

    protected $casts = [
        'detected_at' => 'datetime',
        'temperature' => 'float',
        'humidity' => 'float'
    ];

    public function pestType()
    {
        return $this->belongsTo(PestType::class);
    }

    public function trap()
    {
        return $this->belongsTo(Trap::class);
    }

    // Boot method to handle automatic alert generation
    protected static function boot()
    {
        parent::boot();

        static::created(function ($pestData) {
            $alertService = app(PestAlertService::class);
            $alertService->processPestData($pestData);
        });
    }
} 