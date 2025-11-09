<?php

return [
    'required' => 'Este campo é obrigatório.',
    'min' => [
        'string' => 'Este campo deve ter no mínimo :min caracteres.',
    ],
    'confirmed' => 'A confirmação não corresponde.',
    'accepted' => 'Este campo deve ser aceito.',

    'before_or_equal' => 'O campo da data deve ser uma data igual ou anterior a hoje.',

    'custom' => [
        'lgpd_consent' => [
            'required' => 'Você deve aceitar os termos da LGPD para continuar.',
            'accepted' => 'Você deve aceitar os termos da LGPD para prosseguir com o cadastro.',
        ],
    ],
];
