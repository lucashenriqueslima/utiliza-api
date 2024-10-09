<?php

namespace App\Models\Ileva;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IlevaSituation extends Model
{
    use HasFactory;

    protected $connection = 'ileva';

    protected $table = 'hbrd_asc_situacao';

    protected $guarded = [];
}
