<?php

namespace App\Http\Controllers;

use App\Models\AiChatMessage;
use App\Services\AntigravityChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AiController extends Controller
{
    protected AntigravityChatService $chatService;

    public function __construct(AntigravityChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function index()
    {
        $messages = AiChatMessage::where('user_id', auth()->id())
            ->oldest('id')
            ->get();

        return view('ai.index', [
            'messages' => $messages,
        ]);
    }

    public function chat(Request $request)
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:2000'],
        ]);

        $prompt = trim($validated['prompt']);
        $userId = auth()->id();

        // Ambil riwayat percakapan sebelumnya untuk konteks multi-turn LLM
        $history = AiChatMessage::where('user_id', $userId)
            ->latest('id')
            ->take(10)
            ->get()
            ->reverse()
            ->values();

        // 1. Simpan pesan pengguna ke database
        $userMessage = AiChatMessage::create([
            'user_id' => $userId,
            'role' => 'user',
            'message' => $prompt,
        ]);

        // 2. Dapatkan respon dari Antigravity LLM
        $response = $this->chatService->ask($prompt, $history);

        // 3. Simpan respon asisten bot ke database
        $botMessage = AiChatMessage::create([
            'user_id' => $userId,
            'role' => 'assistant',
            'message' => $response,
        ]);

        // Tanggapi dalam bentuk JSON jika dipanggil via AJAX / Fetch
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'user_message' => [
                    'id' => $userMessage->id,
                    'role' => 'user',
                    'message' => $prompt,
                    'time' => $userMessage->created_at->format('H:i'),
                ],
                'bot_message' => [
                    'id' => $botMessage->id,
                    'role' => 'assistant',
                    'message' => $response,
                    'rendered' => Str::markdown($response),
                    'time' => $botMessage->created_at->format('H:i'),
                ],
            ]);
        }

        return redirect()->route('ai.index');
    }

    public function clear()
    {
        AiChatMessage::where('user_id', auth()->id())->delete();

        return redirect()->route('ai.index')->with('success', 'Riwayat percakapan berhasil dibersihkan.');
    }
}