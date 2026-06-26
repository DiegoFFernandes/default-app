@extends('layouts.master')

@section('title', 'Funções x Usuário')

@section('css')
    <style>
        #user-list .list-item.active {
            background-color: #17a2b8;
            color: #fff;
        }
        #user-list .list-item.active .text-muted {
            color: #fff !important;
        }
        #user-list .list-item.active .badge {
            background-color: #fff;
            color: #17a2b8;
        }
    </style>
@stop

@section('content')
    <section class="content">
        <div class="mb-2">
            <a href="{{ route('usuario.permission-user') }}" class="btn btn-warning btn-sm ml-1">Permissão x Usuário</a>
            <a href="{{ route('usuario.permission-role') }}" class="btn btn-danger btn-sm ml-1">Funções</a>
            <a href="{{ route('usuario.index') }}" class="btn btn-secondary btn-sm ml-1">Usuários</a>
        </div>
        <div class="row">

            {{-- LEFT: user list --}}
            <div class="col-md-3">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">Usuários</h3>
                        <div class="card-tools">
                            <button class="btn btn-success btn-sm" id="btn-create">
                                <i class="fas fa-plus"></i> Nova
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0" style="max-height:72vh;overflow-y:auto;">
                        <ul class="nav nav-pills flex-column" id="user-list">
                            <li class="nav-item">
                                <div class="text-center py-3 text-muted small" id="list-loading">
                                    <i class="fas fa-spinner fa-spin"></i> Carregando...
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- RIGHT: roles panel --}}
            <div class="col-md-9">

                {{-- Empty state --}}
                <div id="panel-empty" class="card">
                    <div class="card-body text-center py-5 text-muted">
                        <i class="fas fa-users-cog fa-3x mb-3 d-block" style="color:#dee2e6;"></i>
                        <p class="mb-0">Selecione um usuário para gerenciar suas funções</p>
                    </div>
                </div>

                {{-- Edit / Create panel --}}
                <div id="panel-main" class="d-none">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <div style="flex:1;min-width:0;" class="pr-2">
                                <div id="panel-info" class="d-none">
                                    <h5 id="panel-title" class="mb-0"></h5>
                                    <small id="panel-email" class="text-muted"></small>
                                </div>
                                <div id="panel-select-wrap" class="d-none">
                                    <select id="entity-select" class="form-control form-control-sm" style="width:100%;"></select>
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
                        <div class="card-body" id="roles-groups" style="max-height:300px;overflow-y:auto;"></div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@stop

