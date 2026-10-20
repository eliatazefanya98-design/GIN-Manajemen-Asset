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
        'name',
        'kontak',
        'contact',
        'email',
        'alamat',
        'address',
    ];

    protected static function booted(): void
    {
        static::saving(function (Vendor $vendor) {
            if ($vendor->nama && ! $vendor->name) {
                $vendor->name = $vendor->nama;
            } elseif ($vendor->name && ! $vendor->nama) {
                $vendor->nama = $vendor->name;
            }

            if ($vendor->kontak && ! $vendor->contact) {
                $vendor->contact = $vendor->kontak;
            } elseif ($vendor->contact && ! $vendor->kontak) {
                $vendor->kontak = $vendor->contact;
            }

            if ($vendor->alamat && ! $vendor->address) {
                $vendor->address = $vendor->alamat;
            } elseif ($vendor->address && ! $vendor->alamat) {
                $vendor->alamat = $vendor->address;
            }
        });
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}