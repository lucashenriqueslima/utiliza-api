<?php

namespace App\Services\Aupet;

use Illuminate\Support\Facades\DB;

class AupetService
{
    public static function getCustomerInIlevaSolidyDatabase(string $cpf): array
    {

        $formatedCpf = self::formatCpfCnpj($cpf);

        $aupetCustomer = DB::connection('ileva')
            ->select("
            SELECT
            hai.nome `name`,
            hai.email,
            hai.telefone phone_number
            FROM hbrd_adm_indication hai
            WHERE hai.modelo = 'Aupet'
            AND hai.cpf_cnpj = '$formatedCpf'
        ");

        if (empty($aupetCustomer)) {
            return [];
        }

        $aupetCustomer[0]->activated = true;

        return $aupetCustomer;
    }

    public static function getCustomerInAupetDatabase(string $cpf): array
    {
        //add '.' and '-'
        $formatedCpf = self::formatCpfCnpj($cpf);

        $aupetCustomer = DB::connection('ileva_aupet')
            ->select("
                SELECT
    hap.nome name,
    hap.email,
    hap.telefone phone_number,
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
