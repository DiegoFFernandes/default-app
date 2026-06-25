@extends('layouts.master')
@section('title', 'Notas Divergentes')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="card card-dark card-outline card-outline-tabs">
                    @include('admin.comercial.vendedor-nota.tabs.nav-tabs')
                    <div class="card-body">
                        <div class="tab-content">
                            @include('admin.comercial.vendedor-nota.tabs.notas-divergentes')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.comercial.vendedor-nota.modals.modal-alterar-vendedor')
    </section>
@stop

@section('css')
    <style>
        .texto-curto {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 250px;
        }

        .grupo-a {
            background-color: #ffffff !important;
        }

        .grupo-b {
            background-color: #f2f2f2 !important;
        }
    </style>
@stop

@section('js')
    <script>
        window.routes = {
            languageDatatables: "{{ asset('vendor/datatables/pt-BR.json') }}",
            notaVendedorDivergentes: "{{ route('get-nota-vendedor-divergentes') }}",
            substituirItemVendedorNota: "{{ route('substituir-item-vendedor-nota') }}",
            searchVendedor: "{{ route('get-search-vendedor') }}",
            updateVendedorNota: "{{ route('update-alterar-vendedor-nota') }}",
            token: "{{ csrf_token() }}"
        };

        let tableNotasVendedorDivergentes;

        $(document).ready(function() {
            initTableNotasVendedorDivergentes();
        });

        $(document).on("click", "#btn-alterar-vendedor", function() {
            const selectedData = getVendedorNotaSelecionados();

            if (selectedData.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nenhuma nota selecionada',
                    text: 'Por favor, selecione pelo menos uma nota para alterar o vendedor.',
                });
                return;
            }

            let vendedorNota = false;

            selectedData.forEach(nota => {
                // Verifica se a nota possui um vendedor associado (CD_VENDEDOR vazio)
                if (nota.CD_VENDEDOR === "") {
                    vendedorNota = true;
                    return false; // Sai do loop assim que encontrar uma nota com vendedor
                }
            });

            if (vendedorNota) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nota sem vendedor associado',
                    text: 'Uma ou mais notas não possuem um vendedor associado. Por favor, verifique as notas selecionadas e tente novamente.',
                });
                return;
            }

            $.ajax({
                type: "POST",
                url: window.routes.substituirItemVendedorNota,
                data: {
                    notas: selectedData,
                    _token: window.routes.token
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Processando...',
                        text: 'Aguarde enquanto processamos as notas selecionadas.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            text: response.message,
                        });
                        $(tableNotasVendedorDivergentes.table().container()).find('.dt-row-checkbox-vendedor, .dt-select-all-vendedor').prop('checked', false);
                        updateVendedorNotaBadge(0);
                        tableNotasVendedorDivergentes.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao alterar vendedor',
                            text: response.message ||
                                'Ocorreu um erro ao tentar alterar o vendedor das notas selecionadas.',
                        });
                    }
                }
            });

        });

        $(document).on("click", "#btn-manter-vendedor", function() {
            const selectedData = getVendedorNotaSelecionados();

            if (selectedData.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nenhuma nota selecionada',
                    text: 'Por favor, selecione pelo menos uma nota para manter o vendedor.',
                });
                return;
            }

        });

        $(document).on("click", ".editar-vendedor-nota", function() {
            const cd_empresa = $(this).data('cd_empresa');
            const nrLancamento = $(this).data('nr_lancamento');
            const nrNota = $(this).data('nr_nota');
            const vendedorAtual = $(this).data('nm_vendedor_nota');
            const nm_pessoa = $(this).data('nm_pessoa');

            $('#cd_empresa').val(cd_empresa);
            $('#nr_lancamento').val(nrLancamento);
            $('#nr_nota').val(nrNota);
            $('#vendedor_atual').val(vendedorAtual);
            $('#nm_pessoa').val(nm_pessoa);

            inicializaSelect2Lista({
                route: window.routes.searchVendedor,
                selectId: "#cd_vendedor_novo",
                placeholder: "Selecione vendedor novo",
                modalParent: "#modal-alterar-vendedor",
                textField: "NM_VENDEDOR",
                valueField: "CD_VENDEDOR",
            });

            $('#modal-alterar-vendedor').modal('show');
        });

        $(document).on("click", "#btn-update-vendedor-nota", function() {
            const cd_empresa = $('#cd_empresa').val();
            const nr_lancamento = $('#nr_lancamento').val();
            const nr_nota = $('#nr_nota').val();
            const cd_vendedor_novo = $('#cd_vendedor_novo').val();

            if (!cd_vendedor_novo) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Vendedor não selecionado',
                    text: 'Por favor, selecione um vendedor novo para a nota.',
                });
                return;
            }

            $.ajax({
                type: "POST",
                url: window.routes.updateVendedorNota,
                data: {
                    cd_empresa,
                    nr_lancamento,
                    nr_nota,
                    cd_vendedor_novo,
                    _token: window.routes.token
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Alterando vendedor...',
                        text: 'Aguarde enquanto alteramos o vendedor da nota.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            text: response.message,
                        });
                        tableNotasVendedorDivergentes.ajax.reload();
                        $('#modal-alterar-vendedor').modal('hide');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao alterar vendedor',
                            text: response.message ||
                                'Ocorreu um erro ao tentar alterar o vendedor da nota.',
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro na requisição',
                        text: 'Ocorreu um erro ao enviar a requisição. Por favor, tente novamente.',
                    });
                }
            });
        });


        $('#table-notas-vendedores-divergentes')
            .on('preXhr.dt', function() {
                Swal.fire({
                    title: 'Carregando notas divergentes...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            })
            .on('xhr.dt', function() {
                Swal.close();
            });




        function updateVendedorNotaBadge(count) {
            var $badge = $('#vendedor-nota-count-badge');
            if (count > 0) {
                $badge.text(count + ' selecionada' + (count > 1 ? 's' : '')).show();
            } else {
                $badge.hide();
            }
        }

        function getVendedorNotaSelecionados() {
            var lancamentos = [];
            $(tableNotasVendedorDivergentes.table().container()).find('.dt-row-checkbox-vendedor').filter(function() {
                return this.checked;
            }).each(function() {
                lancamentos.push(String($(this).data('lancamento')));
            });
            return tableNotasVendedorDivergentes.rows().data().toArray().filter(function(row) {
                return lancamentos.includes(String(row.NR_LANCAMENTO));
            });
        }

        $(document).on('click', '.dt-select-all-vendedor', function(e) {
            e.stopPropagation();
            var checked = this.checked;
            if (!tableNotasVendedorDivergentes) return;
            var filteredRows = tableNotasVendedorDivergentes.rows({ search: 'applied' });
            filteredRows.nodes().to$().find('.dt-row-checkbox-vendedor').prop('checked', checked);
            var checkedCount = checked ? filteredRows.count() : 0;
            updateVendedorNotaBadge(checkedCount);
        });

        $(document).on('click', '.dt-row-checkbox-vendedor', function(e) {
            e.stopPropagation();
            if (!tableNotasVendedorDivergentes) return;
            var filteredCount = tableNotasVendedorDivergentes.rows({ search: 'applied' }).count();
            var checkedCount = $(tableNotasVendedorDivergentes.table().container()).find('.dt-row-checkbox-vendedor').filter(function() { return this.checked; }).length;
            $('.dt-select-all-vendedor').prop('checked', filteredCount > 0 && checkedCount === filteredCount);
            updateVendedorNotaBadge(checkedCount);
        });

        function initTableNotasVendedorDivergentes() {
            tableNotasVendedorDivergentes = $('#table-notas-vendedores-divergentes').DataTable({
                processing: false,
                serverSide: false,
                searching: true,
                destroy: true,
                autoWidth: false,
                pagingType: "simple",
                pageLength: 100,
                scrollY: "300px",
                scrollCollapse: true,
                language: {
                    url: window.routes.languageDatatables
                },
                ajax: window.routes.notaVendedorDivergentes,
                columns: [{
                        data: null,
                        width: "30px",
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        title: '<input type="checkbox" class="dt-select-all-vendedor" title="Selecionar todos" style="margin:0;">',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return '<input type="checkbox" class="dt-row-checkbox-vendedor" data-lancamento="' + row.NR_LANCAMENTO + '" aria-label="Selecionar linha" style="margin:0;">';
                            }
                            return '';
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        width: "50px",
                        title: 'Ações',
                        className: 'text-center',
                        orderable: false
                    },

                    {
                        data: 'DT_EMISSAO',
                        name: 'emissao',
                        title: 'Emissão',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return moment(data).format('DD/MM/YYYY');
                        }
                    },
                    {
                        data: 'CD_EMPRESA',
                        name: 'CD_EMPRESA',
                        title: 'Empresa',
                        className: 'text-center'
                    },
                    {
                        data: 'NR_LANCAMENTO',
                        name: 'NR_LANCAMENTO',
                        title: 'Lançamento',
                        className: 'text-center'
                    },
                    {
                        data: 'NR_NOTAFISCAL',
                        name: 'NR_NOTAFISCAL',
                        title: 'Nota',
                        className: 'text-center'
                    },
                    {
                        data: 'NM_PESSOA',
                        name: 'NM_PESSOA',
                        title: 'Cliente',
                        className: 'texto-curto'
                    },
                    {
                        data: 'CD_ITEM',
                        name: 'CD_ITEM',
                        title: 'Item',
                        className: 'texto-curto'
                    },
                    {
                        data: 'DS_ITEM',
                        name: 'DS_ITEM',
                        title: 'Item',
                        className: 'texto-curto'
                    },
                    {
                        data: 'NM_VEND_NOTA',
                        name: 'NM_VEND_NOTA',
                        title: 'Vendedor Nota',
                        className: 'texto-curto'
                    },
                    {
                        data: 'NM_VENDEDOR_INV',
                        name: 'NM_VENDEDOR_INV',
                        title: 'Vendedor Comissão',
                        className: 'texto-curto'

                    },
                ],
                "order": [2, 'desc'],
                drawCallback: function(settings) {
                    let grupoAtual = null;
                    let alternador = false;

                    const api = this.api();
                    const rows = api.rows({
                        page: 'current'
                    }).every(function() {
                        const row = $(this.node());
                        const data = this.data();

                        if (grupoAtual !== data.NR_LANCAMENTO) {
                            grupoAtual = data.NR_LANCAMENTO;
                            alternador = !alternador; // Alterna entre true e false para cada novo grupo
                        }

                        row.removeClass('grupo-a grupo-b'); // Remove as classes de grupo anteriores
                        row.addClass(alternador ? 'grupo-a' :
                            'grupo-b'); // Adiciona a classe de grupo com base no alternador
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao carregar dados',
                        text: 'Ocorreu um erro ao carregar os dados. Por favor, tente novamente.',
                    });
                }
            });

        }
    </script>
@stop
