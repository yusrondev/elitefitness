<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopUp extends Model
{
    protected $table = 'top_up';

    protected $fillable = [
        'description',
        'price',
    ];

}