<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TopupInformation extends Model
{
    use HasFactory;
    protected $table = 'top_upInformation';

    // Kolom yang dapat diisi
    protected $fillable = [
    'iduser',
    'idadmin',
    'idtop_up',
    'idtrainer',
    'total_poin',
    'datetop_up',
    'day',
    'status',
    'created_at',
    'updated_at',
    ];
}