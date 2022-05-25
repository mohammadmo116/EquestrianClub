<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
          'name',
          'club' ,
          'email' ,
          'size',
          'location',
          'description',
          'date',
          'image',
          'private',
    ];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
