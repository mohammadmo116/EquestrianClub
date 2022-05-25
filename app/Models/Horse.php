<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horse extends Model
{
    use HasFactory;
    protected $fillable=[
        'owner_type',
        'owner_id',
        'roomId',
        'name',
        'birthday',
        'gender'

    ];
    public function owner(){
        return $this->morphTo();
    }
}
