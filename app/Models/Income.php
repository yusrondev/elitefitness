<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Income extends Model
{
    use HasFactory;
    protected $table = 'income_money';

    // Kolom yang dapat diisi
    protected $fillable = [
    'iduser', 
    'description',
    'money',
    'created_at',
    'updated_at'
    ];
}
