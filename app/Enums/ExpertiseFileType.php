<?php

// 'report_audio',
// 'report_video',
// 'cnh_front_image',
// 'crlv_front_image',
// 'vehicle_plate_image',
// 'vehicle_front_side_image',
// 'vehicle_right_side_image',
// 'vehicle_left_side_image',
// 'vehicle_rear_side_image',
// 'vehicle_street_video',
// 'dynamic_image',

namespace App\Enums;

enum ExpertiseFileType: string
{
    case ReportAudio = 'report_audio';
    case ReportVideo = 'report_video';
    case CNHFrontImage = 'cnh_front_image';
    case CRLVFrontImage = 'crlv_front_image';
    case VehiclePlateImage = 'vehicle_plate_image';
    case VehicleFrontSideImage = 'vehicle_front_side_image';
    case VehicleRightSideImage = 'vehicle_right_side_image';
    case VehicleLeftSideImage = 'vehicle_left_side_image';
    case VehicleRearSideImage = 'vehicle_rear_side_image';
    case RoadImage = 'road_image';
    case RoadSignImage = 'road_sign_image';
    case BikerObservationAudio = 'biker_observation_audio';

    case DynamicImage = 'dynamic_image';

    public function getLabel(): string
    {
        return match ($this) {
            self::ReportAudio => 'Relato em Áudio',
            self::ReportVideo => 'Relato em Vídeo',
            self::CNHFrontImage => 'Imagem da CNH',
            self::CRLVFrontImage => 'Imagem do CRLV',
            self::VehiclePlateImage => 'Imagem da Placa do Veículo',
            self::VehicleFrontSideImage => 'Imagem Lado Frontal do Veículo',
            self::VehicleRightSideImage => 'Imagem Lado Direito do Veículo',
            self::VehicleLeftSideImage => 'Imagem Lado Esquerdo do Veículo',
            self::VehicleRearSideImage => 'Imagem Lado Traseiro do Veículo',
            self::RoadImage => 'Imagem da Via',
            self::RoadSignImage => 'Imagem de Sinalização',
            self::BikerObservationAudio => 'Observação do Motociclista',
            self::DynamicImage => 'Imagem Complementar'
        };
    }

    public function getFileType(): string
    {
        return match ($this) {
            self::ReportAudio => 'audio',
            self::ReportVideo => 'video',
            self::CNHFrontImage => 'image',
            self::CRLVFrontImage => 'image',
            self::VehiclePlateImage => 'image',
            self::VehicleFrontSideImage => 'image',
            self::VehicleRightSideImage => 'image',
            self::VehicleLeftSideImage => 'image',
            self::VehicleRearSideImage => 'image',
            self::RoadImage => 'image',
            self::RoadSignImage => 'image',
            self::BikerObservationAudio => 'audio',
            self::DynamicImage => 'image',
        };
    }
}
