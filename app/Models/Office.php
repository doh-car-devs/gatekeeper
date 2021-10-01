<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'office_id',
        'name',
        'short_name',
        'unit',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'office_id');
    }

    public function children()
    {
        return $this->hasMany(self::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
