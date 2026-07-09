@props([
    'valores' => [],
    'modo' => 'detalhe', // 'detalhe' (traço se zero, opcionalmente clicável) ou 'total' (sempre mostra o valor)
    'clicavel' => false,
    'tipo' => null,
    'categoria' => null,
    'cliente' => null,
    'colorirPorSinal' => false,
    'classeCelula' => '',
    'subtotalModo' => 'soma', // 'soma' ou 'ultimo' (saldo acumulado no fim da semana)
    'totalModo' => 'soma', // 'soma', 'ultimo' ou 'traco'
    'finsDeSemana' => [], // array paralelo a $valores: true nas posições de sábado/domingo
])

@php
    $qtdDias = count($valores);
@endphp

@foreach ($valores as $i => $v)
    @php
        $classeSinal = $colorirPorSinal ? ($v >= 0 ? 'valor-positivo' : 'valor-negativo') : '';
        $classeCelulaAtual = $modo === 'total' ? ($colorirPorSinal ? $classeSinal : $classeCelula) : '';
    @endphp
    <td class="text-right {{ $classeCelulaAtual }} {{ ($modo === 'detalhe' && $clicavel && $v > 0) ? 'valor-clicavel' : '' }} {{ ($finsDeSemana[$i] ?? false) ? 'dia-fim-semana-cel' : '' }}"
        @if ($modo === 'detalhe' && $clicavel && $v > 0)
            data-tipo="{{ $tipo }}"
            data-categoria="{{ $categoria }}"
            @if ($cliente) data-cliente="{{ $cliente }}" @endif
            data-dia="{{ $i }}"
        @endif
    >{{ $modo === 'detalhe' ? ($v > 0 ? number_format($v, 2, ',', '.') : '-') : number_format($v, 2, ',', '.') }}</td>

    @if ((($i + 1) % 7 === 0) && ($i + 1 < $qtdDias))
        @php
            $valorSemana = $subtotalModo === 'ultimo' ? $v : array_sum(array_slice($valores, $i - 6, 7));
            $classeSubtotal = $colorirPorSinal ? ($valorSemana >= 0 ? 'valor-positivo' : 'valor-negativo') : $classeCelula;
        @endphp
        <td class="text-right font-weight-bold col-subtotal-semana {{ $modo === 'total' ? $classeSubtotal : '' }}">
            {{ number_format($valorSemana, 2, ',', '.') }}</td>
    @endif
@endforeach

<td class="text-right font-weight-bold col-total">
    @if ($totalModo === 'traco')
        -
    @else
        {{ number_format($totalModo === 'ultimo' ? end($valores) : array_sum($valores), 2, ',', '.') }}
    @endif
</td>
