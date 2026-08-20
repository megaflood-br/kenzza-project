<?php

return [
    'accepted'        => 'O campo :attribute deve ser aceito.',
    'active_url'      => 'O campo :attribute não é uma URL válida.',
    'after'           => 'O campo :attribute deve ser uma data posterior a :date.',
    'alpha'           => 'O campo :attribute deve conter apenas letras.',
    'boolean'         => 'O campo :attribute deve ser verdadeiro ou falso.',
    'confirmed'       => 'A confirmação de :attribute não confere.',
    'email'           => 'O campo :attribute deve ser um endereço de e-mail válido.',
    'ends_with'       => 'O campo :attribute deve terminar com um dos seguintes: :values',
    'exists'          => 'O campo :attribute selecionado é inválido.',
    'filled'          => 'O campo :attribute deve conter um valor.',
    'image'           => 'O campo :attribute deve ser uma imagem.',
    'integer'         => 'O campo :attribute deve ser um número inteiro.',
    'max'             => [
        'numeric' => 'O campo :attribute não pode ser superior a :max.',
        'file'    => 'O campo :attribute não pode ser superior a :max kilobytes.',
        'string'  => 'O campo :attribute não pode ser superior a :max caracteres.',
    ],
    'min'             => [
        'numeric' => 'O campo :attribute deve ser pelo menos :min.',
        'string'  => 'O campo :attribute deve ter pelo menos :min caracteres.',
    ],
    'numeric'         => 'O campo :attribute deve ser um número.',
    'required'        => 'O campo :attribute é obrigatório.',
    'unique'          => 'O campo :attribute já está sendo utilizado.',
    'url'             => 'O campo :attribute não é uma URL válida.',

    // Aqui você define os nomes amigáveis dos campos
    'attributes' => [
        'name'     => 'nome',
        'email'    => 'e-mail',
        'password' => 'senha',
        'whatsapp' => 'WhatsApp',
        'city'     => 'cidade',
    ],
];
