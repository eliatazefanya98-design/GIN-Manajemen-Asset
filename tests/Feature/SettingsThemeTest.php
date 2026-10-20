<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_and_view_the_dark_theme_setting(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('settings.update'), [
            'organization_name' => 'GIN',
            'organization_unit' => 'IT',
            'theme' => 'dark',
            'asset_prefix' => 'AST',
            'asset_separator' => '-',
            'asset_digits' => 4,
            'asset_next_number' => 1,
            'timezone' => 'Asia/Jakarta (GMT+07:00)',
            'date_format' => 'DD/MM/YYYY',
        ])->assertRedirect();

        $this->assertSame('dark', SystemSetting::current()->theme);

        $this->get(route('settings.index'))
            ->assertOk()
            ->assertSee('data-theme="dark"', false)
            ->assertSee('id="theme-toggle"', false)
            ->assertSee('value="dark" checked', false);
    }
}