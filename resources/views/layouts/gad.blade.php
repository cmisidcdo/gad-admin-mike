<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>GAD System @yield('page-title')</title>

    <link href="{{ asset('assets/img/cdo-seal.png') }}" rel="icon" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Nunito:300,400,600,700|Poppins:300,400,500,600,700" rel="stylesheet" />

    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/gad.css') }}?v={{ filemtime(public_path('assets/css/gad.css')) }}" rel="stylesheet" />
</head>

<body class="@yield('body-class')">
    <header class="gad-header @yield('header-class')">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('/home') }}" class="gad-logo">
                <img src="{{ asset('assets/img/gad-logo.png') }}" alt="" />
                <span>GAD System</span>
            </a>
            <i class="bi bi-list gad-toggle" onclick="document.body.classList.toggle('gad-mini')"></i>
        </div>

        <div class="dropdown">
            <a class="gad-user dropdown-toggle" href="#" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i>
                <span>{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ url('/change-password') }}"><i class="ri-lock-password-line me-2"></i>Change Password</a></li>
                <li>
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </li>
            </ul>
        </div>
    </header>

    <aside class="gad-sidebar">
        <a href="{{ url('/home') }}" class="{{ request()->is('home') ? 'active' : '' }}"><i class="bi bi-grid"></i><span>Home</span></a>
        <a href="{{ url('/archive') }}" class="{{ request()->is('archive') ? 'active' : '' }}"><i class="bi bi-database"></i><span>Database</span></a>
        <a href="{{ url('/plan-budget') }}" class="{{ request()->is('plan-budget*') ? 'active' : '' }}"><i class="bi bi-folder-fill"></i><span>Plan and Budget</span></a>
        <a href="{{ url('/accomplishment') }}" class="{{ request()->is('accomplishment*') ? 'active' : '' }}"><i class="bi bi-bag"></i><span>Accomplishment</span></a>
    </aside>

    <main class="gad-main">
        @yield('page-content')
    </main>

    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/gad-money.js') }}?v={{ filemtime(public_path('assets/js/gad-money.js')) }}"></script>
</body>

</html>

