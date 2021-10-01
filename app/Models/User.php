<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = env('DB_DATABASE').'.users';
    }

    protected $fillable = [
        'given_name',
        'middle_name',
        'last_name',
        'name_suffix',
        'office_id',
        'username',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->attributes['status'] = $user->attributes['status'] ?? 'active';
        });
    }
}
