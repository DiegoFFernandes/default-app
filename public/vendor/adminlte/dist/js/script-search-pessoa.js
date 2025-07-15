const modalId = $('#pessoa').data('modal');
const dropdownParent = modalId ? $(modalId) : $(document.body);

$("#pessoa")
    .select2({
        placeholder: "Pessoa",
        theme: "bootstrap4",
        width: "100%",
        allowClear: true,
        minimumInputLength: 2,
        dropdownParent: dropdownParent,
        ajax: {
            url: routes.searchPessoa,
            dataType: "json",
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.NM_PESSOA,
                            id: item.ID,
                            email: item.DS_EMAIL,
                            tipopessoa: item.CD_TIPOPESSOA,
                            phone: item.NR_CELULAR,
                        };
                    }),
                };
            },
            cache: true,
        },
    })
    .change(function (el) {
        var data = $(el.target).select2("data");
        $("#name").val(data[0].text);
        $("#email").val(data[0].email);
        $("#ds_tipopessoa").val(data[0].tipopessoa);
        $("#phone").val(data[0].phone);
    });

