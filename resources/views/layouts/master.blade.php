@extends('adminlte::page')

@push('css')
    <!-- DataTables-->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/datatables/responsive.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/datatables/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/datatables/select.dataTables.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/4.0.3/css/fixedHeader.bootstrap4.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.5.1/css/rowGroup.dataTables.css">


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <!-- Tabulator CSS -->
    <link href="https://unpkg.com/tabulator-tables@6.3.1/dist/css/tabulator.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/toastr.min.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/select2-bootstrap4.min.css') }}">


    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte_custom.css?v=2') }}">
@endpush

@push('js')
    <!-- DataTables -->
    <script src="{{ asset('vendor/adminlte/dist/js/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/datatables/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/datatables/dataTables.select.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/datatables/select.dataTables.js') }}"></script>

    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.4/api/sum().js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.19/dataRender/datetime.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.4/js/responsive.dataTables.js"></script>


    <script src="https://cdn.datatables.net/fixedheader/4.0.3/js/dataTables.fixedHeader.js"></script>
    <script src="https://cdn.datatables.net/rowgroup/1.5.1/js/dataTables.rowGroup.js"></script>
    <script src="https://cdn.datatables.net/rowgroup/1.5.1/js/rowGroup.dataTables.js"></script>


    <!-- Tabulator JS -->
    <script src="https://unpkg.com/tabulator-tables@6.3.1/dist/js/tabulator.min.js"></script>
    <script type="text/javascript" src="https://oss.sheetjs.com/sheetjs/xlsx.full.min.js"></script>


    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('vendor/adminlte/dist/js/toastr.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/select2.min.js') }}"></script>


    <script src="{{ asset('vendor/adminlte/dist/js/inputmask.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/inputmask.extensions.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/handlebars.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/moment.min.js') }}"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="{{ asset('vendor/adminlte/dist/js/html5-qrcode.min.js') }}"></script>

    <script>
        function msgToastr(msg, classe) {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-bottom-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "3000",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }
            toastr[classe](msg);
        }

        function exportarParaExcel(dados, nomeArquivo = "dados.xlsx", nomeAba = "Planilha") {
            // Cria uma nova planilha a partir dos dados (array de objetos)
            const worksheet = XLSX.utils.json_to_sheet(dados);

            // Cria o workbook (arquivo Excel)
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, nomeAba);

            // Faz o download do arquivo
            XLSX.writeFile(workbook, nomeArquivo);
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            $('#phone').inputmask({
                mask: ['(99)9999-9999', '(99)99999-9999']
            });
            $('#daterange').inputmask({
                mask: ['99/99/9999 - 99/99/9999']
            });
            $('.date-mask').inputmask({
                mask: ['99/99/9999']
            });

            $('#daterange').daterangepicker({
                autoUpdateInput: false,
            }).attr('readonly', true);


            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                    'DD/MM/YYYY'));
                inicioData = picker.startDate.format('MM/DD/YYYY');
                fimData = picker.endDate.format('MM/DD/YYYY');
            });
            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val("");
                inicioData = 0;
                fimData = 0;
            });

            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            @if (session('warning'))
                toastr.warning("{{ session('warning') }}");
            @endif

            @if (session('info'))
                toastr.info("{{ session('info') }}");
            @endif


            if (window.innerWidth <= 768) { // Tamanho para celulares
                document.body.classList.remove("sidebar-mini-xs");
                document.body.classList.add("sidebar-collapse"); // Mantém o menu fechado
            }
        });
    </script>
   
@endpush
