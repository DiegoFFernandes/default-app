$(document).ready(function () {
    initTabEvents();
});

// ─── Eventos da Aba ───────────────────────────────────────────────────────────
let enviosDisparoCarregado = false;

function initTabEvents() {
    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        const href = $(e.target).attr('href');
        $('#btn-config-disparo-automatico').toggleClass('d-none', href !== '#pane-disparos-automaticos');

        if (href === '#pane-disparos-automaticos' && !enviosDisparoCarregado) {
            enviosDisparoCarregado = true;
            initFiltroContexto();
            initPeriodo();
            initDataTableEnvios();
        }
    });
}

// ─── Modal: Contextos de Disparo ──────────────────────────────────────────────
$('#modal-contextos-disparo').on('show.bs.modal', function () {
    carregarContextos();
});

function carregarContextos() {
    $.get(window.routesFollowUp.disparoListContextos)
        .done(function (contextos) {
            renderContextos(contextos);
        })
        .fail(function () {
            $('#tbody-contextos-disparo').html(
                '<tr><td colspan="6" class="text-center text-danger py-3">Erro ao carregar contextos.</td></tr>'
            );
        });
}

function renderContextos(contextos) {
    if (!contextos || !contextos.length) {
        $('#tbody-contextos-disparo').html(
            '<tr><td colspan="6" class="text-center text-muted py-3">Nenhum contexto cadastrado.</td></tr>'
        );
        return;
    }

    const rows = contextos.map(function (c) {
        const ativo = c.ST_ATIVO === 'S';
        const badge = ativo
            ? '<span class="badge badge-success">Ativo</span>'
            : '<span class="badge badge-secondary">Inativo</span>';
        const btnToggle = ativo
            ? `<button class="btn btn-xs btn-danger btn-toggle-contexto-disparo" data-id="${c.CD_CONTEXTO}" title="Desativar"><i class="fas fa-pause"></i></button>`
            : `<button class="btn btn-xs btn-success btn-toggle-contexto-disparo" data-id="${c.CD_CONTEXTO}" title="Ativar"><i class="fas fa-play"></i></button>`;
        const btnEditar = `<button class="btn btn-xs btn-secondary btn-editar-horario-disparo mr-1"
                data-id="${c.CD_CONTEXTO}" data-ds="${c.DS_CONTEXTO}" data-horario="${(c.HR_EXECUCAO || '').substring(0, 5)}"
                title="Editar horário"><i class="fas fa-clock"></i></button>`;

        return `<tr data-id="${c.CD_CONTEXTO}">
            <td>${c.DS_CONTEXTO}</td>
            <td class="text-center">${(c.HR_EXECUCAO || '').substring(0, 5)}</td>
            <td class="text-center">${c.NR_TENTATIVAS}</td>
            <td>${c.DT_ULTIMAEXECUCAO || '<span class="text-muted">—</span>'}</td>
            <td class="text-center td-status-contexto">${badge}</td>
            <td class="text-center td-acao-contexto">${btnEditar}${btnToggle}</td>
        </tr>`;
    });

    $('#tbody-contextos-disparo').html(rows.join(''));
}

$(document).on('click', '.btn-toggle-contexto-disparo', function () {
    const id = $(this).data('id');
    const btn = $(this);
    btn.prop('disabled', true);

    $.post(window.routesFollowUp.disparoToggleContexto.replace(':id', id), {
        _token: $('[name=csrf-token]').attr('content'),
    }).done(function (res) {
        Swal.fire({
            icon: 'success', title: res.success, toast: true, position: 'top-end',
            showConfirmButton: false, timer: 2000, timerProgressBar: true,
        });
        carregarContextos();
    }).fail(function () {
        Swal.fire('Erro', 'Não foi possível atualizar o status do contexto.', 'error');
        btn.prop('disabled', false);
    });
});

$(document).on('click', '.btn-editar-horario-disparo', function () {
    $('#modal-contextos-disparo').modal('hide');
    $('#horario_cd_contexto').val($(this).data('id'));
    $('#horario_ds_contexto').text($(this).data('ds'));
    $('#horario_hr_execucao').val($(this).data('horario'));
    $('#modal-horario-contexto').modal('show');
});

