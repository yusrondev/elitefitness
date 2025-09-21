<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleTrainer extends Model
{

    use HasFactory;
    protected $table = 'schedule_trainer';

    protected $fillable = [
        'idtopup_informasi', 
        'iduser', 
        'date_trainer', 
        'start_time', 
        'end_time',
        'created_at',
        'updated_at'
    ];
}