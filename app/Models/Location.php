<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'name',
        'gedung',
        'lantai',
        'ruangan',
        'deskripsi',
        'description',
    ];

    protected static function booted(): void
    {
        static::saving(function (Location $location) {
            if ($location->nama && ! $location->name) {
                $location->name = $location->nama;
            } elseif ($location->name && ! $location->nama) {
                $location->nama = $location->name;
            }

            if ($location->deskripsi && ! $location->description) {
                $location->description = $location->deskripsi;
            } elseif ($location->description && ! $location->deskripsi) {
                $location->deskripsi = $location->description;
            }
        });
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}