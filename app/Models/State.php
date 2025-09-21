<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $table = 'states';

    // Kolom yang dapat diisi
    protected $fillable = ['name', 'country_id'];

    /**
     * Relasi ke model Country
     * Banyak state milik satu country
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Relasi ke model City
     * Satu state memiliki banyak city
     */
    public function cities()
    {
        return $this->hasMany(City::class, 'state_id');
    }
}