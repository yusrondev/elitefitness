<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;
    protected $table = 'expense_money';

    // Kolom yang dapat diisi
    protected $fillable = [
    'iduser', 
    'description',
    'money',
    'created_at',
    'updated_at'
    ];
}
