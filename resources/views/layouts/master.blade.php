@extends('adminlte::page')

@push('content')
    @include('layouts.components.btn-expansivo-modal')
@endpush

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


    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte_custom.css?v=14') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/boleto.css?v=1') }}">
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

    <script src="{{ asset('vendor/adminlte/dist/js/select2.min.js?v=2') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/select2-pt-br.min.js?v=2') }}"></script>


    <script src="{{ asset('vendor/adminlte/dist/js/inputmask.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/inputmask.extensions.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/handlebars.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/moment.min.js') }}"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="{{ asset('vendor/adminlte/dist/js/html5-qrcode.min.js') }}"></script>


    <script src="{{ asset('vendor/adminlte/dist/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/jquery.ui.touch-punch.min.js') }}"></script>

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", async () => {

            const firebaseConfig = {
                apiKey: "AIzaSyC2MUvepLCHUVg6ondQ8plEbiutJ2sEYz0",
                authDomain: "meuapppwa-f72da.firebaseapp.com",
                projectId: "meuapppwa-f72da",
                storageBucket: "meuapppwa-f72da.firebasestorage.app",
                messagingSenderId: "629286230886",
                appId: "1:629286230886:web:f5d45aaea590a725bd06a7",
                measurementId: "G-1K1VHKY9XJ"
            };

            firebase.initializeApp(firebaseConfig);
            const messaging = firebase.messaging();

            try {
                // Pede permissão ao usuário
                const permission = await Notification.requestPermission();

                if (permission !== "granted") {
                    console.log("Permissão negada");
                    return;
                }

                // Gera o token usando a sua VAPID PUBLIC KEY
                const token = await messaging.getToken({
                    vapidKey: "BKyzNZVpjPCLXop4YWUNxN6ipedqYUw3invK9L35JrH8_rsaQPGUZLTR3DWa_YHOmok6GJIAi7DSBKMXGcujNJg"
                });

                console.log("TOKEN DO DISPOSITIVO:", token);

                // Envie o token ao Laravel
                await fetch("/fcm/device-token", {
                    method: "POST",
                    credentials: "include",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                         "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        token                        
                    })
                });

            } catch (error) {
                console.error("Erro ao obter token:", error);
            }

        });
    </script>


    <script>
        $.extend(true, $.fn.dataTable.defaults, {
            ajax: {
                error: function(xhr, status, error) {
                    if (xhr.status === 401) {

                        // Salva a URL atual no sessionStorage antes de redirecionar
                        sessionStorage.setItem('redirect_url', window.location.href);

                        Swal.fire({
                            title: 'Sessão Expirada!',
                            text: 'Sua sessão expirou. Você será redirecionado para login.',
                            icon: 'warning',
                            confirmButtonColor: '#D43343',
                            confirmButtonText: 'OK'
                        }).then(function() {
                            window.location.href = '/login'; // Altere para a URL da sua tela de login
                        });
                    }
                }
            }
        });

        document.addEventListener("DOMContentLoaded", function() {


            $('.input-venda').inputmask({
                mask: 'decimal',
                radixPoint: ',',
            });

            $('#phone').inputmask({
                mask: ['(99)9999-9999', '(99)99999-9999']
            });

            $('.date-mask').inputmask({
                mask: ['99/99/9999']
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

    {{-- Script de Funções --}}
    <script src="{{ asset('vendor/adminlte/dist/js/script-functions.js?v=13') }}"></script>
@endpush
