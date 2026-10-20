<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'organization_name',
        'organization_unit',
        'theme',
        'timezone',
        'date_format',
        'asset_prefix',
        'asset_separator',
        'asset_digits',
        'asset_next_number',
        'logo_path',
        'email_notifications',
    ];

    protected $casts = [
        'asset_digits' => 'integer',
        'asset_next_number' => 'integer',
        'email_notifications' => 'boolean',
    ];

    public static function defaults(): array
    {
        return [
            'organization_name' => 'PT Global Internasional Nusantara',
            'organization_unit' => 'Divisi Infrastruktur & IT Ops',
            'theme' => 'light',
            'timezone' => 'Asia/Jakarta (GMT+07:00)',
            'date_format' => 'DD/MM/YYYY',
            'asset_prefix' => 'AST-GIN',
            'asset_separator' => '-',
            'asset_digits' => 4,
            'asset_next_number' => 1,
            'email_notifications' => true,
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate([], static::defaults());
    }

    public function nextAssetCode(): string
    {
        return $this->asset_prefix
            .$this->asset_separator
            .str_pad((string) $this->asset_next_number, $this->asset_digits, '0', STR_PAD_LEFT);
    }

    public function initials(): string
    {
        $name = preg_replace('/\b(PT|CV|UD|YAYASAN)\b\.?/i', '', $this->organization_name);
        $words = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY);
        $initials = collect($words)->map(fn (string $word) => strtoupper(substr($word, 0, 1)))->take(3)->implode('');

        return $initials ?: 'GIN';
    }
}
