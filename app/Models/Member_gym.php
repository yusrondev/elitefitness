<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member_gym extends Model
{
    use HasFactory;
    protected $table = 'member_gym';

    // Kolom yang dapat diisi
    protected $fillable = [
    'iduser', 
    'idmember', 
    'idpaket',
    'total_price',
    'start_training',
    'end_training',
    'description',
    'created_at',
    'updated_at'
    ];

    public function trainer()
    {
        return $this->belongsTo(Trainer::class, 'idpacket_trainer', 'id');
    }
}