$('#modal-horario-contexto').on('hidden.bs.modal', function () {
    $('#modal-contextos-disparo').modal('show');
});

$('#btn-salvar-horario-contexto').on('click', function () {
    const id = $('#horario_cd_contexto').val();
    const horario = $('#horario_hr_execucao').val();

    $.post(window.routesFollowUp.disparoHorarioContexto.replace(':id', id), {
        _token: $('[name=csrf-token]').attr('content'),
        hr_execucao: horario,
    }).done(function (res) {
        $('#modal-horario-contexto').modal('hide');
        Swal.fire({
            icon: 'success', title: res.success, toast: true, position: 'top-end',
            showConfirmButton: false, timer: 2000, timerProgressBar: true,
        });
        carregarContextos();
    }).fail(function (xhr) {
        const msg = xhr.responseJSON?.errors ?? xhr.responseJSON?.message ?? 'Falha ao salvar horário.';
        Swal.fire('Erro', msg, 'error');
    });
});

// ─── Filtro de Contexto (busca de envios) ─────────────────────────────────────
function initFiltroContexto() {
    $.get(window.routesFollowUp.disparoListContextos).done(function (contextos) {
        const select = $('#disparo_nr_contexto');
        (contextos || []).forEach(function (c) {
            select.append(new Option(c.DS_CONTEXTO, c.CD_CONTEXTO));
        });
        select.select2({ theme: 'bootstrap4' });
    });
}

// ─── Período (padrão: hoje) ────────────────────────────────────────────────────
let datasSelecionadasDisparo;

function initPeriodo() {
    const hoje = moment().format('DD.MM.YYYY');
    datasSelecionadasDisparo = initDateRangePicker('#disparo-daterange', hoje, hoje);
    $('.badge-date-disparo').text('Período: ' + hoje);
}

// ─── DataTable de Envios ──────────────────────────────────────────────────────
let tabelaDisparos;

function initDataTableEnvios() {
    tabelaDisparos = $('#table-search-disparo').DataTable({
        paging: false,
        scrollY: '50vh',
        scrollCollapse: true,
        language: { url: window.routesFollowUp.languageDatatables },
        ajax: {
            url: window.routesFollowUp.disparoListEnvios,
            method: 'GET',
            data: function (d) {
                d.inicio_data = datasSelecionadasDisparo.getInicio();
                d.fim_data = datasSelecionadasDisparo.getFim();
                d.cd_contexto = $('#disparo_nr_contexto').val();
                d.st_envio = $('#disparo_st_envio').val();
                d.nm_pessoa = $('#disparo_nm_pessoa').val();
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Carregando...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
            },
            complete: function () {
                Swal.close();
            },
            dataSrc: function (json) {
                if (json.aviso) {
                    // Adiado para o próximo tick: o complete() acima fecha o Swal de
                    // loading logo depois que este dataSrc retorna - abrir o aviso
                    // aqui na hora faria o complete() fechar ele também.
                    const aviso = json.aviso;
                    setTimeout(function () {
                        Swal.fire({
                            icon: 'info',
                            title: 'Período fora do disparo automático',
                            text: aviso,
                        });
                    }, 0);
                }
                return json.data;
            },
        },
        columns: [
            { title: 'Emp.', data: 'CD_EMPRESA' },
            { title: 'Contexto', data: 'DS_CONTEXTO', width: '18%', visible: false},
            { title: 'Cliente', data: 'NM_PESSOA', width: '20%' },
            { title: 'Nota', data: 'NR_LANCAMENTO', width: '8%', className: 'text-center' },
            { title: 'E-mail', data: 'DS_EMAILDEST', width: '18%' },
            {
                title: 'Cópia', data: 'DS_EMAILCOPIA', width: '6%', className: 'text-center', orderable: false,
                render: function (data) {
                    if (!data) {
                        return '<span class="text-muted">—</span>';
                    }
                    const emails = data.split(';').map((e) => e.trim()).filter(Boolean);
                    if (!emails.length) {
                        return '<span class="text-muted">—</span>';
                    }
                    return '<span class="badge badge-info btn-emailcopia-disparo" style="cursor:pointer" '
                        + 'data-emails="' + emails.join(', ') + '" title="Ver e-mails em cópia">'
                        + '<i class="fas fa-users"></i> ' + emails.length + '</span>';
                },
            },
            { title: 'Tentativas', data: 'NR_TENTATIVAS', width: '8%', className: 'text-center' },
            {
                title: 'Emissão', data: 'DT_EMISSAO', width: '10%', className: 'text-center',
                render: function (data) {
                    return data ? moment(data).format('DD/MM/YYYY') : '';
                },
            },
            {
                title: 'Registro', data: 'DT_REGISTRO', width: '12%', className: 'text-center',
                render: function (data) {
                    return data ? moment(data).format('DD/MM/YYYY HH:mm') : '';
                },
            },
            {
                title: 'Envio', data: 'DT_ENVIO', width: '12%', className: 'text-center',
                render: function (data) {
                    return data ? moment(data).format('DD/MM/YYYY HH:mm') : '';
                },
            },
            { title: 'Status', data: 'status_badge', width: '8%', className: 'text-center', orderable: false },
            { title: '#', data: 'action', width: '14%', className: 'text-center text-nowrap', orderable: false },
        ],
        order: [[7, 'desc']],
    });
}

