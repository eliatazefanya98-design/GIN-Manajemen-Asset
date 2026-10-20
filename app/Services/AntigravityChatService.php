<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AntigravityChatService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $model;
    protected int $timeout;
    protected AiKnowledgeService $knowledgeService;

    public function __construct(AiKnowledgeService $knowledgeService)
    {
        $this->knowledgeService = $knowledgeService;
        $this->apiKey = config('services.antigravity.key', 'sk-a9a37f311b1527fb-r3j1ok-6dcdb951');
        $this->baseUrl = rtrim(config('services.antigravity.url', 'http://localhost:20128/v1'), '/');
        $this->model = config('services.antigravity.model', 'ag/gemini-3.8-flash-low');
        $this->timeout = config('services.antigravity.timeout', 60);
    }

    /**
     * Mengirim prompt pengguna beserta seluruh Knowledge Base ke LLM Antigravity
     */
    public function ask(string $userPrompt): string
    {
        $systemPrompt = $this->knowledgeService->buildSystemPrompt($userPrompt);

        $payload = [
            'model' => $this->model,
            'stream' => false,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ],
                [
                    'role' => 'user',
                    'content' => $userPrompt,
                ],
            ],
            'temperature' => 0.1,
            'max_tokens' => 2048,
        ];

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout($this->timeout)
                ->post("{$this->baseUrl}/chat/completions", $payload);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? null;

                if (is_string($content) && trim($content) !== '') {
                    return trim($content);
                }

                Log::warning('Antigravity API response choices empty', ['response' => $data]);
                return 'Asisten AI menerima respons kosong dari server model.';
            }

            Log::error('Antigravity API HTTP error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return "Maaf, terjadi kendala saat menghubungi gateway Antigravity AI (Status: {$response->status()}). Mohon pastikan router API di {$this->baseUrl} aktif.";
        } catch (\Throwable $e) {
            Log::error('Antigravity connection exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return 'Layanan Chatbot AI belum dapat terhubung ke Antigravity router (http://localhost:20128). Pastikan gateway router sedang berjalan.';
        }
    }
}
