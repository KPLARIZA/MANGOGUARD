<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Users extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'supabase_user_id',
        'profile_picture',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function farmImages()
    {
        return $this->hasMany(FarmImage::class, 'user_id');
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class, 'user_id');
    }

    public function pestAdvice()
    {
        return $this->hasMany(PestAdvice::class, 'user_id');
    }

    public function pestAlerts()
    {
        return $this->hasMany(PestAlert::class, 'user_id');
    }

    public function farms()
    {
        return $this->hasMany(Farm::class, 'user_id');
    }
}