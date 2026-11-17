<?php

namespace Database\Seeders;

use App\Models\CatalogoRisco;
use Illuminate\Database\Seeder;

class CatalogoRiscoSeeder extends Seeder
{
    public function run(): void
    {
        $riscos = [
            [
                'categoria'             => 'Elétrica',
                'descricao_etapa'       => 'Manutenção e inspeção em painel elétrico',
                'riscos_envolvidos'     => 'Choque elétrico, arco elétrico, curto-circuito e queimaduras.',
                'epis_medidas_controle' => 'EPIs: Capacete classe B, luvas isolantes de borracha, óculos de proteção, calçado de segurança sem partes metálicas e vestimenta antichama (NR-10). Medidas: Desenergizar o circuito, aplicar bloqueio e etiquetagem (LOTO) e testar ausência de tensão.',
            ],
            [
                'categoria'             => 'Trabalho em Altura',
                'descricao_etapa'       => 'Instalação ou manutenção acima de 2 metros do nível inferior',
                'riscos_envolvidos'     => 'Queda de pessoa com diferença de nível, queda de ferramentas e materiais.',
                'epis_medidas_controle' => 'EPIs: Cinto de segurança tipo paraquedista com talabarte duplo, capacete com jugular, luvas de vaqueta e botina de segurança (NR-35). Medidas: Isolar e sinalizar a área inferior, ancorar o talabarte em ponto resistente e amarrar ferramentas.',
            ],
            [
                'categoria'             => 'Trabalho a Quente',
                'descricao_etapa'       => 'Corte e soldagem de estruturas metálicas',
                'riscos_envolvidos'     => 'Projeção de partículas incandescentes, radiação não ionizante, fumos metálicos, incêndio e queimaduras.',
                'epis_medidas_controle' => 'EPIs: Máscara de solda com filtro adequado, avental, mangote e luvas de raspa, perneira e respirador PFF2 para fumos metálicos. Medidas: Remover materiais inflamáveis do entorno, instalar biombo de proteção e manter extintor próximo.',
            ],
            [
                'categoria'             => 'Máquinas e Equipamentos',
                'descricao_etapa'       => 'Operação em torno mecânico, fresadora ou esmerilhadeira',
                'riscos_envolvidos'     => 'Aprisionamento de membros em partes móveis, projeção de cavacos/fagulhas e ruído elevado.',
                'epis_medidas_controle' => 'EPIs: Óculos de ampla visão ou protetor facial, protetor auricular tipo plugue/concha e calçado de segurança (NR-12). Medidas: Proibido uso de luvas, anéis, relógios ou roupas folgadas próximas a eixos rotativos; verificar proteções fixas.',
            ],
            [
                'categoria'             => 'Produtos Químicos',
                'descricao_etapa'       => 'Manuseio de solventes, tintas, óleos ou desengraxantes',
                'riscos_envolvidos'     => 'Inalação de vapores tóxicos, contato dérmico, irritação ocular e princípio de incêndio.',
                'epis_medidas_controle' => 'EPIs: Luvas nitrílicas, óculos de proteção química, avental impermeável e respirador com filtro para vapores orgânicos. Medidas: Consultar a FISPQ do produto, manter o local ventilado e longe de fontes de ignição.',
            ],
            [
                'categoria'             => 'Movimentação de Cargas',
                'descricao_etapa'       => 'Transporte e içamento de peças pesadas',
                'riscos_envolvidos'     => 'Esmagamento de membros, queda de carga suspensa e lesão lombar.',
                'epis_medidas_controle' => 'EPIs: Capacete de segurança, luvas de vaqueta/raspa e botina com biqueira de aço. Medidas: Inspecionar cintas e cabos antes do içamento, não permanecer sob carga suspensa e manter postura ergonômica.',
            ],
        ];

        foreach ($riscos as $risco) {
            CatalogoRisco::firstOrCreate(
                ['descricao_etapa' => $risco['descricao_etapa']],
                $risco
            );
        }
    }
}