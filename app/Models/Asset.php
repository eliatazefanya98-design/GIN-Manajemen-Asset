<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_asset',
        'nama_asset',
        'category_id',
        'location_id',
        'vendor_id',
        'merk',
        'tipe',
        'tanggal_perolehan',
        'harga',
        'kondisi',
        'status',
        'penanggung_jawab',
        'deskripsi',
    ];

    protected $casts = [
        'tanggal_perolehan' => 'date',
        'harga' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(AssetHistory::class);
    }

    public function aiPredictions(): HasMany
    {
        return $this->hasMany(AiPrediction::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(AssetLoan::class);
    }
}