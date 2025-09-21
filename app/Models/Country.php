<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table = 'countries';

    // Kolom yang dapat diisi
    protected $fillable = ['name', 'code'];

    /**
     * Relasi ke model State
     * Satu negara memiliki banyak state
     */
    public function states()
    {
        return $this->hasMany(State::class, 'country_id');
    }
}
