@section('title','admin')
@extends('master')
@section('noidung')

    <style>
        th {
            position: sticky;
            top: 0;
            background: #ccc;
        }

        .cltien {
            position: sticky;
            top: 30px;
        }

        .cltien2 {
            position: sticky;
            top: 61px;
        }
    </style>

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Thêm dữ liệu</h1>
        </div>

        <!-- Content Row -->
        <div class="card shadow mb-4">
            <div class="card-body">
                @if(isset($mess) && !empty($mess))
                    <div class="row" id="divsuccess">
                        <div class="col-md-12 alert alert-success">Thêm dữ liệu thành công!</div>
                    </div>
                @endif
                @if(Session::has('mess'))
                    <div class="row" id="divsuccess">
                        <div class="col-md-12 alert alert-success">{{Session::get('mess')}}</div>
                    </div>
                @endif
                <form action="{{route('post-themdulieu')}}" method="POST" id="filter-frm">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-md-3" style="height: 80px;">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Ngày</label>
                                <input type="date" class="form-control" id="date" name="date" value="{{date('Y-m-d')}}"
                                       onchange="changeAds()">
                            </div>
                        </div>
                        <div class="col-md-3" style="height: 80px;">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Game</label>
                                <select name="game" id="game" class="form-control chosen-select"
                                        onchange="changeGame()">
                                    <option value="0">--Chọn Game--</option>
                                    @foreach($games as $item)
                                        @if(in_array($item->gameid,$permission) || $permission[0] == 'admin')
                                            <option value="{{$item->gameid}}">{{$item->gamename}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" style="height: 80px;">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Adsnetwork</label>
                                <select name="adsnetwork" id="adsnetwork" class="form-control chosen-select"
                                        onchange="changeAds()">
                                    <option value="0">--Chọn Adsnetwork--</option>
                                    @foreach($adsnetwork as $item)
                                        <option value="{{$item->adsnetworkid}}">{{$item->adsnetworkshow}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <style>
                        tr, td {
                            text-align: center;
                        }
                    </style>
                    <div class="row" id="divbang">

                    </div>

                    <div class="row" id="divdoi" style="margin-bottom: 20px;display: none">
                        <div class="col-md-12 text-center">
                            <code>Hệ thống đang xử lý. Vui lòng đợi...</code>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-xs-12 col-md-12" id="divload">

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-primary" onclick="clickXacNhan()">Xác nhận</button>
                        </div>
                    </div>
                </form>
            </div>
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

        $('.chosen-select').chosen();

        $(function () {
            setTimeout(function () {
                $('#divsuccess').hide();
            }, 3000);
        });

        function changeGame() {
            var gameid = $('#game').val();
            $('#divbang').empty();
            if (gameid != 0) {
                $('#divbang').html('<div class="loader"></div>');

                $.ajax({
                    url: "{{route('get-bangthemdulieu')}}",
                    //async: false,
                    dataType: "text",
                    data: {gameid: gameid},
                    type: "GET",
                    success: function (data) {
                        $('#divbang').html(data);

                        changeAds();
                    },
                    error: function () {
                    }
                });
            }
        }

        function changeAds() {
            var gameid = $('#game').val();
            var adsnetwork = $('#adsnetwork').val();
            var date = $('#date').val();

            if (adsnetwork != 0 && gameid != 0 && date != '') {
                $('.clearinput').val('');
                $.ajax({
                    url: "{{route('get-bangthemdulieu-thongsoads')}}",
                    //async: false,
                    dataType: "json",
                    data: {gameid: gameid, adsnetwork: adsnetwork, date: date},
                    type: "GET",
                    success: function (data) {
                        $.each(data, function (key, value) {
                            $('#budget' + key).val(value['budget']);
                            $('#costvnd' + key).val(value['cost']);
                            $('#costusd' + key).val(value['costusd']);
                            $('#cpitarget' + key).val(value['cpitarget']);
                            $('#ctr' + key).val(value['ctr']);
                            $('#cr' + key).val(value['cr']);
                            $('#install' + key).val(value['install']);
                            $('#revenuevnd' + key).val(value['revenue']);
                            $('#revenueusd' + key).val(value['revenueusd']);
                        });

                        tinhsum();
                    },
                    error: function () {
                    }
                });
            }
        }

        function tinhsum() {
            var arrcountry = JSON.parse($('#arrcountry').val());
            var sumcostusd = 0;
            var sumcostvnd = 0;
            var sumrevenueusd = 0;
            var sumrevenuevnd = 0;
            $.each(arrcountry, function (key, value) {
                var costusd = rmcomma($('#costusd' + value).val()) * 1;
                var costvnd = rmcomma($('#costvnd' + value).val()) * 1;
                var revenueusd = rmcomma($('#revenueusd' + value).val()) * 1;
                var revenuevnd = rmcomma($('#revenuevnd' + value).val()) * 1;

                sumcostusd += costusd;
                sumcostvnd += costvnd;
                sumrevenueusd += revenueusd;
                sumrevenuevnd += revenuevnd;
            });

            if (sumcostusd == 0){
                $('#sumcostusd').html(0);
            } else{
                sumcostusd = Math.round(sumcostusd * 100) / 100;
                $('#sumcostusd').html(addcomma(sumcostusd));
            }

            if (sumcostvnd == 0){
                $('#sumcostvnd').html(0);
            } else{
                $('#sumcostvnd').html(addcomma(sumcostvnd));
            }

            if (sumrevenueusd == 0){
                $('#sumrevenueusd').html(0);
            } else{
                sumrevenueusd = Math.round(sumrevenueusd * 100) / 100;
                $('#sumrevenueusd').html(addcomma(sumrevenueusd));
            }

            if (sumrevenuevnd == 0){
                $('#sumrevenuevnd').html(0);
            } else{
                $('#sumrevenuevnd').html(addcomma(sumrevenuevnd));
            }
        }

        function changeCostUsd(code) {
            var costusd = $('#costusd' + code).val();
            var costvnd = costusd * {{config('tygia.cost')}};
            if (costvnd == 0 || costvnd == '') {
                $('#costvnd' + code).val(costvnd);
            } else {
                costvnd = Math.round(costvnd);
                $('#costvnd' + code).val(addcomma(costvnd));
            }

            tinhsum();
        }

        function changeCostVnd(ele, code) {
            $('#costusd' + code).val('');
            var costvnd = rmcomma($('#costvnd' + code).val());
            var costusd = costvnd / {{config('tygia.cost')}};
            if (costusd == 0 || costusd == '') {
                $('#costusd' + code).val(costusd);
            } else {
                costusd = Math.round(costusd * 100) / 100;
                $('#costusd' + code).val(addcomma(costusd));
            }

            addcomma1(ele);

            tinhsum();
        }

        function changeRevenueUsd(code) {
            var revenueusd = $('#revenueusd' + code).val();
            var revenuevnd = revenueusd * {{config('tygia.revenue')}};
            if (revenuevnd == 0 || revenuevnd == '') {
                $('#revenuevnd' + code).val(revenuevnd);
            } else {
                revenuevnd = Math.round(revenuevnd);
                $('#revenuevnd' + code).val(addcomma(revenuevnd));
            }

            tinhsum();
        }

        function changeRevenueVnd(ele, code) {
            $('#revenueusd' + code).val('');
            var revenuevnd = rmcomma($('#revenuevnd' + code).val());
            var revenueusd = revenuevnd / {{config('tygia.revenue')}};
            if (revenueusd == 0 || revenueusd == '') {
                $('#revenueusd' + code).val(revenueusd);
            } else {
                revenueusd = Math.round(revenueusd * 100) / 100;
                $('#revenueusd' + code).val(addcomma(revenueusd));
            }

            addcomma1(ele);

            tinhsum();
        }

        function clickXacNhan() {

            if ($('#date').val() == '') {
                makeAlertright("Vui lòng chọn Ngày", 2000);
                return;
            }

            if ($('#game').val() == 0) {
                makeAlertright("Vui lòng chọn Game", 2000);
                return;
            }

            if ($('#adsnetwork').val() == 0) {
                makeAlertright("Vui lòng chọn Adsnetwork", 2000);
                return;
            }

            $('.btn').attr('disabled', 'disabled');
            $('#divdoi').show();
            $('#divload').html("<div class='loader'></div>");

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

        function addcomma(x) {
            if (x != null && x != '')
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            else
                return null;
        }

        function rmcomma(x) {
            if (x != null && x != '')
                return x.replace(/,/g, "");
            else
                return null;
        }

        function addcomma1(ele) {
            var x = rmcomma(ele.value);
            if (x != null && x != '')
                ele.value = x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

    </script>

@endsection