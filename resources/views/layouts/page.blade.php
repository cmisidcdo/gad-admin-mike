<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title>GAD Database @yield('page-title', 'Dashboard')</title>
    <meta content="" name="description" />
    <meta content="" name="keywords" />

    <!-- Favicons -->
    <link href="{{ asset('assets/img/cdo-seal.png') }}" rel="icon" />
    <link href="{{ asset('assets/img/cdo-seal.png') }}" rel="apple-touch-icon" />

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet" />

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet" />

    <!-- Template Main CSS File -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" />

    <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: Nov 17 2023 with Bootstrap v5.3.2
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
    ======================================================== -->
</head>

<body>
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ url('/home') }}" class="logo d-flex align-items-center">
                <img src="{{ asset('assets/img/gad-logo.png') }}" alt="" />
                <span class="d-none d-lg-block">GAD Database</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div>
        <!-- End Logo -->

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">

                <li class="nav-item dropdown pe-3">
                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle" style="font-size: 25px;"></i>
                        <span class="d-none d-md-block dropdown-toggle ps-2">{{ Auth::user()->name }}</span> </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6>{{ Auth::user()->name }}</h6>
                            <span>{{ Auth::user()->user_role_id == 1 ? 'System Administration' : 'User' }}</span>
                        </li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('/change-password') }}">
                                <i class="ri-lock-password-line"></i>
                                <span>Change Password</span>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </a>
                        </li>
                    </ul>
                    <!-- End Profile Dropdown Items -->
                </li>
                <!-- End Profile Nav -->
            </ul>
        </nav>
        <!-- End Icons Navigation -->
    </header>
    <!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('home') ? '' : 'collapsed' }}" href="{{ url('/home') }}">
                    <i class="bi bi-grid"></i>
                    <span>Home</span>
                </a>
            </li>
            <!-- End Dashboard Nav -->

            <li class="nav-item">
                <a class="nav-link {{ request()->is('plan-budget*') ? '' : 'collapsed' }}" href="{{ url('/plan-budget') }}">
                    <i class="bi bi-folder"></i>
                    <span>Plan and Budget</span>
                </a>
            </li>
            <!-- End Plan and Budget Nav -->

            <li class="nav-item">
                <a class="nav-link {{ request()->is('accomplishment*') ? '' : 'collapsed' }}" href="{{ url('/accomplishment') }}">
                    <i class="bi bi-bag"></i>
                    <span>Accomplishment</span>
                </a>
            </li>
            <!-- End Accomplishment Nav -->

            @php
            $userID = Auth::user()->id;
            $menu = App\Models\TblCmsMenu::all();
            $permissions = App\Models\TblUserAccessPermission::where('id_user', $userID)->get()->sortBy('id_cms_menu');
            @endphp

            @foreach ($permissions as $permission)

            @if($permission->id_cms_menu == '3' && ($permission->create == "1" || $permission->read == "1" || $permission->update == "1" || $permission->delete == "1"))
            <li class="nav-item">
                <a class="nav-link {{ request()->is('archive') ? '' : 'collapsed' }}" href="{{ url('/archive') }}">
                    <i class="bi bi-archive"></i>
                    <span>Archive</span>
                </a>
            </li>
            @endif
            <!-- End Archive Nav -->

            @if($permission->id_cms_menu == '4' && ($permission->create == "1" || $permission->read == "1" || $permission->update == "1" || $permission->delete == "1"))
            <li class="nav-item">
                <a class="nav-link {{ request()->is('reference') ? '' : 'collapsed' }}" href="{{ url('/reference') }}">
                    <i class="bi bi-eyeglasses"></i>
                    <span>Reference</span>
                </a>
            </li>
            @endif
            <!-- End Reference Nav -->

            @if($permission->id_cms_menu == '5' && ($permission->create == "1" || $permission->read == "1" || $permission->update == "1" || $permission->delete == "1"))
            <li class="nav-item">
                <a class="nav-link {{ request()->is('user-administration') ? '' : 'collapsed' }}" href="{{ url('/user-administration') }}">
                    <i class="bi bi-people"></i>
                    <span>User Administration</span>
                </a>
            </li>
            @endif
            <!-- End User Administration Nav -->

            @if($permission->id_cms_menu == '6' && ($permission->create == "1" || $permission->read == "1" || $permission->update == "1" || $permission->delete == "1"))
            <li class="nav-item">
                <a class="nav-link {{ request()->is('recycle-bin') ? '' : 'collapsed' }}" href="{{ url('/recycle-bin') }}">
                    <i class="bi bi-trash"></i>
                    <span>Recycle Bin</span>
                </a>
            </li>
            @endif
            <!-- End Recycle Bin Nav -->

            @if($permission->id_cms_menu == '7' && ($permission->create == "1" || $permission->read == "1" || $permission->update == "1" || $permission->delete == "1"))
            @php
            $pendingDocsCount = App\Models\TblDocumentInformation::where('approval_status', 'pending')->count();
            @endphp
            <li class="nav-item">
                <a class="nav-link {{ request()->is('document-approval') ? '' : 'collapsed' }} d-flex justify-content-between align-items-center" href="{{ url('/document-approval') }}">
                    <div>
                        <i class="bi bi-file-earmark-check"></i>
                        <span>Document Approval</span>
                    </div>
                    @if($pendingDocsCount > 0)
                    <span class="badge bg-danger text-white rounded-pill">{{ $pendingDocsCount }}</span>
                    @endif
                </a>
            </li>
            @endif
            <!-- End Document Approval Nav -->

            @endforeach

            {{-- Not Yet Submitted — Admin only --}}
            @if((int) Auth::user()->user_role_id === 1)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('not-yet-submitted') ? '' : 'collapsed' }}" href="{{ url('/not-yet-submitted') }}">
                    <i class="ri-user-unfollow-line"></i>
                    <span>Not Yet Submitted</span>
                </a>
            </li>
            @endif

        </ul>
    </aside>
    <!-- End Sidebar-->

    <main id="main" class="main">
        <div class="pagetitle">
            <h1></h1>
        </div>
        <!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                @yield('page-content')
            </div>
        </section>
    </main>
    <!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer" style="background-color: white;">

        <div class="copyright" style="text-align: left;">
            <div class="container">
                <div class="row">
                    <div class="col-md-7 mb-3">
                        <div class="row">
                            <div class="col-12 mb-5 pb-5">
                                <h1>GAD Database</h1>
                            </div>
                            <div class="col-12 mt-4">
                                If you have issues encountered and inquiries:
                                <strong>
                                    <span>
                                        <a href="https://services.cagayandeoro.gov.ph/helpdesk/">
                                            CMISID Helpdesk
                                        </a>
                                    </span>
                                </strong>.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <img src="{{ asset('assets/img/cdo-seal.png') }}" alt="" style="height: 90px; width: 90px;" />
                            </div>
                            <div class="col-md-8 mt-3">
                                <img src="{{ asset('assets/img/headergoldencdologo.png') }}" alt="" style="height: 60px; width: 150px;" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <img src="{{ asset('assets/img/rise.png') }}" alt="" class="mt-3" style="height: 110px; width: 230px;" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 ">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <a href="https://cagayandeoro.gov.ph/" target="_blank" class="btn btn-primary btn-lg mt-3 rounded-pill d-flex align-items-center justify-content-center" style="height: 60px; background-color: #FACD06; text-decoration: none; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                    <span style="color: black; font-size: 15px;">Visit Official Website</span>
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="https://cagayandeoro.gov.ph/index.php/news/the-city-mayor/rise1.html" target="_blank" class="btn btn-primary btn-lg mt-5 rounded-pill d-flex align-items-center justify-content-center" style="height: 60px; background-color: #5B9EB4; text-decoration: none; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                    <span style="font-size: 15px;">Learn RISE Platform</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-2 mb-2">
                    <div class="col-12 text-center">
                        <div class="row">
                            <div class="col-12">
                                <img src="{{ asset('assets/img/ict-logo.png') }}" alt="" style="height: 50px; width: 50px;" />
                            </div>
                            <div class="col-12">
                                <span style="font-size: 12px;">Powered by: <span>City Management Information Systems and Innovation Department</span>.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </footer>
    <!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/quill/quill.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>

    <!-- Template Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

</body>

</html>