@section('title','admin')
@extends('master')
@section('noidung')

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Danh sách user</h1>
        </div>

        <style>
            .btn-xs {
                padding: 1px 5px;
                font-size: 12px;
                line-height: 1.5;
                border-radius: 3px;
            }

            .btn-sm {
                padding: 5px 10px;
                font-size: 12px;
                line-height: 1.5;
                border-radius: 3px;
            }
        </style>

        <!-- Content Row -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row" id="divbang">
                    @if(Session::has('mess_succ'))
                        <div class="alert alert-success col-md-12">
                            {{Session::get('mess_succ')}}
                        </div>
                    @endif
                    <div class="col-xs-12" style="margin-bottom: 20px">
                        <a href="{{route('get-themmoiuser')}}"><button class="btn btn-primary btn-sm">Thêm mới user</button></a>
                    </div>
                    <div class="col-xs-12 table-responsive contable">
                        <table class="table table-bordered" width="100%" cellspacing="0" id="dataTable">
                            <thead>
                            <tr>
                                <th>Tên hiển thị</th>
                                <th>Username</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($listUser as $key => $item)
                                <tr>
                                    <td>{{$item->name}}</td>
                                    <td>{{$item->username}}</td>
                                    <td>
                                        @if($item->username != 'admin')
                                            <a href="{{route('get-phanquyenuser',$item->id)}}">
                                                <button class="btn btn-primary btn-xs" style="margin-right: 10px">Phân
                                                    quyền
                                                </button>
                                            </a>

                                            <a href="{{route('get-resetpassword',$item->id)}}"
                                               onclick="return confirm('Bạn có muốn reset mật khẩu về: 123456  không?');">
                                                <button class="btn btn-primary btn-xs" style="margin-right: 10px">Reset
                                                    password
                                                </button>
                                            </a>

                                            <a href="{{route('get-xoauser',$item->id)}}"
                                               onclick="return confirm('Bạn có muốn xoá user không?');">
                                                <button class="btn btn-primary btn-xs">
                                                    Xoá
                                                </button>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div style="float: right">
                            {!!  $listUser->appends(request()->except(['page']))->links("pagination::bootstrap-4")  !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>
    </div>

@endsection