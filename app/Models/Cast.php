<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cast extends Model
{
    use HasFactory;
    protected $table = 'casts';
    protected $fillable = ['name','age', 'bio'];

    public function perans()
    {
        return $this->hasMany(Peran::class);
    }

    public function films()
    {
        return $this->belongsToMany(Film::class, 'perans')
                     ->withPivot('actor');
    }
}
