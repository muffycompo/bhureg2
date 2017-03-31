<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Bingham University Student Registration Portal</title>

    <!-- Styles -->
    {!!  Html::style('/css/app.css') !!}
    {!!  Html::style('/css/custom.css') !!}

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top hidden-print">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        Bingham University
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        @if(request()->segment(1) == 'admin' && request()->segment(2) == 'login')
                            <li>
                                <a href="{{ route('getLogin') }}">
                                    <span class="glyphicon glyphicon-user"></span>
                                    Student Login
                                </a>
                            </li>
                        @elseif(request()->segment(1) == 'login')
                            <li>
                                <a href="{{ route('admin.get_login') }}">
                                    <span class="glyphicon glyphicon-user"></span>
                                    Staff Login
                                </a>
                            </li>
                        @endif
                        <!-- Student Authentication Links -->
                        @if (!Auth::guest())
                            <li class="{{ isset($current_nav) && $current_nav == 'dashboard' ? 'active' : '' }}">
                                <a href="{{ route('dashboard') }}">
                                    <span class="glyphicon glyphicon-home"></span>
                                    Dashboard
                                </a>
                            </li>
                            <li class="{{ isset($current_nav) && $current_nav == 'register_course' ? 'active' : '' }}">
                                <a href="{{ route('get.register') }}">
                                    <span class="glyphicon glyphicon-th-list"></span>
                                    Register Courses
                                </a>
                            </li>
                            <li class="{{ isset($current_nav) && $current_nav == 'print_form' ? 'active' : '' }}">
                                <a href="{{ route('get.print_course') }}">
                                    <span class="glyphicon glyphicon-print"></span>
                                    Printable Form
                                </a>
                            </li>
                            <li class="dropdown">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    <span class="glyphicon glyphicon-user"></span>
                                    {{ ucwords(strtolower(session('firstname'))) . ' ' . ucwords(strtolower(session('surname'))) . ' (' . Auth::user()->regno . ')' }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ url('/logout') }}"
                                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            <span class="glyphicon glyphicon-log-out"></span>
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        <!-- HOD: Admin Links -->
                        @if(session()->has('role') && session('role') == 'HOD')
                            <li class="{{ isset($current_nav) && $current_nav == 'dashboard' ? 'active' : '' }}">
                                <a href="{{ route('admin.dashboard') }}">
                                    <span class="glyphicon glyphicon-home"></span>
                                    Dashboard
                                </a>
                            </li>
                            <li class="{{ isset($current_nav) && $current_nav == 'manage_courses' ? 'active' : '' }}">
                                <a href="{{ route('admin.hod_manage_courses') }}">
                                    <span class="glyphicon glyphicon-th-list"></span>
                                    Course Management
                                </a>
                            </li>
                            <li class="{{ isset($current_nav) && $current_nav == 'manage_lecturers' ? 'active' : '' }}">
                                <a href="{{ route('admin.hod_manage_lecturers') }}">
                                    <span class="glyphicon glyphicon-book"></span>
                                    Lecturer Management
                                </a>
                            </li>
                            <li class="{{ isset($current_nav) && $current_nav == 'manage_adjustments' ? 'active' : '' }}">
                                {{--HOD's can only make 1 correction to a Course Result--}}
                                <a href="{{ route('admin.hod_manage_result_adjustments') }}">
                                    <span class="glyphicon glyphicon-edit"></span>
                                    Result Adjustment
                                </a>
                            </li>
                        @endif
                        <!-- Lecturer: Admin Links -->
                        @if(session()->has('role') && session('role') == 'Lecturer')
                            <li class="{{ isset($current_nav) && $current_nav == 'dashboard' ? 'active' : '' }}">
                                <a href="{{ route('admin.dashboard') }}">
                                    <span class="glyphicon glyphicon-home"></span>
                                    Dashboard
                                </a>
                            </li>
                            <li class="{{ isset($current_nav) && $current_nav == 'manage_results' ? 'active' : '' }}">
                                <a href="{{ route('admin.lecturer_manage_results') }}">
                                    <span class="glyphicon glyphicon-list-alt"></span>
                                    Manage Course Results
                                </a>
                            </li>
                        @endif
                        <!-- End Lecturer: Admin Links -->

                        <!-- Dean: Admin Links -->
                        @if(session()->has('role') && session('role') == 'Dean')
                            <li class="{{ isset($current_nav) && $current_nav == 'dashboard' ? 'active' : '' }}">
                                <a href="{{ route('admin.dashboard') }}">
                                    <span class="glyphicon glyphicon-home"></span>
                                    Dashboard
                                </a>
                            </li>
                        @endif
                        <!-- End Dean: Admin Links -->

                        <!-- Senate: Admin Links -->
                        @if(session()->has('role') && session('role') == 'Senate')
                            <li class="{{ isset($current_nav) && $current_nav == 'dashboard' ? 'active' : '' }}">
                                <a href="{{ route('admin.dashboard') }}">
                                    <span class="glyphicon glyphicon-home"></span>
                                    Dashboard
                                </a>
                            </li>
                            @endif
                        <!-- End Senate: Admin Links -->

                        @if(session('role') == 'HOD' or session('role') == 'Dean' or session('role') == 'Senate')
                            <li class="dropdown {{ isset($current_nav) && $current_nav == 'manage_reports' ? 'active' : '' }}">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    <span class="glyphicon glyphicon-stats"></span>
                                    Reports <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('admin.get_reports') }}">
                                            <span class="glyphicon glyphicon-check"></span>
                                            Result Approvals
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.get_detailed_reports') }}">
                                            <span class="glyphicon glyphicon-list"></span>
                                            Detailed Reports
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        @if(session('role') == 'Lecturer' or session('role') == 'HOD' or session('role') == 'Dean' or session('role') == 'Senate')
                            <li class="dropdown {{ isset($current_nav) && $current_nav == 'user_settings' ? 'active' : '' }}">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    <span class="glyphicon glyphicon-user"></span>
                                    {{ ucwords(strtolower(session('first_name'))) . ' ' . ucwords(strtolower(session('last_name'))) }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('admin.logout') }}">
                                            <span class="glyphicon glyphicon-log-out"></span>
                                            Logout
                                        </a>
                                        <a href="{{ route('admin.change_password') }}">
                                            <span class="glyphicon glyphicon-lock"></span>
                                            Change Password
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <!-- Scripts -->
    {{--<script src="/js/app.js"></script>--}}
    {!! Html::script('/js/jquery.min.js') !!}
    {!! Html::script('/js/bootstrap.min.js') !!}
    {!! Html::script('/js/custom.js') !!}
</body>
</html>
