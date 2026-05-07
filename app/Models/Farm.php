<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'block',
        'area',
        'description',
        'user_id',
    ];

    public function images()
    {
        return $this->hasMany(FarmImage::class);
    }

    public function owner()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    public function cropHarvests()
    {
        return $this->hasMany(CropHarvest::class);
    }
}