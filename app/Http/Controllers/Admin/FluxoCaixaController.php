<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contas;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FluxoCaixaController extends Controller
{
    public function index(Request $request)
    {
        // Data Real = DT_VENCIMENTO puro. Data Personalizada = data ajustada pela regra
        // de compensação bancária (hoje só definida para CD_TIPOCONTA = 2). Personalizada é
        // o padrão ao abrir a tela sem parâmetro.
        $tipoData = $request->query('tipo_data') === 'real' ? 'real' : 'personalizada';

        // "ref" é qualquer data dentro da semana que o usuário quer ver — os botões de
        // navegação (anterior/hoje/próxima) só mudam esse valor. Sem parâmetro, ou com um
        // valor inválido, cai na semana atual.
        try {
            $inicio = Carbon::parse($request->query('ref'));
        } catch (\Exception $e) {
            $inicio = Carbon::now();
        }

        // Garante que a semana sempre comece no sábado e termine na sexta-feira
        while (!$inicio->isSaturday()) {
            $inicio->subDay();
        }

        // Quantidade de semanas exibidas lado a lado (visão horizontal). Navegação
        // (anterior/hoje/próxima) sempre desliza 1 semana, independente desse valor.
        $qtdSemanas = max(1, min(8, (int) $request->query('semanas', 1)));
        $qtdDias    = $qtdSemanas * 7;

        $dias = [];
        for ($i = 0; $i < $qtdDias; $i++) {
            $dias[] = $inicio->copy()->addDays($i);
        }

        $dtInicio = $dias[0]->format('Y-m-d');
        $dtFim    = $dias[$qtdDias - 1]->format('Y-m-d');

        // Cada CD_TIPOCONTA tem sua própria condição de forma de pagamento, então
        // consultamos um de cada vez e mesclamos os lançamentos antes de agrupar.
        $lancamentosReceber = array_merge(
            Contas::contasReceber($dtInicio, $dtFim, 2, ['BL', 'DB']),
            Contas::contasReceber($dtInicio, $dtFim, 12),
            Contas::contasReceber($dtInicio, $dtFim, 5)
        );

        $contasReceber = $this->agruparContas($lancamentosReceber, $dias, $tipoData, 'Clientes');

        $lancamentoManualEntrada = array_fill(0, $qtdDias, 0);

        $lancamentosPagar = array_merge(
            Contas::contasPagar($dtInicio, $dtFim, 1),
            Contas::contasPagar($dtInicio, $dtFim, 17),
            Contas::contasPagar($dtInicio, $dtFim, 29),
            Contas::contasPagar($dtInicio, $dtFim, 31)
        );

        $contasPagar = $this->agruparContas($lancamentosPagar, $dias, $tipoData, 'Fornecedores');

        $lancamentoManualSaida = array_fill(0, $qtdDias, 0);
        if ($qtdDias > 5) {
            $lancamentoManualSaida[5] = 1200;
        }

        $saldoInicial = 45230.50;

        $totalContasReceberPorDia = array_fill(0, $qtdDias, 0);
        foreach ($contasReceber as $categoria) {
            foreach ($categoria['totais'] as $i => $v) {
                $totalContasReceberPorDia[$i] += $v;
            }
        }

        $totalEntradasPorDia = array_fill(0, $qtdDias, 0);
        foreach ($totalContasReceberPorDia as $i => $v) {
            $totalEntradasPorDia[$i] = $v + $lancamentoManualEntrada[$i];
        }

        $totalContasPagarPorDia = array_fill(0, $qtdDias, 0);
        foreach ($contasPagar as $categoria) {
            foreach ($categoria['totais'] as $i => $v) {
                $totalContasPagarPorDia[$i] += $v;
            }
        }

        $totalSaidasPorDia = array_fill(0, $qtdDias, 0);
        foreach ($totalContasPagarPorDia as $i => $v) {
            $totalSaidasPorDia[$i] = $v + $lancamentoManualSaida[$i];
        }

        $saldoDia = [];
        $saldoAcumulado = [];
        $saldoAnterior = $saldoInicial;
        for ($i = 0; $i < $qtdDias; $i++) {
            $saldoDia[$i] = $totalEntradasPorDia[$i] - $totalSaidasPorDia[$i];
            $saldoAnterior += $saldoDia[$i];
            $saldoAcumulado[$i] = $saldoAnterior;
        }

        return view('admin.financeiro.fluxo-caixa', [
            'dias' => $dias,
            'finsDeSemana' => array_map(fn (Carbon $dia) => $dia->isWeekend(), $dias),
            'tipoData' => $tipoData,
            'qtdSemanas' => $qtdSemanas,
            'colspanSaldoInicial' => $qtdDias + ($qtdSemanas - 1),
            'refSemanaAtual' => $dias[0]->format('Y-m-d'),
            'refSemanaAnterior' => $dias[0]->copy()->subDays(7)->format('Y-m-d'),
            'refSemanaProxima' => $dias[0]->copy()->addDays(7)->format('Y-m-d'),
            'saldoInicial' => $saldoInicial,
            'contasReceber' => $contasReceber,
            'lancamentoManualEntrada' => $lancamentoManualEntrada,
            'contasPagar' => $contasPagar,
            'lancamentoManualSaida' => $lancamentoManualSaida,
            'totalContasReceberPorDia' => $totalContasReceberPorDia,
            'totalContasPagarPorDia' => $totalContasPagarPorDia,
            'totalEntradasPorDia' => $totalEntradasPorDia,
            'totalSaidasPorDia' => $totalSaidasPorDia,
            'saldoDia' => $saldoDia,
            'saldoAcumulado' => $saldoAcumulado,
        ]);
    }

    /**
     * Agrupa os lançamentos de contas (a receber ou a pagar) por categoria (DS_FORMAPAGTO) e,
     * dentro de cada categoria, por pessoa — cada nível com os valores distribuídos por dia da semana.
     *
     * @param  array          $lancamentos      Retorno de Contas::contasReceber()/contasPagar()
     * @param  Carbon\Carbon[] $dias             Os 7 dias (sáb a sex) exibidos no fluxo
     * @param  string         $tipoData         'real' (DT_VENCIMENTO) ou 'personalizada' (data ajustada)
     * @param  string         $prefixoCategoria Usado como fallback de categoria quando DS_FORMAPAGTO vem nulo
     * @return array<string, array{totais: array<int,float>, detalhe: array<string, array<int,float>>}>
     */
    private function agruparContas(array $lancamentos, array $dias, string $tipoData, string $prefixoCategoria): array
    {
        $qtdDias = count($dias);

        $indicePorData = [];
        foreach ($dias as $i => $dia) {
            $indicePorData[$dia->format('Y-m-d')] = $i;
        }

        $grupos = [];
        foreach ($lancamentos as $lancamento) {
            $dtVencimento = Carbon::parse($lancamento->DT_VENCIMENTO);
            $dataReferencia = $tipoData === 'personalizada'
                ? $this->calcularDataPersonalizada($dtVencimento, (int) $lancamento->CD_TIPOCONTA)
                : $dtVencimento;

            $dataChave = $dataReferencia->format('Y-m-d');
            if (!isset($indicePorData[$dataChave])) {
                continue;
            }
            $i = $indicePorData[$dataChave];

            $categoria = trim($lancamento->DS_FORMAPAGTO ?? ($prefixoCategoria . ' - ' . $lancamento->DS_TIPOCONTA));
            $cliente = $lancamento->NM_PESSOA;
            $valor = (float) $lancamento->VL_SALDO;

            if (!isset($grupos[$categoria])) {
                $grupos[$categoria] = [
                    'totais' => array_fill(0, $qtdDias, 0),
                    'detalhe' => [],
                    'lancamentos' => array_fill(0, $qtdDias, []),
                ];
            }
            $grupos[$categoria]['totais'][$i] += $valor;

            if (!isset($grupos[$categoria]['detalhe'][$cliente])) {
                $grupos[$categoria]['detalhe'][$cliente] = array_fill(0, $qtdDias, 0);
            }
            $grupos[$categoria]['detalhe'][$cliente][$i] += $valor;

            $grupos[$categoria]['lancamentos'][$i][] = [
                'nr_lancamento' => $lancamento->NR_LANCAMENTO,
                'nm_pessoa' => $cliente,
                'dt_real' => $dtVencimento->format('d/m/Y'),
                'valor' => $valor,
            ];
        }

        return $grupos;
    }

    /**
     * Calcula a data de compensação bancária (Data Personalizada) a partir do vencimento.
     *
     * Regras:
     * - CD_TIPOCONTA 2 (Contas a Receber) e 12 (Cartão de Crédito a Receber - boleto/débito):
     *   compensa D+1, exceto:
     *     - vencimento na sexta compensa na segunda-feira seguinte;
     *     - vencimento no sábado ou domingo compensa na terça-feira seguinte.
     * - CD_TIPOCONTA 1, 17, 29 e 31 (Contas a Pagar): compensa D+1, exceto:
     *     - vencimento na sexta, sábado ou domingo compensa na segunda-feira seguinte
     *       (próximo dia útil).
     * - CD_TIPOCONTA 5 (Cheque a Receber): compensa D+2, exceto:
     *     - vencimento na quinta compensa na segunda-feira seguinte;
     *     - vencimento na sexta compensa na terça-feira seguinte;
     *     - vencimento no sábado ou domingo compensa na quarta-feira seguinte.
     *
     * Demais tipos de conta ainda não têm regra própria e usam o vencimento sem ajuste.
     */
    private function calcularDataPersonalizada(Carbon $dtVencimento, int $cdTipoConta): Carbon
    {
        $offsetPorDiaSemana = match ($cdTipoConta) {
            2, 12 => [
                Carbon::MONDAY    => 1,
                Carbon::TUESDAY   => 1,
                Carbon::WEDNESDAY => 1,
                Carbon::THURSDAY  => 1,
                Carbon::FRIDAY    => 3,
                Carbon::SATURDAY  => 3,
                Carbon::SUNDAY    => 2,
            ],
            1, 17, 29, 31 => [
                Carbon::MONDAY    => 1,
                Carbon::TUESDAY   => 1,
                Carbon::WEDNESDAY => 1,
                Carbon::THURSDAY  => 1,
                Carbon::FRIDAY    => 3,
                Carbon::SATURDAY  => 2,
                Carbon::SUNDAY    => 1,
            ],
            5 => [
                Carbon::MONDAY    => 2,
                Carbon::TUESDAY   => 2,
                Carbon::WEDNESDAY => 2,
                Carbon::THURSDAY  => 4,
                Carbon::FRIDAY    => 4,
                Carbon::SATURDAY  => 4,
                Carbon::SUNDAY    => 3,
            ],
            default => null,
        };

        if ($offsetPorDiaSemana === null) {
            return $dtVencimento->copy();
        }

        return $dtVencimento->copy()->addDays($offsetPorDiaSemana[$dtVencimento->dayOfWeek]);
    }
}
