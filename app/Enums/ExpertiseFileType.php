<?php

// 'report_audio','report_video','plate_image','front_side_image','right_side_image','left_side_image','rear_side_image','street_video'

namespace App\Enums;

enum ExpertiseFileType: string
{
    case ReportAudio = 'report_audio';
    case ReportVideo = 'report_video';
    case PlateImage = 'plate_image';
    case FrontSideImage = 'front_side_image';
    case RightSideImage = 'right_side_image';
    case LeftSideImage = 'left_side_image';
    case RearSideImage = 'rear_side_image';
    case StreetVideo = 'street_video';
}
