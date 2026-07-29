$(document).ready(function() {

    var dtInicio = moment().subtract(30, 'days').format('DD.MM.YYYY');
    var dtFim = moment().subtract(1, 'days').format('DD.MM.YYYY');
    var datasSelecionadas = initDateRangePicker('#daterange', dtInicio, dtFim);

    $('.badge-date-follow').text('Período: ' + dtInicio + ' a ' + dtFim);

    $('#nr_contexto').select2({
        theme: 'bootstrap4'
    });

    $('#submit-seach').click(function() {

        let cd_number = $("#search-number").val();
        let cd_pessoa = $("#cd_pessoa").val();
        let nm_pessoa = $("#nm_pessoa").val();
        let cpf_cnpj = $("#cpf_cnpj").val();
        let nr_contexto = $("#nr_contexto").val();
        let ds_email_pessoa = $("#ds_email_pessoa").val();
        let inicio_data = datasSelecionadas.getInicio();
        let fim_data = datasSelecionadas.getFim();

        $('.badge-date-follow').text('Período: ' + inicio_data + ' a ' + fim_data);

        $("#table-search").DataTable().destroy();

        $("#table-search").DataTable({
            pagingType: "simple",
            pageLength: 10,
            language: {
                url: window.routesFollowUp.languageDatatables,
            },
            ajax: {
                url: window.routesFollowUp.getSearchEnvio,
                method: "GET",
                data: {
                    cd_number: cd_number,
                    cd_pessoa: cd_pessoa,
                    nm_pessoa: nm_pessoa,
                    cpf_cnpj: cpf_cnpj,
                    ds_email: ds_email_pessoa,
                    nr_contexto: nr_contexto,
                    inicio_data: inicio_data,
                    fim_data: fim_data,
                },
            },
            columns: [{
                    title: 'Descrição',
                    data: 'DS_CONTEXTO',
                    width: '20%',
                },
                {
                    title: 'Agenda',
                    data: 'NR_AGENDA',
                    width: '8%',
                    sClass: 'text-center',
                },
                {
                    title: 'Cliente',
                    data: 'NM_PESSOA',
                    width: '20%',
                },
                {
                    title: 'Data Envio',
                    data: 'DT_ENVIO',
                    width: '10%',
                },
                {
                    title: 'Data Registro',
                    data: 'DT_REGISTRO',
                    width: '10%',
                },
                {
                    title: 'Status',
                    data: 'ST_ENVIO',
                    width: '8%',
                    sClass: 'text-center',
                },
                {
                    title: '#',
                    data: 'action',
                    width: '15%',
                }
            ],
            order: [
                [4, 'desc']
            ],
        });
    });

    $(document).on('click', '.ver-email', function(e) {

        $.ajax({
            url: window.routesFollowUp.getEmailFollow,
            method: 'GET',
            data: {
                nr_envio: $(this).data('id'),
                nr_agenda: $(this).data('nr_agenda'),
                nr_contexto: $(this).data('nr_contexto')
            },
            beforeSend: function() {
                $("#loading").removeClass('hidden');
            },
            success: function(result) {
                $("#loading").addClass('hidden');
                $('#modal-email').modal('show');
                $('#assunto').val(result[0].DS_ASSUNTO);
                $('#from').val(result[0].DS_EMAILREM);
                $('#to').val(result[0].DS_EMAILDEST);
                $('#message').val($('<div/>').html(result[0].DS_MENSAGEM).text());

                let anexos = '';
                result[0].ANEXOS.forEach(item => {
                    anexos +=
                        `<button class="btn btn-secondary btn-sm mr-1">${item.TITULO}</button>`;
                });
                $('#lista-anexos').html(anexos);
            }
        });
    });

    $(document).on('click', '.reenviar-email', function(e) {
        let nr_envio = $(this).data('id');

        Swal.fire({
            title: 'Atenção!',
            text: "Deseja realmente reenviar esse email?",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, reenviar!',
            cancelButtonText: 'Cancelar',
            customClass: {
                title: 'my-small-title',
                htmlContainer: 'my-small-text',
                confirmButton: 'btn btn-primary btn-sm mr-1',
                cancelButton: 'btn btn-secondary btn-sm',
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                update_email = 1;
                ReenviaFollow(nr_envio, update_email);
            }
        });
    });

    $(document).on('click', '.btn-motivo-falha', function(e) {
        let motivo = $(this).data('motivo');

        Swal.fire({
            title: 'Motivo da Falha',
            text: motivo,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Fechar',
            customClass: {
                title: 'my-small-title',
                htmlContainer: 'my-small-text',
                confirmButton: 'btn btn-secondary btn-sm',
            },
            buttonsStyling: false
        });
    });

    function ReenviaFollow(nr_envio, update_email) {
        $.ajax({
            url: window.routesFollowUp.reenviaFollow,
            method: 'POST',
            data: {
                _token: $("[name=csrf-token]").attr("content"),
                nr_envio: nr_envio,
                email: update_email
            },
            beforeSend: function() {
                $("#loading").removeClass('hidden');
            },
            success: function(response) {
                $("#loading").addClass('hidden');
                if (response.error) {
                    msgToastr(response.error, 'warning');
                } else {
                    msgToastr(response.success, 'success');
                }
            }
        });
    }

});
