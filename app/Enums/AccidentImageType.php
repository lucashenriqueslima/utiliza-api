<?php

namespace App\Enums;

enum AccidentImageType: string
{
    case Front = 'front';
    case FrontLeft = 'front_left';
    case FrontRight = 'front_right';
    case Rear = 'rear';
    case RearLeft = 'rear_left';
    case RearRight = 'rear_right';
    case Left = 'left';
    case Right = 'right';
    case Trunk = 'trunk';
    case TrunkTire = 'trunk_tire';
    case Dashboard = 'dashboard';
    case Crlv = 'crlv';
    case Cnh = 'cnh';

    public function getLabel(): string
    {
        return match ($this) {
            self::Front => 'Frente',
            self::FrontLeft => 'Frente Lateral Esquerda',
            self::FrontRight => 'Frente Lateral Direita',
            self::Rear => 'Traseira',
            self::RearLeft => 'Traseira Lateral Esquerda',
            self::RearRight => 'Traseira Lateral Direita',
            self::Left => 'Lateral Esquerda',
            self::Right => 'Lateral Direita',
            self::Trunk => 'Porta-Malas (Aberto)',
            self::TrunkTire => 'Porta-Malas com Pneu Estepe',
            self::Dashboard => 'Painel Interno',
            self::Crlv => 'CRLV',
            self::Cnh => 'CNH',
        };
    }
}
