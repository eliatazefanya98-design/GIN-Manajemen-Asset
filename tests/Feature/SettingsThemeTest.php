<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_page_does_not_have_theme_toggle_and_defaults_to_light_mode(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('settings.update'), [
            'organization_name' => 'GIN',
            'organization_unit' => 'IT',
            'asset_prefix' => 'AST',
            'asset_separator' => '-',
            'asset_digits' => 4,
            'asset_next_number' => 1,
            'timezone' => 'Asia/Jakarta (GMT+07:00)',
            'date_format' => 'DD/MM/YYYY',
        ])->assertRedirect();

        $this->assertSame('light', SystemSetting::current()->theme);

        $this->get(route('settings.index'))
            ->assertOk()
            ->assertSee('data-theme="light"', false)
            ->assertDontSee('id="theme-toggle"', false)
            ->assertDontSee('Tema Tampilan Antarmuka', false)
            ->assertDontSee('Mode Gelap (Dark)', false)
            ->assertDontSee('Email Notifications', false);
    }
}
