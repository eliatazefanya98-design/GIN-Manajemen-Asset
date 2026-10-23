<?php

namespace Tests\Feature;

use App\Models\AiChatMessage;
use App\Models\User;
use App\Services\AntigravityChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AiChatHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_page_displays_chat_history_for_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        // Buat riwayat pesan sebelumnya
        AiChatMessage::create([
            'user_id' => $user->id,
            'role' => 'user',
            'message' => 'Halo, berapa jumlah aset aktif saat ini?',
        ]);

        AiChatMessage::create([
            'user_id' => $user->id,
            'role' => 'assistant',
            'message' => 'Saat ini ada **5 aset aktif** yang tercatat di sistem.',
        ]);

        $response = $this->actingAs($user)->get(route('ai.index'));

        $response->assertStatus(200);
        $response->assertSee('Halo, berapa jumlah aset aktif saat ini?');
        $response->assertSee('Saat ini ada');
        $response->assertSee('5 aset aktif');
        $response->assertSee('2 pesan');
    }

    public function test_submitting_chat_persists_both_user_and_bot_messages_without_losing_history(): void
    {
        $user = User::factory()->create(['role' => 'staff']);

        // Mock AntigravityChatService agar tidak memanggil remote HTTP saat testing
        $mockChatService = Mockery::mock(AntigravityChatService::class);
        $mockChatService->shouldReceive('ask')
            ->once()
            ->with('Di mana printer Epson disimpan?', Mockery::any())
            ->andReturn('Printer Epson disimpan di Lab Komputer 1.');
        $this->app->instance(AntigravityChatService::class, $mockChatService);

        // Pesan pertama
        AiChatMessage::create([
            'user_id' => $user->id,
            'role' => 'user',
            'message' => 'Pertanyaan awal yang tidak boleh hilang.',
        ]);
        AiChatMessage::create([
            'user_id' => $user->id,
            'role' => 'assistant',
            'message' => 'Jawaban awal yang tetap tersimpan.',
        ]);

        // Kirim pertanyaan kedua via POST biasa
        $response = $this->actingAs($user)->post(route('ai.chat'), [
            'prompt' => 'Di mana printer Epson disimpan?',
        ]);

        $response->assertRedirect(route('ai.index'));

        // Cek bahwa total pesan sekarang bertambah menjadi 4 (pesan lama tidak hilang!)
        $this->assertDatabaseCount('ai_chat_messages', 4);
        $this->assertDatabaseHas('ai_chat_messages', [
            'user_id' => $user->id,
            'role' => 'user',
            'message' => 'Pertanyaan awal yang tidak boleh hilang.',
        ]);
        $this->assertDatabaseHas('ai_chat_messages', [
            'user_id' => $user->id,
            'role' => 'user',
            'message' => 'Di mana printer Epson disimpan?',
        ]);
        $this->assertDatabaseHas('ai_chat_messages', [
            'user_id' => $user->id,
            'role' => 'assistant',
            'message' => 'Printer Epson disimpan di Lab Komputer 1.',
        ]);
    }

    public function test_ajax_chat_returns_json_and_stores_messages(): void
    {
        $user = User::factory()->create(['role' => 'pimpinan']);

        $mockChatService = Mockery::mock(AntigravityChatService::class);
        $mockChatService->shouldReceive('ask')
            ->once()
            ->andReturn('Terdapat 1 mutasi yang menunggu persetujuan Anda.');
        $this->app->instance(AntigravityChatService::class, $mockChatService);

        $response = $this->actingAs($user)
            ->postJson(route('ai.chat'), [
                'prompt' => 'Ada berapa mutasi pending?',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'user_message' => [
                'role' => 'user',
                'message' => 'Ada berapa mutasi pending?',
            ],
            'bot_message' => [
                'role' => 'assistant',
                'message' => 'Terdapat 1 mutasi yang menunggu persetujuan Anda.',
            ],
        ]);

        $this->assertDatabaseCount('ai_chat_messages', 2);
    }

    public function test_clear_chat_removes_messages_only_for_current_user(): void
    {
        $user1 = User::factory()->create(['role' => 'admin']);
        $user2 = User::factory()->create(['role' => 'staff']);

        AiChatMessage::create(['user_id' => $user1->id, 'role' => 'user', 'message' => 'Pesan user 1']);
        AiChatMessage::create(['user_id' => $user2->id, 'role' => 'user', 'message' => 'Pesan user 2']);

        $response = $this->actingAs($user1)->post(route('ai.clear'));

        $response->assertRedirect(route('ai.index'));
        $response->assertSessionHas('success');

        // Pesan user 1 terhapus, pesan user 2 tetap aman
        $this->assertDatabaseMissing('ai_chat_messages', ['user_id' => $user1->id]);
        $this->assertDatabaseHas('ai_chat_messages', ['user_id' => $user2->id, 'message' => 'Pesan user 2']);
    }
}
