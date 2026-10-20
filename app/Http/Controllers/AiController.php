<?php

namespace App\Http\Controllers;

use App\Services\AntigravityChatService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    protected AntigravityChatService $chatService;

    public function __construct(AntigravityChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function index()
    {
        return view('ai.index');
    }

    public function chat(Request $request)
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:1500'],
        ]);

        $prompt = trim($validated['prompt']);
        $response = $this->chatService->ask($prompt);

        return view('ai.index', [
            'prompt' => $prompt,
            'response' => $response,
        ]);
    }
}