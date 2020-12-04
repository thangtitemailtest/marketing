@section('title','admin')
@extends('master')
@section('noidung')

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Thêm mới user</h1>
        </div>

        <!-- Content Row -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('post-themmoiuser') }}" method="POST" class="mb-5" id="filter-frm">
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
                                       value="">
                            </div>
                        </div>
                            <div class="col-sm-3" style="height:85px">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Tên đăng nhập</label>
                                <input class="form-control" type="text" id="username" name="username"
                                       value="">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Mật khẩu</label>
                                <input class="form-control" type="password" id="matkhau" name="matkhau">
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

@endsection