{{-- Tab: Gerenciador de Modelos --}}
<div class="tab-pane fade show active" id="pane-modelos" role="tabpanel">

    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-outline-secondary btn-sm" id="btn-sincronizar">
            <i class="fas fa-sync-alt mr-1"></i> Sincronizar status
        </button>
    </div>

    <table class="table table-bordered table-sm" id="tabela-templates">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Idioma</th>
                <th>Status</th>
                <th style="width:260px;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($templates as $template)
            <tr data-id="{{ $template->id }}">
                <td>{{ $template->nome }}</td>
                <td>{{ $template->categoria }}</td>
                <td>{{ $template->idioma }}</td>
                <td>
                    @php
                        $cores = ['rascunho' => 'secondary', 'enviado' => 'info', 'pending' => 'info', 'approved' => 'success', 'aprovado' => 'success', 'rejected' => 'danger', 'rejeitado' => 'danger'];
                        $cor = $cores[$template->status] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $cor }}">{{ $template->status }}</span>
                    @if($template->motivo_rejeicao)
                        <br><small class="text-danger">{{ $template->motivo_rejeicao }}</small>
                    @endif
                </td>
                <td>
                    @if(in_array($template->status, ['rascunho', 'rejeitado', 'rejected']))
                        <button class="btn btn-outline-primary btn-xs btn-editar"
                            data-id="{{ $template->id }}"
                            data-nome="{{ $template->nome }}"
                            data-categoria="{{ $template->categoria }}"
                            data-idioma="{{ $template->idioma }}"
                            data-header-tipo="{{ $template->header['format'] ?? '' }}"
                            data-header-texto="{{ $template->header['text'] ?? '' }}"
                            data-tem-arquivo="{{ $template->header_documento_path ? '1' : '' }}"
                            data-corpo="{{ $template->corpo }}"
                            data-exemplos="{{ $template->exemplos }}"
                            data-rodape="{{ $template->rodape }}">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </button>
                        <button class="btn btn-primary btn-xs btn-enviar" data-id="{{ $template->id }}">
                            <i class="fas fa-paper-plane mr-1"></i>Enviar p/ análise
                        </button>
                    @endif
                    <button class="btn btn-outline-danger btn-xs btn-excluir" data-id="{{ $template->id }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr>

    <h5 class="mb-3" id="titulo-form-template">Novo template (rascunho)</h5>

    <div class="row">
        {{-- Formulário --}}
        <div class="col-lg-8">
            <form id="form-novo-template" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="template_id" id="input-template-id">

                <div class="row">
                    <div class="col-md-5 form-group">
                        <label>Nome (identificador, minúsculo/underline)</label>
                        <input type="text" class="form-control" name="nome" id="input-nome" placeholder="ex: nota_cliente" required>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Categoria</label>
                        <select class="form-control" name="categoria" id="input-categoria" required>
                            <option value="UTILITY">Utilidade</option>
                            <option value="MARKETING">Marketing</option>
                            <option value="AUTHENTICATION">Autenticação</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Idioma</label>
                        <input type="text" class="form-control" name="idioma" id="input-idioma" value="pt_BR" required>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Cabeçalho</label>
                        <select class="form-control" name="header_tipo" id="select-header-tipo">
                            <option value="">Nenhum</option>
                            <option value="TEXT">Texto</option>
                            <option value="DOCUMENT">Documento (PDF)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="grupo-header-texto" style="display:none;">
                    <label>Texto do cabeçalho</label>
                    <input type="text" class="form-control" name="header_texto" id="input-header-texto" maxlength="60">
                </div>

                <div class="form-group" id="grupo-header-arquivo" style="display:none;">
                    <label>PDF de amostra do cabeçalho</label>
                    <input type="file" class="form-control-file" name="header_arquivo" id="input-header-arquivo" accept="application/pdf">
                    <small class="text-muted" id="texto-arquivo-atual"></small>
                    <br><small class="text-muted">Obrigatório — a Meta exige um exemplo do documento pra aprovar o template.</small>
                </div>

                <div class="form-group">
                    <label>Corpo da mensagem (use @{{1}}, @{{2}}... para variáveis)</label>
                    <textarea class="form-control" name="corpo" id="input-corpo" rows="4" required></textarea>
                    <small class="text-muted">Não pode começar nem terminar logo em cima de uma variável — precisa de texto antes/depois.</small>
                </div>

                <div class="form-group" id="exemplos-wrapper" style="display:none;">
                    <label>Amostras de variáveis</label>
                    <div id="exemplos-lista"></div>
                    <small class="text-muted">Obrigatório — a Meta rejeita template com variável sem exemplo de preenchimento.</small>
                </div>
                <input type="hidden" name="exemplos" id="input-exemplos-hidden">

                <div class="form-group">
                    <label>Rodapé (opcional, texto fixo)</label>
                    <input type="text" class="form-control" name="rodape" id="input-rodape" maxlength="60">
                </div>

                <button type="submit" class="btn btn-success" id="btn-salvar-template">
                    <i class="fas fa-save mr-1"></i>Salvar rascunho
                </button>
                <button type="button" class="btn btn-outline-secondary d-none" id="btn-cancelar-edicao">
                    Cancelar edição
                </button>
            </form>
        </div>

        {{-- Prévia --}}
        <div class="col-lg-4">
            <label class="text-muted">Prévia</label>
            <div class="wa-preview-wrap">
                <div class="wa-preview-bubble">
                    <div class="wa-preview-header" id="preview-header"></div>
                    <div class="wa-preview-body" id="preview-body">A mensagem aparece aqui...</div>
                    <div class="wa-preview-footer" id="preview-footer"></div>
                    <div class="wa-preview-time">10:32</div>
                </div>
            </div>
        </div>
    </div>

</div>
