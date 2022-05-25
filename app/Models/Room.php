<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'FirstUserUsername',
        'SecondUserUsername',
  ];
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    use HasFactory;
}
