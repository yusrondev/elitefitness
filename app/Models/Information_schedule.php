<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Information_schedule extends Model
{
    use HasFactory;
    protected $table = 'information_schedule';
    protected $fillable = ['iduser', 'service_price', 'start_time', 'start_break', 'end_break', 'end_time'];
}