<?php

namespace App\Console\Commands;

use App\Models\Associate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class HandleIlevaDependentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:handle-ileva-dependents-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ilevaAssocitesWithDependents = $this->getIlevaAssociatesWithDependets();


        foreach ($ilevaAssocitesWithDependents as $ilevaAssociteWithDependents) {

            $contractQuestions = json_decode($ilevaAssociteWithDependents['perguntas_contrato'], true);

            $associate = $this->updateOrCreateDependentAssociate($ilevaAssociteWithDependents);

            $dependents = array_filter($contractQuestions, function ($item) {
                return str_starts_with($item['variavel'], '{[dependente')
                    && strlen($item['resposta']) > 6
                    && preg_match('/[a-zA-Z]/', $item['resposta'])
                    && stripos($item['resposta'], 'não') === false
                    && stripos($item['resposta'], 'nao') === false;
            });

            foreach ($dependents as $dependent) {
                $associate->dependents()->updateOrCreate(
                    [
                        'name' => $dependent['resposta'],
                        'association' => $ilevaAssociteWithDependents['association'],
                    ],
                    [
                        'situation' => $ilevaAssociteWithDependents['situacao'] ?? 'Não Informado',
                        'benefit' => $ilevaAssociteWithDependents['beneficio'],
                        'contract_date' => $ilevaAssociteWithDependents['contract_date'],
                    ]
                );
            }
        }
    }

    private function updateOrCreateDependentAssociate(array $associate): Associate
    {

        if (empty($associate['id_associado'])) {
            return Associate::updateOrCreate(
                [
                    'cpf' => $associate['cpf'],
                    'name' => $associate['associado'],
                    'ileva_associate_id' => null
                ],
                [
                    'name' => $associate['associado'],
                    'email' => $associate['email'],
                    'phone' => $associate['tel_celular'],
                ]
            );
        }

        return Associate::updateOrCreate(
            ['ileva_associate_id' => $associate['id_associado']],
            [
                'name' => $associate['associado'],
                'email' => $associate['email'],
                'phone' => $associate['tel_celular'],
                'cpf' => $associate['cpf'],
            ]
        );
    }

    private function getIlevaAssociatesWithDependets(): array
    {
        $ilevaAssociatesWithDependets = DB::connection('ileva')
            ->select("
        SELECT
    hav.id,
    hav.id_associado,
    hap.nome AS associado,
    hap.email,
    hap.tel_celular,
    hap.cpf,
    hav.id_plan_item,
    hapiv.id_plan,
    hapiv.val_mensal AS valor_plan,
    hav.id_situacao,
    has.nome AS situacao,
    hav.id_consultor,
    hac.nome AS consultor,
    hat.equipe,
    hasc.nome AS regional,
    hav.placa,
    hav.chassi,
    hav.dt_contrato,
    hmuc.cidade,
    hmus.estado,
    hmus.uf,
    habv.id_beneficio,
    habv.id_veiculo,
    habv.created_at,
    habv.valor_beneficio,
    hab.nome AS beneficio,
    hab.valor,
    hab.delete_at AS delet_banco_dados,
    hait.id_contrato,
    hait.perguntas_contrato,
    'solidy' AS association,
    hav.dt_contrato contract_date
FROM hbrd_asc_veiculo hav
LEFT JOIN hbrd_asc_associado haa ON haa.id = hav.id_associado
LEFT JOIN hbrd_asc_pessoa hap ON hap.id = haa.id_pessoa
LEFT JOIN hbrd_asc_situacao has ON has.id = hav.id_situacao
LEFT JOIN hbrd_main_util_city hmuc ON hmuc.id = hap.id_cidade
LEFT JOIN hbrd_main_util_state hmus ON hmus.id = hmuc.id_estado
LEFT JOIN hbrd_asc_beneficio_veiculo habv ON habv.id_veiculo = hav.id
LEFT JOIN hbrd_adm_benefit hab ON hab.id = habv.id_beneficio
LEFT JOIN hbrd_adm_consultant hac ON hac.id = hav.id_consultor
LEFT JOIN hbrd_adm_team hat ON hat.id = hac.id_equipe_
LEFT JOIN hbrd_adm_sectional hasc ON hasc.id = hat.id_regional
LEFT JOIN hbrd_adm_plan_item hapiv ON hapiv.id = hav.id_plan_item
LEFT JOIN hbrd_adm_indication_termo hait ON hait.id_indicacao = hav.id_indicacao
WHERE habv.id_beneficio IN (319,585, 42, 43, 50, 131, 175, 207, 237, 238, 267, 277, 280, 309, 310, 335, 366, 416, 425, 427, 688, 68, 69, 70, 71, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 127, 128, 525, 839)
AND hait.perguntas_contrato LIKE '%{[dependente%'
");

        $ilevaAssociatesWithDependetsArray = json_decode(json_encode($ilevaAssociatesWithDependets), true);

        $ilevaAssociatesWithDependetsAfterContract = DB::connection('ileva')
            ->select("
SELECT
hai.nome as associado,
hai.create_at as contract_date,
hai.cpf_cnpj as cpf,
hab.nome AS beneficio,
hait.perguntas_contrato,
hai.telefone tel_celular,
hai.email,
'solidy' AS association
FROM  hbrd_adm_indication hai
LEFT JOIN hbrd_adm_indication_termo hait ON hait.id_indicacao = hai.id
LEFT JOIN hbrd_adm_plan_item hapi ON hapi.id = hai.id_plan_item
LEFT JOIN hbrd_adm_plan hap ON hap.id = hapi.id_plan
LEFT JOIN hbrd_adm_benefit_indication habi ON habi.id_indication = hai.id
LEFT JOIN hbrd_adm_benefit hab ON hab.id = habi.id_benefit
WHERE
    hai.modelo IN ('Elba Weekend 1.5 i.e. 2p e 4p',
                   'Elba 1.6i.e/Top/CSL/ 1.6i.e/1.5 2p e 4p',
                   'Elba S 1.6/ 1.5ie / 1.5 / 1.3')
    AND hai.classificacao = 'arquivada'
    AND hab.nome IS NOT NULL
    AND hait.perguntas_contrato IS NOT NULL
    AND hab.id IN (319,585, 42, 43, 50, 131, 175, 207, 237, 238, 267, 277, 280, 309, 310, 335, 366, 416, 425, 427, 688, 68, 69, 70, 71, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 127, 128, 525, 839)
");

        $ilevaAssociatesWithDependetsAfterContractArray = json_decode(json_encode($ilevaAssociatesWithDependetsAfterContract), true);

        return array_merge($ilevaAssociatesWithDependetsArray, $ilevaAssociatesWithDependetsAfterContractArray);
    }
}