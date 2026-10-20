<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiPrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'prediction',
        'risk_score',
        'risk_category',
        'factors',
        'recommendation',
        'model_name',
        'model_version',
        'predicted_at',
    ];

    protected $casts = [
        'prediction' => 'boolean',
        'risk_score' => 'decimal:4',
        'factors' => 'array',
        'predicted_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}