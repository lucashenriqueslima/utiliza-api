<?php

// 'report_audio','report_video','plate_image','front_side_image','right_side_image','left_side_image','rear_side_image','street_video'

namespace App\Enums;

enum ExpertiseFileType: string
{
    case ReportAudio = 'report_audio';
    case ReportVideo = 'report_video';
    case PlateImage = 'vehicle_plate_image';
    case FrontSideImage = 'vehicle_front_side_image';
    case RightSideImage = 'vehicle_right_side_image';
    case LeftSideImage = 'vehicle_left_side_image';
    case RearSideImage = 'vehicle_rear_side_image';
    case StreetVideo = 'street_video';

    public function getLabel(): string
    {
        return match ($this) {
            self::ReportAudio => 'Relato em Áudio',
            self::ReportVideo => 'Relato em Vídeo',
            self::PlateImage => 'Imagem da Placa do Veículo',
            self::FrontSideImage => 'Imagem Lado Frontal do Veículo',
            self::RightSideImage => 'Imagem Lado Direito do Veículo',
            self::LeftSideImage => 'Imagem Lado Esquerdo do Veículo',
            self::RearSideImage => 'Imagem Lado Traseiro do Veículo',
            self::StreetVideo => 'Vídeo da Rua do Veículo',
        };   
    }

    public function getFileType(): string
    {
        return match ($this) {
            self::ReportAudio => 'audio',
            self::ReportVideo => 'video',
            self::PlateImage => 'image',
            self::FrontSideImage => 'image',
            self::RightSideImage => 'image',
            self::LeftSideImage => 'image',
            self::RearSideImage => 'image',
            self::StreetVideo => 'video',
        };   
    }
}
