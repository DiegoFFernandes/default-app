@extends('layouts.master')

@section('title', 'Permissões de Funções')

@section('content')
    <section class="content">
        <div class="mb-2">
            <a href="{{ route('usuario.permission-user') }}" class="btn btn-warning btn-sm ml-1">Permissão x Usuário</a>
            <a href="{{ route('usuario.role') }}" class="btn btn-info btn-sm ml-1">Funções x Usuário</a>
            <a href="{{ route('usuario.index') }}" class="btn btn-secondary btn-sm ml-1">Usuários</a>
        </div>
        <div class="row">

            {{-- LEFT: role list --}}
            <div class="col-md-3">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Funções</h3>
                        <div class="card-tools">
                            <button class="btn btn-success btn-sm" id="btn-create">
                                <i class="fas fa-plus"></i> Nova
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0" style="max-height:72vh;overflow-y:auto;">
                        <ul class="nav nav-pills flex-column" id="role-list">
                            <li class="nav-item">
                                <div class="text-center py-3 text-muted small" id="list-loading">
                                    <i class="fas fa-spinner fa-spin"></i> Carregando...
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- RIGHT: permission panel --}}
            <div class="col-md-9">

                {{-- Empty state --}}
                <div id="panel-empty" class="card">
                    <div class="card-body text-center py-5 text-muted">
                        <i class="fas fa-shield-alt fa-3x mb-3 d-block" style="color:#dee2e6;"></i>
                        <p class="mb-0">Selecione uma função para gerenciar suas permissões</p>
                    </div>
                </div>

                {{-- Edit / Create panel --}}
                <div id="panel-main" class="d-none">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <div style="flex:1;min-width:0;" class="pr-2">
                                <h5 id="panel-title" class="mb-0 d-none"></h5>
                                <div id="panel-select-wrap" class="d-none">
                                    <input type="text" id="entity-input" class="form-control form-control-sm"
                                        placeholder="Nome da nova função..." autocomplete="off" style="width:100%;">
                                </div>
                            </div>
                            <div class="d-flex flex-shrink-0">
                                <button id="btn-save" class="btn btn-sm btn-success d-none mr-1">
                                    <i class="fas fa-save"></i> Salvar
                                </button>
                                <button id="btn-update" class="btn btn-sm btn-primary d-none mr-1">
                                    <i class="fas fa-save"></i> Atualizar
                                </button>
                                <button id="btn-delete" class="btn btn-sm btn-danger d-none">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" id="permissions-groups"></div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@stop

