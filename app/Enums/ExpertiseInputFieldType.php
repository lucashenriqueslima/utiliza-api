<?php

namespace App\Enums;

enum ExpertiseInputFieldType: string
{
    case Name = 'name';
    case Cpf = 'cpf';
    case Phone = 'phone';
    case Plate = 'plate';

    public function getLabel(): string
    {
        return match ($this) {
            self::Name => 'Nome',
            self::Cpf => 'CPF',
            self::Phone => 'Telefone',
            self::Plate => 'Placa',
        };   
    }
}