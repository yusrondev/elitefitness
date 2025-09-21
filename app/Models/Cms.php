<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cms extends Model
{
    use HasFactory;

    protected $table = 'cms';
    protected $fillable = ['section', 'content'];
    protected $casts = [
        'content' => 'array', // Konversi otomatis ke array saat mengambil data
    ];
}
