<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cashflow extends Model
{
    protected $table = 'cashflow';

    protected $fillable = [
        'description',
        'amount',
        'type',
        'date',
        'member_id',
        'created_by',
        'created_at',
        'updated_at',
    ];

    // Jika tabel kamu menggunakan timestamps (created_at & updated_at)
    public $timestamps = true;

    // Relasi ke Member (opsional)
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    // Relasi ke User/Admin yang membuat transaksi
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}