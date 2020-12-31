@section('title','admin')
@extends('master')
@section('noidung')

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Cài đặt lấy doanh thu</h1>
        </div>

        <!-- Content Row -->
        <div class="card shadow mb-4">
            <form action="{{route('post-settinggetrevenue')}}" method="post" id="filter-frm">
                <div class="card-body">
                    @if(Session::has('mess'))
                        <div class="row" id="divsuccess">
                            <div class="col-md-12 alert alert-success">{{Session::get('mess')}}</div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-3" style="height: 80px;">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Game</label>
                                <select name="gameid" id="gameid" class="form-control chosen-select"
                                        onchange="changeGame()">
                                    <option value="0">--Chọn Game--</option>
                                    @foreach($games as $item)
                                        <option value="{{$item->gameid}}">{{$item->gamename}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3" style="height: 80px;">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Kênh</label>
                                <select name="kenh" id="kenh" class="form-control chosen-select"
                                        onchange="changeGame()">
                                    <option value="0">--Chọn kênh--</option>
                                    <option value="ironsource">Ironsource</option>
                                    <option value="applovin">Applovin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="divbang">

                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12" style="text-align: center">
                            <button type="button" class="btn btn-primary" onclick="clickXacNhan()">Xác nhận</button>
                        </div>
                    </div>
                </div>
                {{csrf_field()}}
                <input type="hidden" name="json" id="json" value='[]'>
            </form>
        </div>

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>
    </div>


    <link rel="stylesheet" href="{{asset('chosen/chosen.min.css')}}">
    <script type="text/javascript" src="{{asset('chosen/chosen.jquery.min.js')}}"></script>

    <!-- Page level plugins -->
    <script>

        $(function () {
            setTimeout(function () {
                $('#divsuccess').hide();
            }, 3000);
        });

        $('.chosen-select').chosen();

        function changeGame() {
            var gameid = $('#gameid').val();
            var kenh = $('#kenh').val();
            if (gameid == 0 || kenh == 0) {
                $("#divbang").empty();
            } else {
                $("#divbang").html('<div class="loader"></div>');

                $.ajax({
                    url: "{{route('get-bangsettinggetrevenue')}}",
                    //async: false,
                    dataType: "text",
                    data: {gameid: gameid, kenh: kenh},
                    type: "GET",
                    success: function (data) {
                        $('#divbang').html(data);
                    },
                    error: function () {
                    }
                });
            }
        }

        function clickXacNhan() {
            var gameid = $('#gameid').val();
            var kenh = $('#kenh').val();
            if (gameid == 0) {
                makeAlertright('Vui lòng chọn Game!', 2000);
                return;
            }

            if (kenh == 0) {
                makeAlertright('Vui lòng chọn Kênh!', 2000);
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