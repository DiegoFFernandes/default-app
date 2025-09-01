@extends('adminlte::auth.login')

@section('adminlte_js')
    <script src="{{ asset('/sw.js') }}"></script>
    <script>
        document.getElementById('redirect_url').value = sessionStorage.getItem('redirect_url');
        if ("serviceWorker" in navigator) {
            // Register a service worker hosted at the root of the
            // site using the default scope.
            navigator.serviceWorker.register("/sw.js").then(
                (registration) => {
                    console.log("Service worker registration succeeded:", registration);
                },
                (error) => {
                    console.error(`Service worker registration failed: ${error}`);
                },
            );
        } else {
            console.error("Service workers are not supported.");
        }
    </script>

    @stack('js')
    @yield('js')
@stop
