<?php

namespace App\Service\Ai;

/**
 * Abstrai o provedor de IA generativa. Hoje só o Gemini implementa; trocar de
 * provedor é trocar o binding no AppServiceProvider.
 */
interface TextGenerator
{
    /**
     * Gera uma resposta em JSON que obedece ao schema informado.
     *
     * @param  array<string, mixed>  $schema  Schema no formato aceito pelo provedor (subset de OpenAPI).
     * @return array<string, mixed>
     *
     * @throws TextGenerationException
     */
    public function generateJson(string $prompt, array $schema): array;

    /** Identificador do modelo usado (para auditoria). */
    public function modelName(): string;
}
