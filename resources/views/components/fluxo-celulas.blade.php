@props([
    'valores' => [],
    'modo' => 'detalhe', // 'detalhe' (traço se zero, opcionalmente clicável) ou 'total' (sempre mostra o valor)
    'clicavel' => false,
    'tipo' => null,
    'categoria' => null,
    'cliente' => null,
    'colorirPorSinal' => false,
    'classeCelula' => '',
    'subtotalModo' => 'soma', // 'soma', 'ultimo' ou 'traco'
    'totalModo' => 'soma', // 'soma', 'ultimo' ou 'traco'
    'finsDeSemana' => [], // array paralelo a $valores: true nas posições de sábado/domingo
    'destaques' => [], // array paralelo a $valores: true nos dias com lançamento manual de saldo
    'clicavelTotal' => false, // permite clique em modo 'total' para TODOS os dias (ex: linha Saldo Banco)
    'clicavelPorDia' => null, // array<int,bool> paralelo a $valores — clique só nos dias marcados; tem prioridade sobre clicavelTotal
    'lancAvulsoTipo' => null, // 'receber' ou 'pagar' — marca a célula pra mostrar no hover os lançamentos avulsos daquele dia (editar/excluir)
    'ordenarGrupo' => null, // 'receber' ou 'pagar' — marca a célula do dia (na linha de total do grupo) como clicável pra ordenar categorias/clientes por aquele dia
])

@php
    $qtdDias = count($valores);
@endphp

@foreach ($valores as $i => $v)
    @php
        $destacada = $destaques[$i] ?? false;
        $classeSinal = $colorirPorSinal ? ($v >= 0 ? 'valor-positivo' : 'valor-negativo') : '';
        $classeCelulaAtual = $modo === 'total' ? ($colorirPorSinal ? $classeSinal : $classeCelula) : '';
        $ehClicavelTotal = $modo === 'total' && ($clicavelPorDia !== null ? ($clicavelPorDia[$i] ?? false) : $clicavelTotal);
    @endphp
    <td class="text-right col-dia col-dia-semana-{{ intdiv($i, 7) + 1 }} {{ $classeCelulaAtual }} {{ ($modo === 'detalhe' && $clicavel && $v > 0) ? 'valor-clicavel' : '' }} {{ $ehClicavelTotal ? 'valor-clicavel saldo-banco-clicavel' : '' }} {{ ($finsDeSemana[$i] ?? false) ? 'dia-fim-semana-cel' : '' }} {{ $destacada ? 'dia-lancamento-manual' : '' }} {{ $lancAvulsoTipo ? 'lanc-avulso-cel' : '' }} {{ ($lancAvulsoTipo && $v > 0) ? 'tem-lancamento' : '' }} {{ $ordenarGrupo ? 'ordenar-dia-cel' : '' }}"
        @if ($destacada) title="Saldo bancário informado manualmente" @endif
        @if ($modo === 'detalhe' && $clicavel && $v > 0)
            data-tipo="{{ $tipo }}"
            data-categoria="{{ $categoria }}"
            @if ($cliente) data-cliente="{{ $cliente }}" @endif
            data-dia="{{ $i }}"
        @endif
        @if ($ehClicavelTotal)
            data-dia="{{ $i }}"
        @endif
        @if ($lancAvulsoTipo)
            data-tipo="{{ $lancAvulsoTipo }}"
            data-dia="{{ $i }}"
        @endif
        @if ($ordenarGrupo)
            data-ordenar-grupo="{{ $ordenarGrupo }}"
            data-dia="{{ $i }}"
            title="Ordenar por este dia"
        @endif
    >{{ $modo === 'detalhe' ? ($v > 0 ? number_format($v, 2, ',', '.') : '-') : number_format($v, 2, ',', '.') }}</td>

    @if ((($i + 1) % 7 === 0) && ($qtdDias > 7))
        @php
            $valorSemana = $subtotalModo === 'ultimo' ? $v : array_sum(array_slice($valores, $i - 6, 7));
            $classeSubtotal = $colorirPorSinal ? ($valorSemana >= 0 ? 'valor-positivo' : 'valor-negativo') : $classeCelula;
        @endphp
        <td class="text-right font-weight-bold col-subtotal-semana {{ $modo === 'total' ? $classeSubtotal : '' }}">
            @if ($subtotalModo === 'traco')
                -
            @else
                {{ number_format($valorSemana, 2, ',', '.') }}
            @endif
        </td>
    @endif
@endforeach

<td class="text-right font-weight-bold col-total">
    @if ($totalModo === 'traco')
        -
    @else
        {{ number_format($totalModo === 'ultimo' ? end($valores) : array_sum($valores), 2, ',', '.') }}
    @endif
</td>
