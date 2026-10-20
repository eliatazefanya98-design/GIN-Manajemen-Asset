<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'name',
        'deskripsi',
        'description',
    ];

    protected static function booted(): void
    {
        static::saving(function (Category $category) {
            if ($category->nama && ! $category->name) {
                $category->name = $category->nama;
            } elseif ($category->name && ! $category->nama) {
                $category->nama = $category->name;
            }

            if ($category->deskripsi && ! $category->description) {
                $category->description = $category->deskripsi;
            } elseif ($category->description && ! $category->deskripsi) {
                $category->deskripsi = $category->description;
            }
        });
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}