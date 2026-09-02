<?php

namespace App\Service\Ai;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GeminiClient implements TextGenerator
{
    private const BASE_URL = 'https://generativelanguage.googleapis.com/v1beta';

    /** Temperatura baixa: análise clínica pede respostas consistentes, não criativas. */
    private const TEMPERATURE = 0.4;

    private readonly string $apiKey;

    private readonly string $model;

    private readonly int $timeoutSeconds;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.api_key', '');
        $this->model = (string) config('services.gemini.model');
        $this->timeoutSeconds = (int) config('services.gemini.timeout');
    }

    public function generateJson(string $prompt, array $schema): array
    {
        if ($this->apiKey === '') {
            throw new TextGenerationException('A chave GEMINI_API_KEY não está configurada.');
        }

        try {
            $response = Http::withHeaders(['x-goog-api-key' => $this->apiKey])
                ->timeout($this->timeoutSeconds)
                ->post(self::BASE_URL."/models/{$this->model}:generateContent", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'responseSchema' => $schema,
                        'temperature' => self::TEMPERATURE,
                    ],
                ]);
        } catch (ConnectionException $e) {
            throw new TextGenerationException('Não foi possível conectar ao Gemini: '.$e->getMessage(), 0, $e);
        }

        if ($response->failed()) {
            $message = $response->json('error.message') ?? $response->body();

            throw new TextGenerationException("O Gemini respondeu com status {$response->status()}: {$message}");
        }

        $parts = $response->json('candidates.0.content.parts');

        if (! is_array($parts)) {
            throw new TextGenerationException('A resposta do Gemini não contém texto (possível bloqueio de segurança).');
        }

        $text = implode('', array_map(fn ($part) => is_array($part) ? (string) ($part['text'] ?? '') : '', $parts));
        $decoded = json_decode($text, true);

        if (! is_array($decoded)) {
            throw new TextGenerationException('A resposta do Gemini não é um JSON válido.');
        }

        return $decoded;
    }

    public function modelName(): string
    {
        return $this->model;
    }
}
