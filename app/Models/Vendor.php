<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'kontak',
        'email',
        'alamat',
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}