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

    public static function getCustomerInAupetDatabase(string $cpf): array
    {
        //add '.' and '-'
        $formatedCpf = self::formatCpfCnpj($cpf);

        $aupetCustomer = DB::connection('ileva_aupet')
            ->select("
                SELECT
    hap.id AS pessoa_id,
    hap.cpf,
    hap.nome AS pessoa_nome,
    haa.id AS associado_id,
    hapr.id AS proposta_id,
    IF(hape_proposta.classificacao = 'ativada' OR hape_associado.classificacao = 'ativada', 'true', 'false') activated,
    JSON_ARRAYAGG(
        JSON_OBJECT(
            'id', IFNULL(hape_proposta.id, hape_associado.id),
            'name', IFNULL(hape_proposta.nome, hape_associado.nome),
            'specie', IFNULL(hape_proposta.id_especie , hape_associado.id_especie),
            'breed', IFNULL((SELECT titulo FROM hbrd_app_pet_raca WHERE id = hape_proposta.id_raca), (SELECT titulo FROM hbrd_app_pet_raca WHERE id = hape_associado.id_raca)),
				'size', IFNULL(hape_proposta.porte , hape_associado.porte),
				'gender', IFNULL(hape_proposta.sexo , hape_associado.sexo),
				'weight', IFNULL(hape_proposta.peso , hape_associado.peso)
        )
    ) AS pets
    FROM hbrd_app_pessoa hap
    LEFT JOIN hbrd_app_associado haa ON haa.id_pessoa = hap.id
    LEFT JOIN hbrd_app_proposta hapr ON hapr.id_pessoa = hap.id
    LEFT JOIN hbrd_app_pet hape_associado ON hape_associado.id_associado = haa.id
    LEFT JOIN hbrd_app_pet hape_proposta ON hape_proposta.id_proposta = hapr.id
    WHERE hap.cpf = '$formatedCpf'
    GROUP BY hap.id;
            ");

        if (empty($aupetCustomer)) {
            return [];
        }

        $aupetCustomer[0]->pets = json_decode($aupetCustomer[0]->pets, true);
        $aupetCustomer[0]->activated = $aupetCustomer[0]->activated == 'true' ? true : false;

        return $aupetCustomer;
    }

    private static function formatCpfCnpj($value)
    {
        $CPF_LENGTH = 11;
        $cnpj_cpf = preg_replace("/\D/", '', $value);

        if (strlen($cnpj_cpf) === $CPF_LENGTH) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
        }

        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
    }
}
