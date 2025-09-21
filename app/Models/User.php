<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'full_name',
        'idcountry',
        'idstate',
        'idcities',
        'gender',
        'address',
        'address_2',
        'portal_code',
        'photo',
        'upload',
        'role',
        'barcode',
        'barcode_path',
        'number_phone',
        'number_phoned',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user is admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is trainer
     *
     * @return bool
     */
    public function isTrainer(): bool
    {
        return $this->role === 'trainer';
    }
    
    public function member()
    {
        return $this->hasOne(Member_gym::class, 'idmember', 'id');
    }

}
