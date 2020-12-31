<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    {{--<title>@yield('title')</title>--}}
    <title>Marketing</title>

    <link rel="icon" href="{{asset('img/favicon.ico')}}" type="image/x-icon">

    <!-- Custom fonts for this template-->
    <link href="{{asset('vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
          rel="stylesheet">

    <script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{asset('vendor/jquery-easing/jquery.easing.min.js')}}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{asset('/js/sb-admin-2.min.js')}}"></script>

    <!-- Custom styles for this template-->
    <link href="{{asset('/css/sb-admin-2.min.css')}}" rel="stylesheet">
    <style>
        .loader {
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid #3498db;
            width: 50px;
            height: 50px;
            margin: auto;
            -webkit-animation: spin .5s linear infinite; /* Safari */
            animation: spin .5s linear infinite;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <style>
        .collapse-item {
            padding-left: 5px !important;
        }

        body, .table, .table-hover tbody tr:hover {
            color: #000 !important;
        }
    </style>
</head>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('get-index')}}">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-plane-departure"></i>
            </div>
            <div class="sidebar-brand-text mx-3">Marketing</div>
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item active">
            <a class="nav-link" href="{{route('get-index')}}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>

        @if(Auth::check() && $permission[0] == 'admin')
            <li class="nav-item">
                <a class="nav-link" href="{{route('get-danhsachuser')}}">
                    <i class="fas fa-male"></i>
                    <span>Danh sách user</span></a>
            </li>
        @endif

        @if(Auth::check() && $permission[0] == 'admin')
            <li class="nav-item">
                <a class="nav-link" href="{{route('get-capnhatdulieu')}}">
                    <i class="fas fa-folder"></i>
                    <span>Cập nhật dữ liệu</span></a>
            </li>
        @endif

        @if(Auth::check() && $permission[0] == 'admin')
            <li class="nav-item">
                <a class="nav-link" href="{{route('get-settinggetrevenue')}}">
                    <i class="fas fa-folder"></i>
                    <span>Cài đặt lấy doanh thu</span></a>
            </li>
        @endif

        @if(in_array('edit',$permission) || $permission[0] == 'admin')
            <li class="nav-item">
                <a class="nav-link" href="{{route('get-themdulieu')}}">
                    <i class="fas fa-plus"></i>
                    <span>Thêm dữ liệu</span></a>
            </li>
        @endif

        <li class="nav-item">
            <a class="nav-link" href="{{route('get-thongkedulieutheoquocgia')}}">
                <i class="fas fa-globe"></i>
                <span>Thống kê dữ liệu theo<br>quốc gia</span></a>
        </li>


        @if(in_array('edit',$permission) || $permission[0] == 'admin')
            <li class="nav-item">
                <a class="nav-link" href="{{route('get-caidatnuoc')}}">
                    <i class="fas fa-globe"></i>
                    <span>Cài đặt quốc gia</span></a>
            </li>
        @endif

        <hr class="sidebar-divider d-none d-md-block">

    </ul>

    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <!-- Sidebar Toggle (Topbar) -->
                <button id="ToggleNav" class="btn btn-link rounded-circle mr-3" onclick="clickToggle()">
                    <i class="fa fa-bars"></i>
                </button>

                <script>
                    function clickToggle() {
                        $('#accordionSidebar').toggle(500);
                    }
                </script>

                <!-- Topbar Search -->


                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                    <li class="nav-item dropdown no-arrow d-sm-none">
                        <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-search fa-fw"></i>
                        </a>
                        <!-- Dropdown - Messages -->
                        <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                             aria-labelledby="searchDropdown">
                            <form class="form-inline mr-auto w-100 navbar-search">
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light border-0 small"
                                           placeholder="Search for..." aria-label="Search"
                                           aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button">
                                            <i class="fas fa-search fa-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </li>


                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">

                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">Xin chào: {{Auth::check() ? Auth::user()->name : ''}}</span>

                            <i class="fas fa-user"></i>
                        </a>

                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="{{route('get-thaydoithongtin')}}">
                                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                Thay đổi thông tin
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{route('get-logout')}}">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Đăng xuất
                            </a>
                        </div>
                    </li>

                </ul>

            </nav>


            @yield('noidung')

        </div>
    </div>
</div>
</body>
</html>