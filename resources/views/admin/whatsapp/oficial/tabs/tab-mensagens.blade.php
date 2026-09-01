{{-- Tab: Mensagens --}}
<div class="tab-pane fade" id="pane-mensagens" role="tabpanel">

    <div class="row">
        <div class="col-lg-8">
            <label class="text-muted">Conversa</label>
            <div class="wa-chat-wrap" id="wa-chat-lista">
                <div class="text-center text-muted py-4" id="wa-chat-vazio">Nenhuma mensagem ainda.</div>
            </div>
        </div>

        <div class="col-lg-4">
            <label class="text-muted">Enviar mensagem</label>
            <form id="form-nova-mensagem">
                @csrf
                <div class="form-group">
                    <label>Telefone (com DDD, com ou sem 55)</label>
                    <input type="text" class="form-control" name="telefone" id="input-mensagem-telefone" placeholder="ex: 41984042323" required>
                </div>
                <div class="form-group">
                    <label>Mensagem</label>
                    <textarea class="form-control" name="mensagem" id="input-mensagem-texto" rows="4" required></textarea>
                    <small class="text-muted">Texto livre só funciona dentro da janela de 24h (após o contato ter mandado mensagem pra este número).</small>
                </div>
                <button type="submit" class="btn btn-success" id="btn-enviar-mensagem">
                    <i class="fas fa-paper-plane mr-1"></i>Enviar
                </button>
            </form>
        </div>
    </div>

</div>