@section('js')
    <script>
        var allRoles = [];
        var currentId = null;

        $.get("{{ route('usuario.role.get-roles') }}", function(data) {
            allRoles = data;
            loadList();
        }).fail(function() { erroCarregamento(); });

        function loadList() {
            $.get("{{ route('usuario.list_role') }}", function(response) {
                var items = response.data || [];
                var html = '';
                items.forEach(function(user) {
                    html += '<a href="#" class="nav-link list-item d-flex justify-content-between align-items-center px-3 py-2 border-bottom"'
                        + ' data-id="' + user.id + '"'
                        + ' data-name="' + escHtml(user.name) + '"'
                        + ' data-email="' + escHtml(user.email) + '"'
                        + ' data-roles=\'' + JSON.stringify(user.role_names) + '\'>'
                        + '<div style="min-width:0;"><div class="text-truncate">' + escHtml(user.name) + '</div>'
                        + '<small class="text-muted text-truncate d-block">' + escHtml(user.email) + '</small></div>'
                        + '<span class="badge badge-info badge-pill ml-2">' + user.role_names.length + '</span>'
                        + '</a>';
                });
                if (!html) html = '<p class="text-center text-muted py-3 small mb-0">Nenhum usuário com funções atribuídas</p>';
                $('#user-list').html(html);

                if (currentId) {
                    var $el = $('#user-list .list-item[data-id="' + currentId + '"]');
                    if ($el.length) {
                        $el.addClass('active');
                        renderRoles(JSON.parse($el.attr('data-roles') || '[]'));
                    }
                }
            }).fail(function() { erroCarregamento(); });
        }

        $('#user-list').on('click', '.list-item', function(e) {
            e.preventDefault();
            currentId = $(this).data('id');
            var roles = $(this).data('roles');
            if (typeof roles === 'string') roles = JSON.parse(roles);

            $('#user-list .list-item').removeClass('active');
            $(this).addClass('active');

            openPanel('edit', $(this).data('name'), $(this).data('email'), roles);
        });

        $('#btn-create').click(function() {
            currentId = null;
            $('#user-list .list-item').removeClass('active');
            openPanel('create', '', '', []);

            $('#entity-select').empty().append('<option value=""></option>');
            $.get("{{ route('usuario.role.get-users') }}", function(data) {
                data.forEach(function(u) {
                    $('#entity-select').append(new Option(u.name + ' — ' + u.email, u.id));
                });
                $('#entity-select').trigger('change');
            });
        });

        function openPanel(mode, name, email, roles) {
            $('#panel-empty').addClass('d-none');
            $('#panel-main').removeClass('d-none');

            if (mode === 'edit') {
                $('#panel-info').removeClass('d-none');
                $('#panel-title').text(name);
                $('#panel-email').text(email);
                $('#panel-select-wrap').addClass('d-none');
                $('#btn-save').addClass('d-none');
                $('#btn-update').removeClass('d-none');
                $('#btn-delete').removeClass('d-none');
            } else {
                $('#panel-info').addClass('d-none');
                $('#panel-select-wrap').removeClass('d-none');
                $('#btn-update').addClass('d-none');
                $('#btn-delete').addClass('d-none');
                $('#btn-save').removeClass('d-none');
            }
            renderRoles(roles);
        }

        function renderRoles(checkedNames) {
            var html = '<div class="row">';
            allRoles.forEach(function(role) {
                var safeId = 'r_' + role.name.replace(/[^a-z0-9]/gi, '_');
                var checked = checkedNames.indexOf(role.name) !== -1 ? 'checked' : '';
                html += '<div class="col-md-4 col-sm-6 mb-2">'
                    + '<div class="custom-control custom-checkbox">'
                    + '<input type="checkbox" class="custom-control-input role-checkbox" id="' + safeId + '" value="' + role.name + '" ' + checked + '>'
                    + '<label class="custom-control-label" for="' + safeId + '">' + role.name + '</label>'
                    + '</div></div>';
            });
            html += '</div>';
            $('#roles-groups').html(html);
        }

        function getChecked() {
            return $('#roles-groups .role-checkbox:checked').map(function() { return $(this).val(); }).get();
        }

        $('#btn-save').click(function() {
            var userId = $('#entity-select').val();
            var roles = getChecked();
            if (!userId) { Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Selecione um usuário.' }); return; }
            if (!roles.length) { Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Selecione pelo menos uma função.' }); return; }
            $.ajax({
                url: "{{ route('usuario.role.assign') }}",
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', user_id: userId, roles: roles },
                success: function(r) { loadList(); Swal.fire({ icon: 'success', title: 'Sucesso', text: r.message, timer: 2000, showConfirmButton: false }); },
                error: function(xhr) { Swal.fire({ icon: 'error', title: 'Erro', text: (xhr.responseJSON || {}).message || 'Erro ao salvar.' }); }
            });
        });

        $('#btn-update').click(function() {
            var roles = getChecked();
            if (!roles.length) { Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Selecione pelo menos uma função.' }); return; }
            $.ajax({
                url: "{{ route('usuario.role.update') }}",
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', user_id: currentId, roles: roles },
                success: function(r) { loadList(); Swal.fire({ icon: 'success', title: 'Sucesso', text: r.message, timer: 2000, showConfirmButton: false }); },
                error: function(xhr) { Swal.fire({ icon: 'error', title: 'Erro', text: (xhr.responseJSON || {}).message || 'Erro ao atualizar.' }); }
            });
        });

        $('#btn-delete').click(function() {
            var name = $('#panel-title').text();
            Swal.fire({
                title: 'Confirmar exclusão',
                html: 'Deseja remover todas as funções e excluir o usuário <strong>' + name + '</strong>?',
                icon: 'warning', showCancelButton: true,
                confirmButtonText: 'Sim, excluir', cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
            }).then(function(result) {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: "{{ route('usuario.role.remove') }}",
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}', user_id: currentId },
                    success: function(r) {
                        currentId = null;
                        $('#panel-main').addClass('d-none');
                        $('#panel-empty').removeClass('d-none');
                        loadList();
                        Swal.fire({ icon: 'success', title: 'Sucesso', text: r.message, timer: 2000, showConfirmButton: false });
                    },
                    error: function(xhr) { Swal.fire({ icon: 'error', title: 'Erro', text: (xhr.responseJSON || {}).message || 'Erro ao excluir.' }); }
                });
            });
        });

        $('#entity-select').select2({ theme: 'bootstrap4', placeholder: 'Selecione o usuário', width: '100%' });

        function escHtml(s) {
            return String(s).replace(/[&<>"']/g, function(c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }
    </script>
@stop
