<?php

namespace App\Service\Ai;

use RuntimeException;

/**
 * Falha esperada ao conversar com o provedor de IA (chave ausente, erro HTTP,
 * resposta fora do formato). Tratada na borda com mensagem amigável.
 */
class TextGenerationException extends RuntimeException {}