@section('js')
    <script>
        var allPermissions = [];
        var currentId = null;

        const groupMeta = {
            'ver':         { label: 'Visualização',  icon: 'fa-eye',           color: 'info'      },
            'editar':      { label: 'Edição',         icon: 'fa-edit',          color: 'warning'   },
            'config':      { label: 'Configuração',   icon: 'fa-cog',           color: 'secondary' },
            'solicitacao': { label: 'Solicitações',   icon: 'fa-clipboard-list',color: 'primary'   },
            'adicionar':   { label: 'Adição',         icon: 'fa-plus-circle',   color: 'success'   },
        };

        $.get("{{ route('usuario.permission-role.get-permissions') }}", function(data) {
            allPermissions = data;
            loadList();
        }).fail(function() { erroCarregamento(); });

        function loadList() {
            $.get("{{ route('usuario.permission-role.list') }}", function(response) {
                var items = response.data || [];
                var html = '';
                items.forEach(function(role) {
                    html += '<a href="#" class="nav-link list-item d-flex justify-content-between align-items-center px-3 py-2 border-bottom"'
                        + ' data-id="' + role.id + '"'
                        + ' data-name="' + escHtml(role.name) + '"'
                        + ' data-perms=\'' + JSON.stringify(role.permission_names) + '\'>'
                        + '<span>' + role.name + '</span>'
                        + '<span class="badge badge-info badge-pill">' + role.permission_names.length + '</span>'
                        + '</a>';
                });
                if (!html) html = '<p class="text-center text-muted py-3 small mb-0">Nenhuma função com permissões</p>';
                $('#role-list').html(html);

                if (currentId) {
                    var $el = $('#role-list .list-item[data-id="' + currentId + '"]');
                    if ($el.length) {
                        $el.addClass('active');
                        renderGroups(JSON.parse($el.attr('data-perms') || '[]'));
                    }
                }
            }).fail(function() { erroCarregamento(); });
        }

        $('#role-list').on('click', '.list-item', function(e) {
            e.preventDefault();
            currentId = $(this).data('id');
            var perms = $(this).data('perms');
            if (typeof perms === 'string') perms = JSON.parse(perms);

            $('#role-list .list-item').removeClass('active');
            $(this).addClass('active');

            openPanel('edit', $(this).data('name'), perms);
        });

        $('#btn-create').click(function() {
            currentId = null;
            $('#role-list .list-item').removeClass('active');
            $('#entity-input').val('');
            openPanel('create', '', []);
        });

        function openPanel(mode, title, perms) {
            $('#panel-empty').addClass('d-none');
            $('#panel-main').removeClass('d-none');

            if (mode === 'edit') {
                $('#panel-title').text(title).removeClass('d-none');
                $('#panel-select-wrap').addClass('d-none');
                $('#btn-save').addClass('d-none');
                $('#btn-update').removeClass('d-none');
                $('#btn-delete').removeClass('d-none');
            } else {
                $('#panel-title').addClass('d-none');
                $('#panel-select-wrap').removeClass('d-none');
                $('#btn-update').addClass('d-none');
                $('#btn-delete').addClass('d-none');
                $('#btn-save').removeClass('d-none');
            }
            renderGroups(perms);
        }

        function renderGroups(checkedNames) {
            var groups = {};
            allPermissions.forEach(function(perm) {
                var key = perm.name.split('-')[0];
                if (!groups[key]) groups[key] = [];
                groups[key].push(perm);
            });

            var html = '<div class="row">';
            Object.keys(groups).sort().forEach(function(key) {
                var meta = groupMeta[key] || { label: key, icon: 'fa-key', color: 'dark' };
                var n = groups[key].filter(function(p) { return checkedNames.indexOf(p.name) !== -1; }).length;

                html += '<div class="col-md-6 mb-3">'
                    + '<div class="card card-outline card-' + meta.color + ' mb-0">'
                    + '<div class="card-header py-1 px-2 d-flex justify-content-between align-items-center">'
                    + '<span class="card-title mb-0 small font-weight-bold"><i class="fas ' + meta.icon + ' mr-1"></i>' + meta.label + '</span>'
                    + '<span class="small text-muted"><span class="gc-' + key + '">' + n + '</span>/' + groups[key].length + '</span>'
                    + '</div><div class="card-body py-2 px-2" style="max-height:300px;overflow-y:auto;">';

                groups[key].forEach(function(perm) {
                    var safeId = 'p_' + perm.name.replace(/[^a-z0-9]/gi, '_');
                    var checked = checkedNames.indexOf(perm.name) !== -1 ? 'checked' : '';
                    html += '<div class="custom-control custom-checkbox mb-1">'
                        + '<input type="checkbox" class="custom-control-input perm-checkbox" id="' + safeId + '" value="' + perm.name + '" ' + checked + ' data-group="' + key + '">'
                        + '<label class="custom-control-label small" for="' + safeId + '">' + perm.name + '</label>'
                        + '</div>';
                });

                html += '</div></div></div>';
            });
            html += '</div>';
            $('#permissions-groups').html(html);
        }

        $('#permissions-groups').on('change', '.perm-checkbox', function() {
            var key = $(this).data('group');
            var n = $('#permissions-groups .perm-checkbox[data-group="' + key + '"]:checked').length;
            $('.gc-' + key).text(n);
        });

        function getChecked() {
            return $('#permissions-groups .perm-checkbox:checked').map(function() { return $(this).val(); }).get();
        }

        $('#btn-save').click(function() {
            var roleName = $('#entity-input').val().trim();
            var perms = getChecked();
            if (!roleName) { Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Digite o nome da função.' }); return; }
            if (!perms.length) { Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Selecione pelo menos uma permissão.' }); return; }
            $.ajax({
                url: "{{ route('usuario.permission-role.create') }}",
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', role_name: roleName, permissions: perms },
                success: function(r) { loadList(); Swal.fire({ icon: 'success', title: 'Sucesso', text: r.message, timer: 2000, showConfirmButton: false }); },
                error: function(xhr) { Swal.fire({ icon: 'error', title: 'Erro', text: (xhr.responseJSON || {}).message || 'Erro ao salvar.' }); }
            });
        });

        $('#btn-update').click(function() {
            var perms = getChecked();
            if (!perms.length) { Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Selecione pelo menos uma permissão.' }); return; }
            $.ajax({
                url: "{{ route('usuario.permission-role.update') }}",
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', role_id: currentId, permissions: perms },
                success: function(r) { loadList(); Swal.fire({ icon: 'success', title: 'Sucesso', text: r.message, timer: 2000, showConfirmButton: false }); },
                error: function(xhr) { Swal.fire({ icon: 'error', title: 'Erro', text: (xhr.responseJSON || {}).message || 'Erro ao atualizar.' }); }
            });
        });

        $('#btn-delete').click(function() {
            var name = $('#panel-title').text();
            Swal.fire({
                title: 'Confirmar remoção',
                html: 'Remover todas as permissões de <strong>' + name + '</strong>?',
                icon: 'warning', showCancelButton: true,
                confirmButtonText: 'Sim, remover', cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
            }).then(function(result) {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: "{{ route('usuario.permission-role.remove') }}",
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}', role_id: currentId },
                    success: function(r) {
                        currentId = null;
                        $('#panel-main').addClass('d-none');
                        $('#panel-empty').removeClass('d-none');
                        loadList();
                        Swal.fire({ icon: 'success', title: 'Sucesso', text: r.message, timer: 2000, showConfirmButton: false });
                    },
                    error: function(xhr) { Swal.fire({ icon: 'error', title: 'Erro', text: (xhr.responseJSON || {}).message || 'Erro ao remover.' }); }
                });
            });
        });

        $('#entity-select').select2({ theme: 'bootstrap4', placeholder: 'Selecione a função', width: '100%' });

        function escHtml(s) {
            return String(s).replace(/[&<>"']/g, function(c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }
    </script>
@stop
