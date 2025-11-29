@extends('adminlte::auth.login')

@section('adminlte_js')
    <script src="{{ asset('/sw.js') }}"></script>
    <script>
        document.getElementById('redirect_url').value = sessionStorage.getItem('redirect_url');
        if ("serviceWorker" in navigator) {
            // Registrar SEU service worker do PWA
            navigator.serviceWorker.register("/sw.js")
                .then(reg => console.log("SW PWA registrado:", reg))
                .catch(err => console.error("Erro ao registrar sw.js:", err));

            // Registrar o service worker do Firebase Messaging
            navigator.serviceWorker.register("/firebase-messaging-sw.js")
                .then(reg => console.log("SW Firebase Messaging registrado:", reg))
                .catch(err => console.error("Erro ao registrar firebase-messaging-sw.js:", err));
        }
    </script>

    @stack('js')
    @yield('js')
@stop
