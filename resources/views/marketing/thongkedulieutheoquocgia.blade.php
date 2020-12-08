@section('title','admin')
@extends('master')
@section('noidung')

    <style>
        th, td {
            text-align: center;
            padding: 5px !important;
        }

        #taboverall .loader {
            position: relative;
            left: 400%;
        }

        .cltr {
            background-color: rgba(0, 0, 0, .05);
        }

        .btn-xs {
            padding: 1px 5px;
            font-size: 12px;
            line-height: 1.5;
            border-radius: 3px;
        }
    </style>
    <style>
        .contable {
            width: 100%;
            margin: 0px auto 0 auto;
            overflow: auto;
            min-height: 50%;

        }

        #divbang td, #divbang th {
            padding: 5px !important;
        }

        .overtit {
            overflow-y: auto !important;
        }

        .webkit-scrollbar::-webkit-scrollbar,
        .webkit-scrollbar + #floating-scrollbar::-webkit-scrollbar {
            height: 12px;
        }

        ::-webkit-scrollbar-button:start, ::-webkit-scrollbar-button:end {
            display: none;
        }

        ::-webkit-scrollbar-track-piece, ::-webkit-scrollbar-thumb {
            -webkit-border-radius: 8px;
        }

        ::-webkit-scrollbar-track-piece {
            background-color: #444;
        }

        ::-webkit-scrollbar-thumb:horizontal {
            width: 50px;
            background-color: #777;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: #aaa;
        }


    </style>

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Thống kê dữ liệu theo quốc gia</h1>
        </div>

        <!-- Content Row -->
        <div class="card shadow mb-4">
            <div class="card-body" style="min-height: 500px;">
                <form action="{{route('get-thongkedulieutheoquocgia')}}" method="GET" id="filter-frm">
                    <div class="row">
                        <div class="col-md-3" style="height: 80px;">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Game &nbsp; &nbsp; </label>
                                <select name="game" id="game" class="form-control chosen-select">
                                    <option value="0">--Chọn Game--</option>
                                    @foreach($game as $item)
                                        <option value="{{$item->gameid}}" {{isset($_GET['game']) && $_GET['game'] == $item->gameid ? "selected" : ""}}>{{$item->gamename}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3" style="height:85px">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">
                                    <input type="radio" name="time" id="time-2" onchange="changeRadio('month')"
                                           {{(isset($_GET['time']) && $_GET['time'] == 'month') || empty($_GET['time']) ? 'checked' : ''}}
                                           value="month">&nbsp;Theo tháng
                                </label>
                                <input type="month" name="month" class="form-control" id="month"
                                       value="{{isset($_GET['month']) && !empty($_GET['month']) ? $_GET['month'] : date('Y-m')}}">
                            </div>
                        </div>
                        <div class="col-sm-3" style="height:85px">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">
                                    <input type="radio" name="time" id="time-1" onchange="changeRadio('week')"
                                           value="week" {{isset($_GET['time']) && $_GET['time'] == 'week' ? 'checked' : ''}}>&nbsp;Theo
                                    tuần
                                </label>
                                <input type="week" name="week" class="form-control" id="week"
                                       value="{{!empty($_GET['week']) ? $_GET['week'] : ''}}">
                            </div>
                        </div>
                        <div class="col-sm-3" style="height:85px;">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">
                                    <input type="radio" name="time" id="time-3" onchange="changeRadio('tuychon')"
                                           {{isset($_GET['time']) && $_GET['time'] == 'tuychon' ? 'checked' : ''}}
                                           value="tuychon">&nbsp;Tuỳ chọn
                                </label>
                                <input type="date" name="from-date" class="form-control" id="from-date" title="Từ ngày"
                                       value="{{!empty($_GET['from-date']) ? $_GET['from-date'] : ''}}">
                                <input type="date" name="to-date" class="form-control" id="to-date" title="Đến ngày"
                                       value="{{!empty($_GET['to-date']) ? $_GET['to-date'] : ''}}">
                            </div>
                        </div>
                        <div class="col-md-3" style="height: 80px;">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Adsnetowrk &nbsp; &nbsp; </label>
                                <select name="adsnetworkid" id="adsnetworkid" class="form-control chosen-select">
                                    <option value="0">--Chọn Adsnetwork--</option>
                                    @foreach($adsnetwork as $item)
                                        <option value="{{$item->adsnetworkid}}" {{isset($_GET['adsnetworkid']) && $_GET['adsnetworkid'] == $item->adsnetworkid ? "selected" : ""}}>{{$item->adsnetworkshow}}</option>
                                    @endforeach
                                </select>
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

                <div class="row" id="divload"></div>

                <div class="row" id="divload2"
                     style="{{(isset($_GET['game']) && !empty($_GET['game'])) ? "" : "display: none"}}">
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
                                       role="tab" onclick="clickCountry('{{$item->code}}')"
                                       aria-selected="true">{{$item->name}}</a>
                                @endforeach
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="taboverall" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 text-right" style="margin: 10px 0">
                                        <button type="button" class="btn btn-primary btn-sm"
                                                onclick="clickOverall('tailai')">Tải lại dữ liệu
                                        </button>
                                    </div>
                                    <div class="col-md-12 table-responsive">
                                        <table class="table table-bordered table-hover" width="100%"
                                               cellspacing="0">
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
                                <div class="row">
                                    <div class="col-md-12 text-right" style="margin: 10px 0">
                                        <button type="button" class="btn btn-primary btn-sm"
                                                onclick="clickSummary('tailai')">Tải lại dữ liệu
                                        </button>
                                    </div>
                                    <div id="divloadsummary" class="col-md-12"></div>
                                </div>
                            </div>
                            @foreach($country as $item)
                                <div class="tab-pane fade" id="tab{{$item->code}}" role="tabpanel">
                                    <input type="hidden" name="showcountry{{$item->code}}"
                                           id="showcountry{{$item->code}}" value="0">
                                    <div class="row">
                                        <div class="col-md-12 text-right" style="margin: 10px 0">
                                            <button type="button" class="btn btn-primary btn-sm"
                                                    onclick="clickCountry('{{$item->code}}','tailai')">Tải lại dữ liệu
                                            </button>
                                        </div>
                                        <div id="divloadcountry{{$item->code}}" class="col-md-12"></div>
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

    <link rel="stylesheet" href="{{asset('floating_scroll/floatingscroll.css')}}">
    <script src="{{asset('floating_scroll/floatingscroll.min.js')}}"></script>

    <script src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

    <!-- Page level plugins -->
    <script>

        $('#accordionSidebar').hide();
        var showoverall = 0;
        var showsummary = 0;

        $('.chosen-select').chosen();

                @if(isset($_GET['game']) && !empty($_GET['game']))
        var game = '{{$_GET['game']}}';
        var time = '{{isset($_GET['time']) ? $_GET['time'] : ''}}';
        var month = '{{isset($_GET['month']) ? $_GET['month'] : ''}}';
        var week = '{{isset($_GET['week']) ? $_GET['week'] : ''}}';
        var fromdate = '{{isset($_GET['from-date']) ? $_GET['from-date'] : ''}}';
        var todate = '{{isset($_GET['to-date']) ? $_GET['to-date'] : ''}}';
        clickOverall();
                @else
        var game = '';
        var time = '';
        var month = '';
        var week = '';
        var fromdate = '';
        var todate = '';
        @endif

        $(function () {
            changeRadio('{{empty($_GET['time']) ? 'month' : $_GET['time']}}');
        });

        function changeRadio(time) {
            if (time == 'week') {
                $('#week').removeAttr('disabled');

                $('#month').attr('disabled', 'disabled');
                $('#from-date').attr('disabled', 'disabled');
                $('#to-date').attr('disabled', 'disabled');
            } else if (time == 'month') {
                $('#month').removeAttr('disabled');

                $('#week').attr('disabled', 'disabled');
                $('#from-date').attr('disabled', 'disabled');
                $('#to-date').attr('disabled', 'disabled');
            } else if (time == 'tuychon') {
                $('#month').attr('disabled', 'disabled');
                $('#week').attr('disabled', 'disabled');

                $('#from-date').removeAttr('disabled');
                $('#to-date').removeAttr('disabled');
            }
        }

        function clickOverall(check = '') {
            if (showoverall == 0 || check == 'tailai') {
                $('#divloadoverall').html("<div class='loader'></div>");

                $.ajax({
                    url: "{{route('get-overall')}}",
                    //async: false,
                    dataType: "json",
                    data: {month: month, game: game, time: time, week: week, fromdate: fromdate, todate: todate},
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

                        showoverall = 1;
                    },
                    error: function () {
                    }
                });
            }
        }

        function clickSummary(check = '') {
            if (showsummary == 0 || check == 'tailai') {
                $('#divloadsummary').html("<div class='loader'></div>");

                $.ajax({
                    url: "{{route('get-summary')}}",
                    //async: false,
                    dataType: "text",
                    data: {month: month, game: game, time: time, week: week, fromdate: fromdate, todate: todate},
                    type: "GET",
                    success: function (data) {
                        $('#divloadsummary').html(data);

                        $('#datatable_summary').DataTable({
                            //"order": [[0, "asc"]],
                            "order": false,
                            "pageLength": 10,
                            "bInfo": false,
                            "bLengthChange": false,
                            "searching": false
                        });

                        showsummary = 1;
                    },
                    error: function () {
                    }
                });
            }
        }

        function clickXemMonth(month, game) {
            $('#divsummary_month').html("<div class='loader'></div>");
            $.ajax({
                url: "{{route('get-summary-month')}}",
                //async: false,
                dataType: "text",
                data: {month: month, game: game},
                type: "GET",
                success: function (data) {
                    $('#divsummary_month').html(data);
                },
                error: function () {
                }
            });
        }

        function clickCountry(countrycode, check = '') {
            var showcountry = $('#showcountry' + countrycode).val();
            if (showcountry == 0 || check == 'tailai') {
                $('#divloadcountry' + countrycode).html("<div class='loader'></div>");
                var adsnetworkid = $('#adsnetworkid').val();

                $.ajax({
                    url: "{{route('get-country')}}",
                    //async: false,
                    dataType: "text",
                    data: {
                        month: month,
                        game: game,
                        time: time,
                        week: week,
                        fromdate: fromdate,
                        todate: todate,
                        countrycode: countrycode,
                        adsnetworkid: adsnetworkid
                    },
                    type: "GET",
                    success: function (data) {
                        $('#divloadcountry' + countrycode).html(data);

                        $(".contable").floatingScroll();

                        $('#showcountry' + countrycode).val(1);
                    },
                    error: function () {
                    }
                });
            }
        }

        function clickXem() {

            if ($('#game').val() == 0) {
                makeAlertright("Vui lòng chọn Game", 2000);
                return;
            }

            if ($('#time-1').is(':checked') === true && $('#week').val() == '') {
                makeAlertright('Vui lòng chọn Tuần.', 3000);
                return;
            }

            if ($('#time-2').is(':checked') === true && $('#month').val() == '') {
                makeAlertright('Vui lòng chọn Tháng.', 3000);
                return;
            }

            if ($('#time-3').is(':checked') === true && ($('#from-date').val() == '' || $('#to-date').val() == '')) {
                makeAlertright('Vui lòng chọn Từ ngày/Đến ngày.', 3000);
                return;
            }

            if ($('#adsnetworkid').val() == 0) {
                makeAlertright("Vui lòng chọn Adsnetwork", 2000);
                return;
            }

            $('.btn').attr('disabled', 'disabled');
            $('#divdoi').show();
            $('#divload2').hide();
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