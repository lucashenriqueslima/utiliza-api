<?php

namespace App\Enums;

enum FileType: string
{
    case Audio = 'audio';
    case Image = 'image';
    case Video = 'video';
}
