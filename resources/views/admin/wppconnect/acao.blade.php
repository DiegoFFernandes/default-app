<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @if(isset($solicitacao))
    {{-- Meta tags OG para preview no WhatsApp --}}
    <meta property="og:title"       content="Solicitação de Compra #{{ $solicitacao->CD_SOLICITACAO }} — Aguardando aprovação">
    <meta property="og:description" content="R$ {{ number_format($solicitacao->VL_TOTAL, 2, ',', '.') }} · {{ $solicitacao->NM_EMPRESA }} · {{ count($itens) }} {{ count($itens) === 1 ? 'item' : 'itens' }}">
    <meta property="og:type"        content="website">
    <title>Aprovação #{{ $solicitacao->CD_SOLICITACAO }}</title>
    @else
    <title>Aprovação de Compra</title>
    @endif

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background: #f4f6f9; }
        .card-acao { max-width: 560px; margin: 40px auto; }
        .table-itens th, .table-itens td { font-size: 13px; }
        .table-itens th { background: #f8f9fa; }
        .btn-acao { font-size: 16px; padding: 14px; }
    </style>
</head>
<body>
<div class="container card-acao">

    {{-- ERRO --}}
    @if(isset($erro))
        <div class="card card-outline card-danger mt-5">
            <div class="card-body text-center py-5">
                <i class="fas fa-times-circle text-danger" style="font-size:60px;"></i>
                <h4 class="mt-3">{{ $erro }}</h4>
                <p class="text-muted">Entre em contato com o solicitante ou acesse o sistema.</p>
            </div>
        </div>

    {{-- SUCESSO --}}
    @elseif(isset($sucesso))
        <div class="card card-outline {{ $acao === 'aprovar' ? 'card-success' : 'card-danger' }} mt-5">
            <div class="card-body text-center py-5">
                @if($acao === 'aprovar')
                    <i class="fas fa-check-circle text-success" style="font-size:60px;"></i>
                    <h4 class="mt-3 text-success">{{ $sucesso }}</h4>
                @else
                    <i class="fas fa-times-circle text-danger" style="font-size:60px;"></i>
                    <h4 class="mt-3 text-danger">{{ $sucesso }}</h4>
                @endif
                <p class="text-muted mt-2">Você já pode fechar esta janela.</p>
            </div>
        </div>

    {{-- PÁGINA PRINCIPAL COM BOTÕES --}}
    @else
        <div class="card card-outline card-primary mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Solicitação de Compra #{{ $solicitacao->CD_SOLICITACAO }}
                </h5>
            </div>
            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Empresa</small>
                        <strong>{{ $solicitacao->NM_EMPRESA }}</strong>
                    </div>
                    <div class="col-6 text-right">
                        <small class="text-muted d-block">Valor Total</small>
                        <strong>R$ {{ number_format($solicitacao->VL_TOTAL, 2, ',', '.') }}</strong>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Sua função</small>
                        <strong>{{ $etapa->DS_CARGO }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Etapa</small>
                        <strong>{{ $etapa->NR_ORDEM }}ª aprovação</strong>
                    </div>
                </div>

                @if($solicitacao->DS_JUSTIFICATIVA)
                <div class="mb-3">
                    <small class="text-muted d-block">Justificativa</small>
                    {{ $solicitacao->DS_JUSTIFICATIVA }}
                </div>
                @endif

                <table class="table table-sm table-bordered table-itens mb-4">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-center" style="width:60px;">Qtd</th>
                            <th style="width:70px;">Unidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($itens as $item)
                        <tr>
                            <td>{{ $item->DS_ITEM }}</td>
                            <td class="text-center">{{ $item->QT_ITEM }}</td>
                            <td>{{ $item->DS_UNIDADE ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Ação = reprovar: mostra campo de motivo --}}
                @if($acao === 'reprovar')
                    <form method="POST" action="{{ route('wppconnect.acao.processar') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="acao" value="reprovar">
                        <div class="form-group">
                            <label><strong>Motivo da reprovação <span class="text-danger">*</span></strong></label>
                            <textarea class="form-control" name="motivo" rows="3"
                                      placeholder="Descreva o motivo..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger btn-block btn-acao">
                            <i class="fas fa-times mr-2"></i>Confirmar Reprovação
                        </button>
                        <a href="{{ route('wppconnect.acao.show', ['token' => $token]) }}"
                           class="btn btn-outline-secondary btn-block mt-2">
                            Voltar
                        </a>
                    </form>

                {{-- Ação = aprovar: confirmação direta --}}
                @elseif($acao === 'aprovar')
                    <form method="POST" action="{{ route('wppconnect.acao.processar') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="acao" value="aprovar">
                        <button type="submit" class="btn btn-success btn-block btn-acao">
                            <i class="fas fa-check mr-2"></i>Confirmar Aprovação
                        </button>
                        <a href="{{ route('wppconnect.acao.show', ['token' => $token]) }}"
                           class="btn btn-outline-secondary btn-block mt-2">
                            Voltar
                        </a>
                    </form>

                {{-- Sem ação: mostra os dois botões --}}
                @else
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('wppconnect.acao.show', ['token' => $token, 'acao' => 'aprovar']) }}"
                               class="btn btn-success btn-block btn-acao">
                                <i class="fas fa-check d-block mb-1" style="font-size:22px;"></i>
                                Aprovar
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('wppconnect.acao.show', ['token' => $token, 'acao' => 'reprovar']) }}"
                               class="btn btn-danger btn-block btn-acao">
                                <i class="fas fa-times d-block mb-1" style="font-size:22px;"></i>
                                Reprovar
                            </a>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    @endif

</div>
</body>
</html>
