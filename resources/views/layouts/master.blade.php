@extends('adminlte::page')

@section('meta_tags')
    <meta name="theme-color" content="#dc3545">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'Portal DF') }}">
@endsection

@push('content')
    @include('layouts.components.btn-expansivo-modal')
@endpush

@push('css')
    <!-- DataTables-->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/datatables/responsive.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/datatables/dataTables.bootstrap.min.css') }}">
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css"> --}}{{-- duplicata — versão local já cobre --}}
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap.min.css"> --}}{{-- botões de exportação: carregar por página --}}
    {{-- <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/datatables/select.dataTables.css') }}"> --}}{{-- seleção de linha: carregar por página --}}
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/4.0.3/css/fixedHeader.bootstrap4.css"> --}}{{-- extensão fixedHeader: carregar por página --}}
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.5.1/css/rowGroup.dataTables.css"> --}}{{-- extensão rowGroup: carregar por página --}}

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    {{-- <link href="https://unpkg.com/tabulator-tables@6.3.1/dist/css/tabulator.min.css" rel="stylesheet"> --}}{{-- Tabulator: pesado, carregar por página --}}

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/toastr.min.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/select2-bootstrap4.min.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte_custom.css?v=22') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/datatables/custom_datatables.css?v=1') }}">
    {{-- <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/boleto.css?v=1') }}"> --}}{{-- específico para layout de boleto --}}
@endpush

@push('js')
    <!-- DataTables -->
    <script src="{{ asset('vendor/adminlte/dist/js/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/datatables/dataTables.responsive.min.js') }}"></script>
    {{-- Botões de exportação (Excel / CSV / Print) — carregar somente nas páginas que usam --}}
    {{-- <script src="{{ asset('vendor/adminlte/dist/js/datatables/dataTables.buttons.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('vendor/adminlte/dist/js/datatables/buttons.bootstrap.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('vendor/adminlte/dist/js/datatables/jszip.min.js') }}"></script> --}}{{-- pesado: necessário só para export Excel --}}
    {{-- <script src="{{ asset('vendor/adminlte/dist/js/datatables/buttons.html5.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('vendor/adminlte/dist/js/datatables/dataTables.select.js') }}"></script> --}}{{-- seleção de linha: carregar por página --}}
    {{-- <script src="{{ asset('vendor/adminlte/dist/js/datatables/select.dataTables.js') }}"></script> --}}

    {{-- <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script> --}}{{-- duplicata CDN — usar versão local se necessário --}}
    {{-- <script src="https://cdn.datatables.net/plug-ins/1.13.4/api/sum().js"></script> --}}{{-- plugin específico: carregar por página --}}
    {{-- <script src="https://cdn.datatables.net/plug-ins/1.10.19/dataRender/datetime.js"></script> --}}{{-- plugin específico: carregar por página --}}
    {{-- <script src="https://cdn.datatables.net/responsive/3.0.4/js/responsive.dataTables.js"></script> --}}{{-- duplicata CDN — versão local já carregada --}}

    {{-- <script src="https://cdn.datatables.net/fixedheader/4.0.3/js/dataTables.fixedHeader.js"></script> --}}{{-- extensão fixedHeader: carregar por página --}}
    {{-- <script src="https://cdn.datatables.net/rowgroup/1.5.1/js/dataTables.rowGroup.js"></script> --}}{{-- extensão rowGroup: carregar por página --}}
    {{-- <script src="https://cdn.datatables.net/rowgroup/1.5.1/js/rowGroup.dataTables.js"></script> --}}{{-- duplicata da linha acima --}}

    {{-- Tabulator + SheetJS — pesados, carregar somente nas páginas que usam --}}
    {{-- <script src="https://unpkg.com/tabulator-tables@6.3.1/dist/js/tabulator.min.js"></script> --}}
    {{-- <script type="text/javascript" src="https://oss.sheetjs.com/sheetjs/xlsx.full.min.js"></script> --}}

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('vendor/adminlte/dist/js/toastr.min.js') }}"></script>

    <script src="{{ asset('vendor/adminlte/dist/js/select2.min.js?v=2') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/select2-pt-br.min.js?v=2') }}"></script>

    <script src="{{ asset('vendor/adminlte/dist/js/inputmask.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/inputmask.extensions.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/jquery.inputmask.js') }}"></script>
    {{-- <script src="{{ asset('vendor/adminlte/dist/js/handlebars.min.js') }}"></script> --}}{{-- não utilizado globalmente --}}
    <script src="{{ asset('vendor/adminlte/dist/js/moment.min.js') }}"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    {{-- <script src="{{ asset('vendor/adminlte/dist/js/html5-qrcode.min.js') }}"></script> --}}{{-- leitor QR: pesado, carregar por página --}}

    {{-- <script src="{{ asset('vendor/adminlte/dist/js/jquery-ui.min.js') }}"></script> --}}{{-- jQuery UI: carregar somente nas páginas que usam drag/sort --}}
    {{-- <script src="{{ asset('vendor/adminlte/dist/js/jquery.ui.touch-punch.min.js') }}"></script> --}}

    {{-- <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script> --}}{{-- duplicata — AdminLTE já carrega o Bootstrap --}}

    <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js"></script>



    <script>
        // Suprime o alert padrão do DataTables — tratamos via Swal abaixo
        $.fn.dataTable.ext.errMode = 'none';

        $.extend(true, $.fn.dataTable.defaults, {
            ajax: {
                error: function(xhr, textStatus) {
                    // Requisição cancelada pelo browser (troca de página) — ignorar
                    if (textStatus === 'abort') return;

                    // Para o spinner das tabelas ativas
                    $($.fn.dataTable.tables(true)).DataTable().processing(false);

                    // 401 → sessão expirada
                    if (xhr.status === 401) {
                        sessionStorage.setItem('redirect_url', window.location.href);
                        Swal.fire({
                            title: 'Sessão Expirada!',
                            text: 'Sua sessão expirou. Você será redirecionado para o login.',
                            icon: 'warning',
                            confirmButtonColor: '#D43343',
                            confirmButtonText: 'OK',
                            allowOutsideClick: false,
                        }).then(function() {
                            window.location.href = '/login';
                        });
                        return;
                    }

                    // 200 com qualquer textStatus = dado carregou ou problema de formato não crítico → ignorar
                    if (xhr.status === 200) return;

                    const msgs = {
                        403: 'Você não tem permissão para acessar estes dados.',
                        404: 'Recurso não encontrado.',
                        422: 'Dados inválidos na requisição.',
                        500: 'Erro interno no servidor. Contate o suporte.',
                        503: 'Serviço temporariamente indisponível.',
                    };
                    const statusInfo = xhr.status ? 'HTTP ' + xhr.status : 'sem conexão com o servidor';
                    const texto = msgs[xhr.status] || 'Não foi possível carregar os dados. (' + statusInfo + ')';

                    Swal.fire({
                        title: 'Erro ao carregar dados',
                        text: texto,
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-sync-alt mr-1"></i> Tentar novamente',
                        cancelButtonText: 'Fechar',
                        confirmButtonColor: '#D43343',
                        reverseButtons: true,
                        allowOutsideClick: false,
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            $($.fn.dataTable.tables(true)).DataTable().ajax.reload(null, false);
                        }
                    });
                }
            }
        });

        document.addEventListener("DOMContentLoaded", function() {

            // Ajusta as colunas dos DataTables quando o menu lateral é expandido ou recolhido
            $(document).on('collapsed.lte.pushmenu shown.lte.pushmenu', function() {
                setTimeout(function() {
                    $($.fn.dataTable.tables(true))
                        .DataTable()
                        .columns.adjust();
                }, 300);
            });

            $('.money-mask').inputmask({
                mask: ['9,99', '99,99', '999,99', '9.999,99', '99.999,99', '999.999,99', '9.999.999,99'],
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
                console.log('Erro:', "{{ session('error') }}");
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
    <script src="{{ asset('vendor/adminlte/dist/js/script-functions.js?v=' . time()) }}"></script>
    <script>
        document.getElementById("ativarNotificacoesCheckbox").addEventListener("click", handleNotificationToggle);

        async function handleNotificationToggle() {
            const checkbox = document.getElementById("ativarNotificacoesCheckbox");

            if (!checkbox.checked) {
                await sendTokenToServer(null, "N");
                return showAlert("Notificações", "Você desativou as notificações.", "info");
            }

            initializeFirebase();

            try {
                const permission = await requestNotificationPermission();
                if (!permission) return;

                const token = await getDeviceToken();

                await sendTokenToServer(token, "S");

                showAlert("Notificações", "Notificações ativadas com sucesso!", "success");

            } catch (error) {
                console.error("Erro ao ativar notificações:", error);
                showAlert("Erro", "Não foi possível ativar as notificações.", "error");
            }
        }

        /* ---------------------------
            FIREBASE
        ---------------------------- */
        let firebaseInitialized = false;

        function initializeFirebase() {
            if (firebaseInitialized) return;

            firebase.initializeApp({
                apiKey: "{{ env('FMCAPI_KEY') }}",
                authDomain: "{{ env('FCM_AUTH_DOMAIN') }}",
                projectId: "{{ env('FCM_PROJECT_ID') }}",
                storageBucket: "{{ env('FCM_STORAGE_BUCKET') }}",
                messagingSenderId: "{{ env('FCM_MESSAGING_SENDER_ID') }}",
                appId: "{{ env('FCM_APP_ID') }}",
                measurementId: "{{ env('FCM_MEASUREMENT_ID') }}"
            });

            firebaseInitialized = true;
        }

        async function requestNotificationPermission() {
            const permission = await Notification.requestPermission();
            return permission === "granted";
        }

        async function getDeviceToken() {
            const messaging = firebase.messaging();

            return await messaging.getToken({
                vapidKey: "{{ env('FCM_VAPID_PUBLIC_KEY') }}"
            });
        }
    </script>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .catch(err => console.error('SW PWA erro:', err));
            navigator.serviceWorker.register('/firebase-messaging-sw.js')
                .catch(err => console.error('SW Firebase erro:', err));
        }
    </script>

    <script>
    (function () {
        var DISMISSED_KEY = 'pwa_banner_dismissed';

        var isStandalone = window.matchMedia('(display-mode: standalone)').matches || !!window.navigator.standalone;
        if (isStandalone || localStorage.getItem(DISMISSED_KEY)) return;

        var ua = navigator.userAgent;
        var isIOS = /iphone|ipad|ipod/i.test(ua) && !window.MSStream;
        var isMacSafari = !isIOS
            && ua.indexOf('Safari') !== -1
            && ua.indexOf('Chrome') === -1
            && ua.indexOf('Chromium') === -1
            && ua.indexOf('CriOS') === -1
            && ua.indexOf('FxiOS') === -1
            && ua.indexOf('Edg') === -1
            && /mac/i.test(navigator.platform || ua);

        var deferredPrompt = null;

        function dismiss() {
            localStorage.setItem(DISMISSED_KEY, '1');
            document.getElementById('pwa-banner').style.display = 'none';
        }

        if (isIOS) {
            setTimeout(function () {
                document.getElementById('pwa-ios').style.display = 'block';
                document.getElementById('pwa-banner').style.display = 'block';
            }, 2000);
            document.getElementById('pwa-ios-dismiss').addEventListener('click', dismiss);

        } else if (isMacSafari) {
            setTimeout(function () {
                document.getElementById('pwa-mac-safari').style.display = 'block';
                document.getElementById('pwa-banner').style.display = 'block';
            }, 2000);
            document.getElementById('pwa-mac-safari-dismiss').addEventListener('click', dismiss);

        } else {
            // Chrome, Edge, Brave — desktop e Android
            window.addEventListener('beforeinstallprompt', function (e) {
                e.preventDefault();
                deferredPrompt = e;
                document.getElementById('pwa-chromium').style.display = 'flex';
                document.getElementById('pwa-banner').style.display = 'block';
            });

            document.getElementById('pwa-install-btn').addEventListener('click', function () {
                if (!deferredPrompt) return;
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function (result) {
                    deferredPrompt = null;
                    if (result.outcome === 'accepted') localStorage.setItem(DISMISSED_KEY, '1');
                    document.getElementById('pwa-banner').style.display = 'none';
                });
            });

            document.getElementById('pwa-chromium-dismiss').addEventListener('click', dismiss);
        }

        window.addEventListener('appinstalled', function () {
            document.getElementById('pwa-banner').style.display = 'none';
        });
    })();
    </script>
@endpush

@push('content')
    <div id="pwa-banner" style="display:none; position:fixed; bottom:0; left:0; right:0; z-index:9999; background:#fff; border-top:3px solid #dc3545; box-shadow:0 -3px 16px rgba(0,0,0,.12); padding:12px 16px;">

        {{-- Chrome / Edge / Brave (Android e Desktop) --}}
        <div id="pwa-chromium" style="display:none; align-items:center; gap:12px;">
            <img src="{{ asset('img/android-chrome-192x192.png') }}" style="width:42px;height:42px;border-radius:10px;flex-shrink:0;">
            <div style="flex:1; min-width:0;">
                <div style="font-weight:600; font-size:14px; color:#212529;">Instalar {{ config('app.name') }}</div>
                <div style="font-size:12px; color:#6c757d;">Acesso rápido sem precisar abrir o navegador</div>
            </div>
            <button id="pwa-install-btn" class="btn btn-danger btn-sm" style="white-space:nowrap;">Instalar</button>
            <button id="pwa-chromium-dismiss" class="btn btn-link btn-sm text-muted p-1" style="flex-shrink:0; font-size:16px; line-height:1;">&times;</button>
        </div>

        {{-- iOS (iPhone / iPad) --}}
        <div id="pwa-ios" style="display:none;">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:8px;">
                <div style="flex:1;">
                    <div style="font-weight:600; font-size:14px; color:#212529; margin-bottom:4px;">
                        📱 Instalar {{ config('app.name') }} no iPhone / iPad
                    </div>
                    <div style="font-size:13px; color:#444; line-height:1.7;">
                        1. Toque em <strong>Compartilhar</strong>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 50 50" style="vertical-align:middle; margin:0 2px;" fill="#007aff">
                            <path d="M30.3 13.7L25 8.4l-5.3 5.3-1.4-1.4L25 5.6l6.7 6.7z"/>
                            <path d="M24 7h2v21h-2z"/>
                            <path d="M35 40H15c-1.7 0-3-1.3-3-3V19c0-1.7 1.3-3 3-3h7v2h-7c-.6 0-1 .4-1 1v18c0 .6.4 1 1 1h20c.6 0 1-.4 1-1V19c0-.6-.4-1-1-1h-7v-2h7c1.7 0 3 1.3 3 3v18c0 1.7-1.3 3-3 3z"/>
                        </svg>
                        no Safari<br>
                        2. Role e toque em <strong>"Adicionar à Tela de Início"</strong> <span style="font-size:15px;">＋</span>
                    </div>
                </div>
                <button id="pwa-ios-dismiss" class="btn btn-link btn-sm text-muted p-1" style="flex-shrink:0; font-size:20px; line-height:1;">&times;</button>
            </div>
        </div>

        {{-- Safari no macOS (Sonoma+) --}}
        <div id="pwa-mac-safari" style="display:none;">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:8px;">
                <div style="flex:1;">
                    <div style="font-weight:600; font-size:14px; color:#212529; margin-bottom:4px;">
                        🖥️ Instalar {{ config('app.name') }} no Mac
                    </div>
                    <div style="font-size:13px; color:#444; line-height:1.7;">
                        No Safari, clique em <strong>Arquivo</strong> → <strong>Adicionar ao Dock...</strong>
                    </div>
                </div>
                <button id="pwa-mac-safari-dismiss" class="btn btn-link btn-sm text-muted p-1" style="flex-shrink:0; font-size:20px; line-height:1;">&times;</button>
            </div>
        </div>

    </div>
@endpush
