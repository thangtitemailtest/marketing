@section('title','admin')
@extends('master')
@section('noidung')

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Thay đổi thông tin</h1>
        </div>

        <!-- Content Row -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('post-thaydoithongtin') }}" method="POST" class="mb-5" id="filter-frm">
                    {{ csrf_field() }}
                    <div class="row">
                        @if(count($errors)>0)
                            <div class="alert alert-danger col-md-12">
                                @foreach($errors->all() as $err)
                                    {{$err.'. '}}
                                @endforeach
                            </div>
                        @endif
                        @if(Session::has('mess_error'))
                            <div class="alert alert-danger col-md-12">
                                {{Session::get('mess_error')}}
                            </div>
                        @endif
                        @if(Session::has('mess_succ'))
                            <div class="alert alert-success col-md-12">
                                {{Session::get('mess_succ')}}
                            </div>
                        @endif
                        <div class="col-sm-3" style="height:85px">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Tên hiển thị</label>
                                <input class="form-control" type="text" id="name" name="name"
                                       value="{{Auth::user()->name}}">
                            </div>
                        </div>
                        <div class="col-sm-2" style="height:85px">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">
                                    <input type="checkbox" name="checkdoimatkhau" id="checkdoimatkhau" value="check"
                                           onchange="changeCheckbox()">&nbsp;Đổi mật khẩu
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-3 cl_doimatkhau" style="height:85px;display: none">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Mật khẩu cũ</label>
                                <input class="form-control" type="password" id="matkhaucu" name="matkhaucu">
                            </div>
                        </div>
                        <div class="col-sm-3 cl_doimatkhau" style="height:85px;display: none">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Mật khẩu mới</label>
                                <input class="form-control" type="password" id="matkhaumoi" name="matkhaumoi">
                            </div>
                        </div>
                    </div>
                    <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                        <input type="submit" class="btn btn-primary" id="btnsubmit"
                               value="Xác nhận">
                    </div>
                </form>
            </div>
        </div>

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>
    </div>

    <script>

        function changeCheckbox() {
            if ($('#checkdoimatkhau').is(':checked')) {
                $('.cl_doimatkhau').css('display', 'block');
            } else {
                $('.cl_doimatkhau').css('display', 'none');
            }
        }

    </script>

@endsection