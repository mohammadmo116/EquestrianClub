<?php

namespace App\Models;

use App\Models\User;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    public function tournaments()
    {
        return $this->belongsTo(Tournament::class,'tournament_id','id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('rank');
    }

    protected $fillable = [
        'category'
    ];

}
