<?php

namespace App\Exceptions;

use App\Models\Patient;
use RuntimeException;
use Throwable;

/**
 * O paciente foi cadastrado, mas o e-mail com a senha temporária não pôde
 * ser entregue. Carrega o paciente para que o fluxo continue a partir dele.
 */
class PatientAccountEmailFailedException extends RuntimeException
{
    public function __construct(public readonly Patient $patient, Throwable $previous)
    {
        parent::__construct(
            "Paciente #{$patient->id} cadastrado, mas o e-mail com a senha temporária falhou: {$previous->getMessage()}",
            0,
            $previous,
        );
    }
}
