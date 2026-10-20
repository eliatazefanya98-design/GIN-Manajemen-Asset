<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_profile_page(): void
    {
        $user = User::factory()->create([
            'name' => 'Zefaa',
            'email' => 'zefaa@gin.co.id',
            'role' => 'pemimpin',
        ]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertOk()
            ->assertSee('Profil Saya')
            ->assertSee('Ubah Password')
            ->assertSee('Zefaa');
    }
}