$('#submit-search-disparo').on('click', function () {
    const inicio = datasSelecionadasDisparo.getInicio();
    const fim = datasSelecionadasDisparo.getFim();
    $('.badge-date-disparo').text(inicio === fim ? 'Período: ' + inicio : 'Período: ' + inicio + ' a ' + fim);
    tabelaDisparos.ajax.reload();
});

$(document).on('click', '.btn-motivo-falha-disparo', function () {
    Swal.fire({
        title: 'Motivo da Falha',
        text: $(this).data('motivo'),
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Fechar',
    });
});

$(document).on('click', '.btn-emailcopia-disparo', function () {
    Swal.fire({
        title: 'E-mails em Cópia (CC)',
        text: $(this).data('emails'),
        icon: 'info',
        confirmButtonText: 'Fechar',
    });
});

// ─── Reenviar ──────────────────────────────────────────────────────────────
$(document).on('click', '.btn-reenviar-disparo', function () {
    const id = $(this).data('id');

    Swal.fire({
        title: 'Reenviar este envio?',
        text: 'O status volta para "Aguardando" e ele entra na próxima execução do disparo automático.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Reenviar',
        cancelButtonText: 'Cancelar',
    }).then(function (result) {
        if (!result.isConfirmed) {
            return;
        }

        $.post(window.routesFollowUp.disparoReenviarEnvio.replace(':id', id), {
            _token: $('[name=csrf-token]').attr('content'),
        }).done(function (res) {
            Swal.fire({
                icon: 'success', title: res.success, toast: true, position: 'top-end',
                showConfirmButton: false, timer: 2000, timerProgressBar: true,
            });
            tabelaDisparos.ajax.reload(null, false);
        }).fail(function () {
            Swal.fire('Erro', 'Não foi possível marcar o envio para reenvio.', 'error');
        });
    });
});

// ─── Editar E-mail do Destinatário ─────────────────────────────────────────
$(document).on('click', '.btn-editar-email-disparo', function () {
    $('#email_disparo_cd_envio').val($(this).data('id'));
    $('#email_disparo_ds_emaildest').val($(this).data('email'));
    $('#modal-editar-email-disparo').modal('show');
});

$('#btn-salvar-email-disparo').on('click', function () {
    const id = $('#email_disparo_cd_envio').val();
    const email = $('#email_disparo_ds_emaildest').val();

    $.post(window.routesFollowUp.disparoAtualizarEmailEnvio.replace(':id', id), {
        _token: $('[name=csrf-token]').attr('content'),
        ds_emaildest: email,
    }).done(function (res) {
        $('#modal-editar-email-disparo').modal('hide');
        Swal.fire({
            icon: 'success', title: res.success, toast: true, position: 'top-end',
            showConfirmButton: false, timer: 2000, timerProgressBar: true,
        });
        tabelaDisparos.ajax.reload(null, false);
    }).fail(function (xhr) {
        const msg = xhr.responseJSON?.errors?.ds_emaildest?.[0] ?? xhr.responseJSON?.message ?? 'Falha ao salvar e-mail.';
        Swal.fire('Erro', msg, 'error');
    });
});
