<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packets extends Model
{
    protected $table = 'packets';

    protected $fillable = [
        'iduser',
        'packet_name',
        'days',
        'price',
        'promote',
        'is_active',
        'description',
    ];

    protected $casts = [
        'description' => 'array',
    ];
}