@extends('layouts.master')

@section('title', $title_page)

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline card-outline-tabs">

                    {{-- Nav Tabs --}}
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabs-itens" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-itens" data-toggle="pill" href="#pane-itens-lista" role="tab">
                                    <i class="fas fa-boxes mr-1"></i> Itens
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-subgrupos" data-toggle="pill" href="#pane-subgrupos" role="tab">
                                    <i class="fas fa-layer-group mr-1"></i> Subgrupos
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">

                            {{-- Tab: Itens --}}
                            <div class="tab-pane fade show active" id="pane-itens-lista" role="tabpanel">
                                <div class="d-flex justify-content-end mb-2">
                                    <button id="btn-novo-compra-item" class="btn btn-primary btn-xs">
                                        <i class="fas fa-plus"></i> Novo Item
                                    </button>
                                </div>
                                <table class="table compact table-striped table-bordered table-hover table-font-small" id="table-compra-itens" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th style="width:80px">Cód.</th>
                                            <th>Descrição</th>
                                            <th>Subgrupo</th>
                                            <th style="width:90px">Unidade</th>
                                            <th style="width:90px">Status</th>
                                            <th style="width:100px">Ações</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            {{-- Tab: Subgrupos --}}
                            <div class="tab-pane fade" id="pane-subgrupos" role="tabpanel">
                                <div class="d-flex justify-content-end mb-2">
                                    <button id="btn-novo-subgrupo" class="btn btn-primary btn-xs">
                                        <i class="fas fa-plus"></i> Novo Subgrupo
                                    </button>
                                </div>
                                <table class="table compact table-striped table-bordered table-hover table-font-small" id="table-subgrupos" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th style="width:100px">Cód.</th>
                                            <th>Descrição</th>
                                            <th style="width:100px">Ações</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('admin.compras.itens.modal-item')
    @include('admin.compras.itens.modal-subgrupo')
@stop

@section('js')
    <script>
        $(document).ready(function () {
            const token = $('[name=csrf-token]').attr('content');

            // ---------------- Itens ----------------
            const dt = $('#table-compra-itens').DataTable({
                processing: false,
                serverSide: false,
                ajax: '{{ route('compras.itens-proprios.list') }}',
                columns: [
                    { data: 'CD_ITEM',      name: 'CD_ITEM' },
                    { data: 'DS_ITEM',      name: 'DS_ITEM' },
                    { data: 'DS_SUBGRUPO',  name: 'DS_SUBGRUPO', defaultContent: '—' },
                    { data: 'SG_UNIDMED',   name: 'SG_UNIDMED', defaultContent: '—' },
                    { data: 'ativo_badge',  name: 'ativo_badge', orderable: false, searchable: false },
                    { data: 'Actions',      name: 'Actions', orderable: false, searchable: false },
                ],
                order: [[1, 'asc']],
                pageLength: 25,
                language: { url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json' },
            });

            $('#btn-novo-compra-item').on('click', function () {
                window.abrirModalNovoCompraItem();
            });

            $('body').on('click', '.btn-edit-compra-item', function () {
                const btn = $(this);
                $('#modal-compra-item-title').text('Editar Item');
                $('#ci_cd').val(btn.data('cd'));
                $('#ci_ds_item').val(btn.data('ds'));
                $('#ci_sg_unidmed').val(btn.data('un'));
                $('#ci_st_ativo').val(btn.data('ativo'));
                window.setSubgrupoCompraItem(btn.data('subgrupo-cd'));
                $('#modal-compra-item').modal('show');
            });

            $('body').on('click', '.btn-delete-compra-item', function () {
                const cd = $(this).data('cd');
                Swal.fire({
                    title: 'Remover item?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Remover',
                    cancelButtonText: 'Cancelar',
                }).then(r => {
                    if (!r.isConfirmed) return;
                    $.ajax({
                        url: '/compras/itens-proprios/' + cd,
                        method: 'DELETE',
                        data: { _token: token },
                        success: res => {
                            if (res.errors) Swal.fire('Erro', res.errors, 'error');
                            else {
                                Swal.fire({ icon: 'success', title: 'Removido!', text: res.success, toast: true,
                                    position: 'top-end', showConfirmButton: false, timer: 2000 });
                                dt.ajax.reload();
                            }
                        }
                    });
                });
            });

            document.addEventListener('compra-item:saved', function () {
                dt.ajax.reload();
            });

            // ---------------- Subgrupos ----------------
            const dtSub = $('#table-subgrupos').DataTable({
                processing: false,
                serverSide: false,
                ajax: '{{ route('compras.subgrupos.list') }}',
                columns: [
                    { data: 'CD_SUBGRUPO', name: 'CD_SUBGRUPO' },
                    { data: 'DS_SUBGRUPO', name: 'DS_SUBGRUPO' },
                    { data: 'Actions',     name: 'Actions', orderable: false, searchable: false },
                ],
                order: [[1, 'asc']],
                pageLength: 25,
                language: { url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json' },
            });

            $('#btn-novo-subgrupo').on('click', function () {
                window.abrirModalNovoSubgrupo();
            });

            $('body').on('click', '.btn-edit-subgrupo', function () {
                const btn = $(this);
                $('#modal-subgrupo-title').text('Editar Subgrupo');
                $('#sg_cd').val(btn.data('cd'));
                $('#sg_ds_subgrupo').val(btn.data('ds'));
                $('#modal-subgrupo').modal('show');
            });

            // Após salvar no modal, recarrega a tabela
            document.addEventListener('compra-subgrupo:saved', function () {
                dtSub.ajax.reload();
            });

            $('body').on('click', '.btn-delete-subgrupo', function () {
                const cd = $(this).data('cd');
                Swal.fire({
                    title: 'Remover subgrupo?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Remover',
                    cancelButtonText: 'Cancelar',
                }).then(r => {
                    if (!r.isConfirmed) return;
                    $.ajax({
                        url: '/compras/subgrupos/' + cd,
                        method: 'DELETE',
                        data: { _token: token },
                        success: res => {
                            if (res.errors) Swal.fire('Erro', res.errors, 'error');
                            else {
                                Swal.fire({ icon: 'success', title: 'Removido!', text: res.success, toast: true,
                                    position: 'top-end', showConfirmButton: false, timer: 2000 });
                                dtSub.ajax.reload();
                            }
                        }
                    });
                });
            });
        });
    </script>

    @include('admin.compras.itens.modal-item-script')
    @include('admin.compras.itens.modal-subgrupo-script')
@stop
