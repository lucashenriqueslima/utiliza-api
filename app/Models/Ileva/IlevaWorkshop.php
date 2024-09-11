<?php

namespace App\Models\Ileva;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IlevaWorkshop extends Model
{
    use HasFactory;

    protected $connection = 'ileva';

    protected $table = 'hbrd_adm_store';

    protected $guarded = [];
}
