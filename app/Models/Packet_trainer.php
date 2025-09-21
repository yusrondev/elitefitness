<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packet_trainer extends Model
{
    protected $table = 'packet_trainer';

    protected $fillable = [
        'iduser',
        'packet_name',
        'pertemuan',
        'poin',
        'price',
    ];

}
