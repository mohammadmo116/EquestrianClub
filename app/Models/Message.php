<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable=[
   'Username',
   'MessageText',

    ];
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    use HasFactory;
}
