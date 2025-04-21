@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fa fa-list-ul"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total</span>
                        <span class="info-box-number" id="soma-geral">

                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="far fa-thumbs-down"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text maior-divida">Maior divida</span>
                        <span class="info-box-number" id="maior-divida">

                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fas fa-sort-amount-up-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text titulos">Quantidade de Titulos</span>
                        <span class="info-box-number" id="maior-divida">

                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="list-cobranca">
                            <table class="table table-striped compact" id="table-rel-cobranca"
                                style="width: 100%;font-size: 13px">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Salary</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@stop

@section('js')
    <script>
        const dados = [{
                "CD_PESSOA": "7231",
                "NM_PESSOA": "7231-ABRAHAO E BUCHNER TRANSPORTES LTDA ME",
                "NR_CNPJCPF": "11.348.528/0001-72",
                "TIPOCONTA": "2 (+) Contas a Receber",
                "VL_SALDO": "560.00",
                "TITULOS": "1",
                "CD_REGIAOCOMERCIAL": "13",
                "DS_REGIAOCOMERCIAL": "PVA",
                "CD_AREACOMERCIAL": "99",
                "DS_AREACOMERCIAL": "SEM AREA"
            },
            {
                "CD_PESSOA": "7269",
                "NM_PESSOA": "7269-DEMORI & VERONEZ LTDA ME",
                "NR_CNPJCPF": "07.558.512/0001-17",
                "TIPOCONTA": "2 (+) Contas a Receber",
                "VL_SALDO": "1352.00",
                "TITULOS": "4",
                "CD_REGIAOCOMERCIAL": "13",
                "DS_REGIAOCOMERCIAL": "PVA",
                "CD_AREACOMERCIAL": "99",
                "DS_AREACOMERCIAL": "SEM AREA"
            }
        ];

        // Criar o nó pai (área comercial)
        const areasComerciais = {};
        dados.forEach(item => {
            const areaId = item.CD_AREACOMERCIAL;
            if (!areasComerciais[areaId]) {
                areasComerciais[areaId] = {
                    tt_key: `area_${areaId}`,
                    tt_parent: 0,
                    name: item.DS_AREACOMERCIAL,
                    tipo: 'Área Comercial'
                };
            }
        });

        const pessoas = dados.map(item => ({
            tt_key: item.CD_PESSOA,
            tt_parent: `area_${item.CD_AREACOMERCIAL}`,
            name: item.NM_PESSOA,
            cnpj: item.NR_CNPJCPF,
            saldo: item.VL_SALDO,
            titulos: item.TITULOS
        }));

        $('#table-rel-cobranca').treeTable({
            data: dadosParaTreeTable,
            columns: [{
                    data: 'name',
                    title: 'Nome'
                },
                {
                    data: 'cnpj',
                    title: 'CNPJ'
                },
                {
                    data: 'saldo',
                    title: 'Saldo'
                },
                {
                    data: 'titulos',
                    title: 'Títulos'
                }
            ]
        });
        // $('#table-rel-cobranca').DataTable({
        //     data: dados, // suponha que venha algo como [{ tt_key: 1, tt_parent: 0, name: 'CEO' }, ...]
        //     rowId: 'CD_AREACOMERCIAL', // ID único por linha
        //     treeTable: {
        //         parentIdSrc: 'CD_REGIAOCOMERCIAL',
        //         childIndent: 20
        //     },
        //     columns: [{
        //             data: 'NM_PESSOA',
        //             title: 'Nome'
        //         },
        //         {
        //             data: 'NR_CNPJCPF',
        //             title: 'CNPJ/CPF'
        //         },
        //         {
        //             data: 'VL_SALDO',
        //             title: 'Saldo'
        //         },
        //         {
        //             data: 'TITULOS',
        //             title: 'Títulos'
        //         }
        //     ]
        // });






        // var table = $('#table-rel-cobranca').treeTable({
        //     language: {
        //         url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
        //     },
        //     pageLength: 50,
        //     // responsive: true,
        //     // "searching": true,
        //     // "bInfo": false,
        //     scrollX: true,
        //     ajax: "{{ route('teste-cobranca') }}",
        //     columns: [{
        //             data: "CD_AREACOMERCIAL",
        //             name: "CD_AREACOMERCIAL",
        //             visible: true
        //         },
        //         {
        //             data: "CD_REGIAOCOMERCIAL",
        //             name: "CD_REGIAOCOMERCIAL",
        //             visible: true
        //         },
        //         {
        //             data: "NM_PESSOA",
        //             name: "NM_PESSOA"
        //         },

        //         {
        //             data: "VL_SALDO",
        //             name: "VL_SALDO",

        //         }
        //     ]

        // });
    </script>
@stop
