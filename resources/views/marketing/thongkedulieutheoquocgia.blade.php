@section('title','admin')
@extends('master')
@section('noidung')

    <style>
        tr, td {
            text-align: center;
        }

        .tab-content .loader {
            position: relative;
            left: 400%;
        }
    </style>

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Thống kê dữ liệu theo quốc gia</h1>
        </div>

        <!-- Content Row -->
        <div class="card shadow mb-4">
            <div class="card-body">
                @if(Session::has('mess'))
                    <div class="row" id="divsuccess">
                        <div class="col-md-12 alert alert-success">{{Session::get('mess')}}</div>
                    </div>
                @endif
                <form action="{{route('get-thongkedulieutheoquocgia')}}" method="GET" id="filter-frm">
                    <div class="row">
                        <div class="col-md-3" style="height: 80px;">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Tháng</label>
                                <input type="month" class="form-control" id="month" name="month"
                                       value="{{isset($_GET['month']) && !empty($_GET['month']) ? $_GET['month'] : date('Y-m')}}">
                            </div>
                        </div>
                        <div class="col-md-3" style="height: 80px;padding-top: 30px;">
                            <button type="button" class="btn btn-primary" onclick="clickXem()">Xem</button>
                        </div>
                    </div>
                </form>

                <div class="row" id="divdoi" style="margin-bottom: 10px;display: none;">
                    <div class="col-md-12 text-center">
                        <code>Hệ thống đang xử lý. Vui lòng đợi...</code>
                    </div>
                </div>

                <div class="row" id="divload"
                     style="{{(isset($_GET['month']) && !empty($_GET['month'])) ? "" : "display: none"}}">
                    <div class="col-md-12">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab"
                                   href="#taboverall"
                                   role="tab" onclick="clickOverall()"
                                   aria-selected="true">Overall</a>
                                <a class="nav-item nav-link" id="nav-home-tab" data-toggle="tab"
                                   href="#tabsummary"
                                   role="tab" onclick="clickSummary()"
                                   aria-selected="true">Summary</a>
                                @foreach($country as $item)
                                    <a class="nav-item nav-link" id="nav-home-tab" data-toggle="tab"
                                       href="#tab{{$item->code}}"
                                       role="tab"
                                       aria-selected="true">{{$item->name}}</a>
                                @endforeach
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="taboverall" role="tabpanel">
                                <input type="hidden" name="showoverall" id="showoverall" value="0">
                                <div class="row">
                                    <div class="col-md-12 text-right" style="margin: 10px 0">
                                        <button type="button" class="btn btn-primary btn-sm"
                                                onclick="clickOverall('tailai')">Tải lại dữ liệu
                                        </button>
                                    </div>
                                    <div class="col-md-12 table-responsive">
                                        <table class="table table-bordered table-hover" width="100%"
                                               cellspacing="0"
                                               id="dataTable">
                                            <thead>
                                            <tr>
                                                <th>STT</th>
                                                <th>Country</th>
                                                <th>Install</th>
                                                <th>Cost</th>
                                                <th>Revenue</th>
                                                <th>Performance</th>
                                                <th>Profit Rate</th>
                                            </tr>
                                            </thead>
                                            <tbody id="divloadoverall">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tabsummary" role="tabpanel">
                                <input type="hidden" name="showsummary" id="showsummary" value="0">
                                <div class="row">
                                    <div class="col-md-12 text-right" style="margin: 10px 0">
                                        <button type="button" class="btn btn-primary btn-sm"
                                                onclick="clickSummary('tailai')">Tải lại dữ liệu
                                        </button>
                                    </div>
                                    <div id="divloadsummary"></div>
                                </div>
                            </div>
                            @foreach($country as $item)
                                <div class="tab-pane fade" id="tab{{$item->code}}" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12 table-responsive">
                                            <table class="table table-bordered table-striped" width="100%"
                                                   cellspacing="0"
                                                   id="dataTable">
                                                <thead>
                                                <tr>
                                                    <th colspan="{{9 + $count_adsnetwork * 2}}">{{$item->name}}</th>
                                                </tr>
                                                <tr>
                                                    <th rowspan="2">Date</th>
                                                    <th colspan="{{$count_adsnetwork}}">Budget</th>
                                                    <th rowspan="2">Cost</th>
                                                    <th colspan="{{$count_adsnetwork}}">CPI target</th>
                                                    <th rowspan="2">CPI (Avg)</th>
                                                    <th rowspan="2">CTR</th>
                                                    <th rowspan="2">CR</th>
                                                    <th rowspan="2">Install</th>
                                                    <th rowspan="2">Revenue</th>
                                                    <th rowspan="2">Performance</th>
                                                    <th rowspan="2">Profit rate</th>
                                                </tr>
                                                <tr>
                                                    @for($i = 1; $i <= 2; $i++)
                                                        @foreach($adsnetwork as $item)
                                                            <th>{{$item->adsnetworkshow}}</th>
                                                        @endforeach
                                                    @endfor
                                                </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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

    <link rel="stylesheet" href="{{asset('chosen/chosen.min.css')}}">
    <script type="text/javascript" src="{{asset('chosen/chosen.jquery.min.js')}}"></script>

    <!-- Page level plugins -->
    <script>

        $('.chosen-select').chosen();

                @if(isset($_GET['month']) && !empty($_GET['month']))
        var month = '{{$_GET['month']}}';
        clickOverall();
                @else
        var month = '';
        @endif

        $(function () {
            $('#accordionSidebar').hide(500);
        });

        function clickOverall(check = '') {
            var showoverall = $('#showoverall').val();
            if (showoverall == 0 || check == 'tailai') {
                $('#divloadoverall').html("<div class='loader'></div>");

                $.ajax({
                    url: "{{route('get-overall')}}",
                    //async: false,
                    dataType: "json",
                    data: {month: month},
                    type: "GET",
                    success: function (data) {
                        $('#divloadoverall').html('');
                        var dem = 0;
                        $.each(data, function (key, value) {
                            dem++;
                            var performance = value["revenue"] - value["cost"];
                            if (value['cost'] == 0 || value['cost'] == '' || value['cost'] == null) {
                                var profit = 0;
                            } else {
                                var profit = performance / value['cost'] * 100;
                                profit = Math.round(profit * 100) / 100;
                            }
                            var $tr = $('<tr></tr>');
                            $tr.append('<td>' + dem + '</td>');
                            $tr.append('<td>' + value["name"] + '</td>');
                            $tr.append('<td>' + value["install"] + '</td>');
                            $tr.append('<td>' + value["cost"] + '</td>');
                            $tr.append('<td>' + value["revenue"] + '</td>');
                            $tr.append('<td>' + performance + '</td>');
                            $tr.append('<td>' + profit + ' %</td>');

                            $('#divloadoverall').append($tr);
                        });

                        $('#showoverall').val(1);
                    },
                    error: function () {
                    }
                });
            }
        }

        function clickSummary(check = '') {
            var showsummary = $('#showsummary').val();
            if (showsummary == 0 || check == 'tailai') {
                $('#divloadsummary').html("<div class='loader'></div>");

                $.ajax({
                    url: "{{route('get-summary')}}",
                    //async: false,
                    dataType: "text",
                    data: {month: month},
                    type: "GET",
                    success: function (data) {
                        $('#divloadsummary').html(data);

                        $('#showsummary').val(1);
                    },
                    error: function () {
                    }
                });
            }
        }

        function clickXem() {

            if ($('#month').val() == '') {
                makeAlertright("Vui lòng chọn Tháng", 2000);
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