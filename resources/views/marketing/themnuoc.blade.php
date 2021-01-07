@section('title','admin')
@extends('master')
@section('noidung')

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Thêm quốc gia</h1>
        </div>

        <!-- Content Row -->
        <div class="card shadow mb-4">
            <div class="card-body">
                @if(Session::has('mess'))
                    <div class="row" id="divsuccess">
                        <div class="col-md-12 alert alert-success">{{Session::get('mess')}}</div>
                    </div>
                @endif
                <form action="{{route('post-themnuoc')}}" method="post" id="filter-frm">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-md-3" style="height: 80px;">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Tên nước</label>
                                <input type="text" name="tennuoc" id="tennuoc" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3" style="height: auto;">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Country code</label>
                                <input type="text" name="countrycode" id="countrycode" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3" style="padding-top: 30px;">
                            <button type="button" class="btn btn-primary btn-sm" onclick="clickXacNhan()">Xác nhận
                            </button>
                        </div>
                    </div>
                </form>
                <hr>
                <div class="row">
                    <div class="col-md-12" id="divbang">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0" cellpadding="0">
                            <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên nước</th>
                                <th>Country code</th>
                                <th>Xoá</th>
                            </tr>
                            </thead>
                            <tbody>
							<?php
							$dem = 0;
							?>
                            @foreach($country as $item)
								<?php
								$dem++;
								?>
                                <tr>
                                    <td>{{$dem}}</td>
                                    <td>{{$item->name}}</td>
                                    <td>{{$item->code}}</td>
                                    <td><a href="{{route('get-xoanuoc',$item->id)}}" onclick="return confirm('Bạn có muốn xoá quốc gia này không?')"><button type="button" class="btn btn-danger btn-xs" style="padding: 0px 10px"><i class="fa fa-times"></i></button></a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>
    </div>

    <!-- Page level plugins -->
    <script>

        $(function () {
            setTimeout(function () {
                $('#divsuccess').hide();
            }, 3000);
        });

        function clickXacNhan() {
            var tennuoc = $('#tennuoc').val();
            var countrycode = $('#countrycode').val();
            if (tennuoc == '') {
                makeAlertright('Vui lòng nhập Tên nước!', 2000);
                return;
            }

            if (countrycode == '') {
                makeAlertright('Vui lòng nhập Country code!', 2000);
                return;
            }

            $('#filter-frm').submit();
        }

        function makeAlertright(msg, duration) {
            var el = document.createElement("div");
            el.setAttribute("style", "position:fixed;bottom:2%;right:2%; width:25%;z-index:999999");
            el.innerHTML = '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="fa fa-times"></i> <strong>Error!! </strong>' + msg + '</div>';
            setTimeout(function () {
                el.parentNode.removeChild(el);
            }, duration);
            document.body.appendChild(el);
        }

    </script>

@endsection