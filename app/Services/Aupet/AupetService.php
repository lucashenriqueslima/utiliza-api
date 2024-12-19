<?php

namespace App\Services\Aupet;

use Illuminate\Support\Facades\DB;

class AupetService
{
    public static function getCustomerInIlevaSolidyDatabase(string $cpf): array
    {
        return DB::connection('ileva')
            ->select("
            SELECT
            hap.nome,
            hap.email,
            hap.tel_celular phone_number
            FROM hbrd_asc_veiculo hav
            LEFT JOIN hbrd_asc_associado haa on haa.id = hav.id_associado
            LEFT JOIN hbrd_asc_pessoa hap on hap.id = haa.id_pessoa
            LEFT JOIN hbrd_asc_situacao has on has.id = hav.id_situacao
            LEFT JOIN hbrd_asc_beneficio_veiculo habv on habv.id_veiculo = hav.id
            LEFT JOIN hbrd_adm_benefit hab ON hab.id = habv.id_beneficio
            WHERE hav.id_situacao = '1'
            AND hap.cpf = '$cpf'
            AND hab.id IN (51,52,53,811,812,813,991,992,993,995,1027,1028,1029,1030,1031,1032,1033,1034,1035,1036,1037,1038)
            GROUP BY hap.id
        ");
    }
}
