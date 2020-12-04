@section('title','admin')
@extends('master')
@section('noidung')

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
                                <input type="date" class="form-control" id="date" name="date" value="{{date('Y-m-d')}}">
                            </div>
                        </div>
                        <div class="col-md-3" style="height: 80px;">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Game</label>
                                <select name="game" id="game" class="form-control chosen-select">
                                    <option value="0">--Chọn Game--</option>
                                    @foreach($games as $item)
                                        <option value="{{$item->gameid}}">{{$item->gamename}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" style="height: 80px;">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Adsnetwork</label>
                                <select name="adsnetwork" id="adsnetwork" class="form-control chosen-select">
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
                    <div class="row">
                        <div class="col-md-12 table-responsive">
                            <table class="table table-bordered table-striped" width="100%" cellspacing="0"
                                   id="dataTable">
                                <thead>
                                <tr>
                                    <th rowspan="2">Country</th>
                                    <th rowspan="2">Budget</th>
                                    <th colspan="2">Cost</th>
                                    <th rowspan="2">CPI target</th>
                                    <th rowspan="2">CTR</th>
                                    <th rowspan="2">CR</th>
                                    <th rowspan="2">Install</th>
                                    <th colspan="2">Revenue</th>
                                </tr>
                                <tr>
                                    <th>USD</th>
                                    <th>VND</th>
                                    <th>USD</th>
                                    <th>VND</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($country))
                                    @foreach($country as $key => $item)
                                        <tr>
                                            <input type="hidden" name="countrycode{{$item->code}}"
                                                   id="countrycode{{$item->code}}" value="{{$item->code}}">
                                            <td>{{$item->name}} ({{$item->code}})</td>
                                            <td><input type="number" class="form-control" name="budget{{$item->code}}"
                                                       id="budget{{$item->code}}">
                                            </td>
                                            <td><input type="number" class="form-control" name="costusd{{$item->code}}"
                                                       id="costusd{{$item->code}}"
                                                       onkeyup="changeCostUsd('{{$item->code}}')">
                                            </td>
                                            <td><input type="text" class="form-control" name="costvnd{{$item->code}}"
                                                       onkeyup="changeCostVnd(this,'{{$item->code}}')"
                                                       id="costvnd{{$item->code}}">
                                            </td>
                                            <td><input type="number" class="form-control"
                                                       name="cpitarget{{$item->code}}" id="cpitarget{{$item->code}}">
                                            </td>
                                            <td><input type="number" class="form-control" name="ctr{{$item->code}}"
                                                       id="ctr{{$item->code}}">
                                            </td>
                                            <td><input type="number" class="form-control" name="cr{{$item->code}}"
                                                       id="cr{{$item->code}}"></td>
                                            <td><input type="number" class="form-control" name="install{{$item->code}}"
                                                       id="install{{$item->code}}">
                                            </td>
                                            <td><input type="number" class="form-control"
                                                       name="revenueusd{{$item->code}}" id="revenueusd{{$item->code}}"
                                                       onkeyup="changeRevenueUsd('{{$item->code}}')"></td>
                                            <td><input type="text" class="form-control"
                                                       onkeyup="changeRevenueVnd(this,'{{$item->code}}')"
                                                       name="revenuevnd{{$item->code}}" id="revenuevnd{{$item->code}}">
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
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

        function changeCostUsd(code) {
            var costusd = $('#costusd' + code).val();
            var costvnd = costusd * {{config('tygia.cost')}};
            if (costvnd == 0 || costvnd == '') {
                $('#costvnd' + code).val(costvnd);
            } else {
                costvnd = Math.round(costvnd);
                $('#costvnd' + code).val(addcomma(costvnd));
            }
        }

        function changeCostVnd(ele, code) {
            addcomma1(ele);
            $('#costusd' + code).val('');
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
        }

        function changeRevenueVnd(ele, code) {
            addcomma1(ele);
            $('#revenueusd' + code).val('');
        }

        function clickXacNhan() {

            if ($('#date').val() == ''){
                makeAlertright("Vui lòng chọn Ngày",2000);
                return;
            }

            if ($('#game').val() == 0){
                makeAlertright("Vui lòng chọn Game",2000);
                return;
            }

            if ($('#adsnetwork').val() == 0){
                makeAlertright("Vui lòng chọn Adsnetwork",2000);
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