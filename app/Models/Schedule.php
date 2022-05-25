<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'startDate',
        'endDate',
        'location',
        'notes',
        'price',
        'allDay',



    ];
    protected $hidden = [
        'user_id',
        'trainer_id',
        'created_at',
        'updated_at',
    ];
    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
