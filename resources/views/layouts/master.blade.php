<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/now-ui-dashboard.css?v=1.5.0') }}" rel="stylesheet" />
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.0/dist/echo.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body class="">
    <div class="wrapper">
        <div class="sidebar" data-color="orange">
            <div class="logo">
                <a href="{{ url('/') }}" class="simple-text logo-normal">Digital Complaint Manager</a>
            </div>
            <div class="sidebar-wrapper" id="sidebar-wrapper">
                <ul class="nav">
                    <li class="@yield('sidebar-active-dashboard')">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="now-ui-icons design_app"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="@yield('sidebar-active-colleges')">
                        <a href="{{ url('/admin/colleges') }}">
                            <i class="now-ui-icons education_hat"></i>
                            <p>Colleges</p>
                        </a>
                    </li>
                    <li class="@yield('sidebar-active-users')">
                        <a href="{{ url('/admin/users') }}">
                            <i class="now-ui-icons users_single-02"></i>
                            <p>Users</p>
                        </a>
                    </li>
                    <li class="@yield('sidebar-active-reports')">
                        <a href="{{ route('admin.reports') }}">
                            <i class="now-ui-icons files_single-copy-04"></i>
                            <p>Reports</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="main-panel" id="main-panel">
            <nav class="navbar navbar-expand-lg navbar-transparent bg-primary navbar-absolute">
                <div class="container-fluid">
                    <div class="navbar-wrapper">
                        <div class="navbar-toggle">
                            <button type="button" class="navbar-toggler">
                                <span class="navbar-toggler-bar bar1"></span>
                                <span class="navbar-toggler-bar bar2"></span>
                                <span class="navbar-toggler-bar bar3"></span>
                            </button>
                        </div>
                        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-bar navbar-kebab"></span>
                        <span class="navbar-toggler-bar navbar-kebab"></span>
                        <span class="navbar-toggler-bar navbar-kebab"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="navigation">
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="now-ui-icons ui-1_zoom-bold"></i>
                                    <p><span class="d-lg-none d-md-block">Search</span>Search</p>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="searchDropdown" style="width: 300px;">
                                    <div class="px-3 py-2">
                                        <input type="text" class="form-control" id="global-search" placeholder="Search complaints, colleges, users...">
                                    </div>
                                    <div id="search-results" class="dropdown-content"></div>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="now-ui-icons media-1_button-power"></i>
                                    <p><span class="d-lg-none d-md-block">Logout</span>Logout</p>
                                </a>
                                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="panel-header panel-header-sm"></div>

            <div class="content">
                @yield('content')
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="copyright" id="copyright">
                        © <script>document.getElementById('copyright').appendChild(document.createTextNode(new Date().getFullYear()))</script>,
                        Digital Complaint Manager
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/now-ui-dashboard.min.js?v=1.5.0') }}"></script>
    <script>
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env('PUSHER_APP_KEY') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            forceTLS: true
        });
    </script>
    @yield('scripts')
</body>
</html>