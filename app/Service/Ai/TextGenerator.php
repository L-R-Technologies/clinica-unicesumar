<?php

namespace App\Service\Ai;

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

    public function modelName(): string;
}
