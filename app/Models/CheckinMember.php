<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CheckinMember extends Model
{
    use HasFactory;
    protected $table = 'checkin_member';

    // Kolom yang dapat diisi
    protected $fillable = [
    'idmember', 
    'key_fob',
    'status',
    'created_at',
    'updated_at'
    ];
}
