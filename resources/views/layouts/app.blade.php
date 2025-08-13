<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Healthcare Grievance Resolution')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f4f9; font-family: 'Inter', sans-serif; }
        .navbar { background-color: #4CAF50; }
        .navbar-brand, .nav-link { color: white !important; }
        .navbar-brand:hover, .nav-link:hover { color: #e6e6e6 !important; }
        .card-header { background-color: #4CAF50; color: white; }
        .btn-primary { background-color: #4CAF50; border-color: #4CAF50; }
        .btn-primary:hover { background-color: #45a049; border-color: #45a049; }
        @media (max-width: 576px) {
            .container { padding: 10px; }
            .card { margin-bottom: 15px; }
            .btn { width: 100%; margin-bottom: 10px; }
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">Healthcare Grievance Resolution</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto flex items-center space-x-4">

                        @auth('web')
                            <li class="nav-item">
    <a class="nav-link" href="{{ route('home') }}" onclick="scrollToComplaintHistory(event)">{{ __('Complaint History') }}</a>
</li>

                            <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                            <button type="submit" class="btn-link px-4 py-2 rounded-md text-sm font-medium" style="color:rgb(253, 255, 246); background-color: transparent; border: none;">
                            {{ __('Logout') }}
                            </button>
                            </form>
                            </li>

                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function scrollToComplaintHistory(event) {
        event.preventDefault();
        const homePath = "{{ route('home') }}".replace(window.location.origin, '');
        const currentPath = window.location.pathname;

        if (currentPath === homePath) {
            const section = document.getElementById('complaint-history');
            if (section) section.scrollIntoView({ behavior: 'smooth' });
        } else {
            window.location.href = "{{ route('home') }}#complaint-history";
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.location.hash === '#complaint-history') {
            const section = document.getElementById('complaint-history');
            if (section) section.scrollIntoView({ behavior: 'smooth' });
        }
    });
</script>

    @yield('scripts')
</body>
</html>