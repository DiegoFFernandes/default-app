<div class="tab-pane fade show active" id="pane-junsoft" role="tabpanel" aria-labelledby="tab-junsoft">
    <div class="row">
        <section class="col-md-4">
            <div class="card">
                <div class="card-header" style="">
                    <h3 class="card-title" style="text-align: center">Pesquisar Envios Automaticos
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div class="mt-1">
                        <small class="badge badge-danger badge-date-follow"></small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="padding-top: 15px">
                                <label for="search-number">Nº Nota/Boleto</label>
                                <input type="number" class="form-control form-control-sm" id="search-number"
                                    placeholder="Número" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group border-bottom">
                                <label for="search-number">Pesquisa Avançada</label>
                                <div class="float-right">
                                    <button type="button" class="btn btn-box-tool" data-toggle="collapse"
                                        data-target="#search-advanced"><i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="collapse" id="search-advanced">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cd_pessoa">Cd. Cliente</label>
                                    <input type="number" class="form-control form-control-sm" id="cd_pessoa"
                                        placeholder="Código">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="cd_pessoa">Cpf/CNPJ</label>
                                    <input type="text" class="form-control form-control-sm" id="cpf_cnpj"
                                        placeholder="Cpf/CNPJ">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nm_pessoa">Razão Social</label>
                                    <input type="text" class="form-control form-control-sm" id="nm_pessoa"
                                        placeholder="Nome Pessoa">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ds_email_pessoa">Email</label>
                                    <input type="email" class="form-control form-control-sm" id="ds_email_pessoa"
                                        placeholder="Email Pessoa">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nr_contexto">Tipo de Disparo</label>
                                    <select class="form-control form-control-sm" name="nr_contexto" id="nr_contexto"
                                        style="width: 100%;">
                                        <option value="0" selected="selected">Selecione</option>
                                        @foreach ($contexto as $c)
                                            <option value="{{ $c->NR_CONTEXTO }}">{{ $c->DS_CONTEXTO }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Periodo</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                        <input type="text" class="form-control form-control-sm float-right"
                                            id="daterange" value="" autocomplete="off" placeholder="Periodo">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-success float-right"
                        id="submit-seach">Pesquisar</button>
                </div>
            </div>
        </section>
        <section class="col-md-8">
            <div class="card">
                <div class="card-header" style="">
                    <h3 class="card-title">Resultado pesquisa - Envios Automaticos</h3>
                </div>
                <div class="card-body">
                    <table id="table-search" class="table compact table-font-small table-bordered table-striped">
                    </table>
                </div>

            </div>
        </section>
    </div>
</div>
