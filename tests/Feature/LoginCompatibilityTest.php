<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LoginCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_plaintext_password_can_still_login(): void
    {
        DB::table('users')->insert([
            'name' => 'User Lama',
            'email' => 'userlama@gin.co.id',
            'password' => 'Raharasia12',
            'role' => 'pemimpin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::where('email', 'userlama@gin.co.id')->first();

        $response = $this->from('/login')->post('/login', [
            'email' => 'userlama@gin.co.id',
            'password' => 'Raharasia12',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}
