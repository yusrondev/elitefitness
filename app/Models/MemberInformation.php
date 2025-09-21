<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'iduser',
        'idcountry',
        'idstate',
        'idcities',
        'name',
        'notelp',
        'gender',
        'address',
        'address_2',
        'portal_code',
        'email',
        'password',
        'barcode',
        'photo',
        'upload',
        'role',
        'status',
        'created_at',
        'updated_at'
    ];

    // Event untuk menghasilkan barcode jika tidak ada barcode yang diatur
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($member) {
            // Buat barcode hanya jika belum diatur sebelumnya
            if (empty($member->barcode)) {
                $member->barcode = 'BC-' . strtoupper(uniqid());
            }
        });
    }
}