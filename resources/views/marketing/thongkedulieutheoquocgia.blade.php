@section('title','admin')
@extends('master')
@section('noidung')

    <style>
        th, td {
            text-align: center;
            padding: 5px !important;
        }

        #divloadoverall .loader {
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

        #table_overall_filter {
            float: right;
        }

        #table_overall_length label {
            display: inline;
        }

        #table_overall_length label select {
            width: 100px;
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
            <h1 class="h3 mb-0 text-gray-800">Thống kê dữ liệu</h1>
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
                                        @if(in_array($item->gameid,$permission) || $permission[0] == 'admin')
                                            <option value="{{$item->gameid}}" {{isset($_GET['game']) && $_GET['game'] == $item->gameid ? "selected" : ""}}>{{$item->gamename}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" style="height: 80px;">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Adsnetwork &nbsp; &nbsp; </label>
                                <select name="adsnetworkid" id="adsnetworkid" class="form-control chosen-select">
                                    <option value="0">--Chọn Adsnetwork--</option>
                                    @foreach($adsnetwork as $item)
                                        <option value="{{$item->adsnetworkid}}" {{isset($_GET['adsnetworkid']) && $_GET['adsnetworkid'] == $item->adsnetworkid ? "selected" : ""}}>{{$item->adsnetworkshow}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3" style="height:85px">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">
                                    <input type="radio" name="time" id="time-0" onchange="changeRadio('ngay')"
                                           {{(isset($_GET['time']) && $_GET['time'] == 'ngay') || empty($_GET['time']) ? 'checked' : ''}}
                                           value="ngay">&nbsp;Theo ngày
                                </label>
                                <input type="date" name="ngay" class="form-control" id="ngay"
                                       value="{{isset($_GET['ngay']) && !empty($_GET['ngay']) ? $_GET['ngay'] : date('Y-m-d', strtotime(date('Y-m-d') . " -1 day"))}}">
                            </div>
                        </div>
                        <div class="col-sm-3" style="height:85px">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">
                                    <input type="radio" name="time" id="time-2" onchange="changeRadio('month')"
                                           {{isset($_GET['time']) && $_GET['time'] == 'month' ? 'checked' : ''}}
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
                        <div class="col-sm-3" style="height:auto;">
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
                                <a class="nav-item nav-link" id="nav-home-tab" data-toggle="tab"
                                   href="#tabtongrevenuetheokenh"
                                   role="tab" onclick="clickTongrevenuetheokenh()"
                                   aria-selected="true">Tổng doanh thu theo Adsnetwork</a>
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
                                        <table class="table table-bordered table-hover" width="100%" id="table_overall"
                                               cellspacing="0">
                                            <thead>
                                            <tr>
                                                <th>STT</th>
                                                <th>Country</th>
                                                <th>Install</th>
                                                <th>Cost</th>
                                                <th>CPI</th>
                                                <th>Revenue</th>
                                                <th>Performance</th>
                                                <th>Profit Rate</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="divloadoverall">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12  table-responsive" id="divloadoverallcountry"></div>
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
                            <div class="tab-pane fade" id="tabtongrevenuetheokenh" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 text-right" style="margin: 10px 0">
                                        <button type="button" class="btn btn-primary btn-sm"
                                                onclick="clickTongrevenuetheokenh('tailai')">Tải lại dữ liệu
                                        </button>
                                    </div>
                                    <div id="divloadtongrevenuetheokenh" class="col-md-12"></div>
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

                <div class="row" id="divload3"
                     style="{{(isset($_GET['game']) && empty($_GET['game'])) ? "" : "display: none"}}">
                    <div class="col-md-12 table-responsive" id="divbang3" style="margin-top: 20px"></div>
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
        var showtongrevenuetheokenh = 0;

        $('.chosen-select').chosen();

                @if(isset($_GET['game']))
                @if(!empty($_GET['game']))
        var game = '{{$_GET['game']}}';
        var time = '{{isset($_GET['time']) ? $_GET['time'] : ''}}';
        var ngay = '{{isset($_GET['ngay']) ? $_GET['ngay'] : ''}}';
        var month = '{{isset($_GET['month']) ? $_GET['month'] : ''}}';
        var week = '{{isset($_GET['week']) ? $_GET['week'] : ''}}';
        var fromdate = '{{isset($_GET['from-date']) ? $_GET['from-date'] : ''}}';
        var todate = '{{isset($_GET['to-date']) ? $_GET['to-date'] : ''}}';
        clickOverall();
                @else
        var game = '{{$_GET['game']}}';
        var time = '{{isset($_GET['time']) ? $_GET['time'] : ''}}';
        var ngay = '{{isset($_GET['ngay']) ? $_GET['ngay'] : ''}}';
        var month = '{{isset($_GET['month']) ? $_GET['month'] : ''}}';
        var week = '{{isset($_GET['week']) ? $_GET['week'] : ''}}';
        var fromdate = '{{isset($_GET['from-date']) ? $_GET['from-date'] : ''}}';
        var todate = '{{isset($_GET['to-date']) ? $_GET['to-date'] : ''}}';
        ThongKeGame();
        @endif
        @endif

        $(function () {
            changeRadio('{{empty($_GET['time']) ? 'ngay' : $_GET['time']}}');
        });

        function changeRadio(time) {
            if (time == 'week') {
                $('#week').removeAttr('disabled');

                $('#ngay').attr('disabled', 'disabled');
                $('#month').attr('disabled', 'disabled');
                $('#from-date').attr('disabled', 'disabled');
                $('#to-date').attr('disabled', 'disabled');
            } else if (time == 'ngay') {
                $('#ngay').removeAttr('disabled');

                $('#month').attr('disabled', 'disabled');
                $('#week').attr('disabled', 'disabled');
                $('#from-date').attr('disabled', 'disabled');
                $('#to-date').attr('disabled', 'disabled');
            } else if (time == 'month') {
                $('#month').removeAttr('disabled');

                $('#ngay').attr('disabled', 'disabled');
                $('#week').attr('disabled', 'disabled');
                $('#from-date').attr('disabled', 'disabled');
                $('#to-date').attr('disabled', 'disabled');
            } else if (time == 'tuychon') {
                $('#month').attr('disabled', 'disabled');
                $('#week').attr('disabled', 'disabled');
                $('#ngay').attr('disabled', 'disabled');

                $('#from-date').removeAttr('disabled');
                $('#to-date').removeAttr('disabled');
            }
        }

        function clickOverall(check = '') {
            if (check == 'tailai'){
                location.reload();
            }
            if (showoverall == 0) {
                $('#divloadoverall').html("<div class='loader'></div>");

                $.ajax({
                    url: "{{route('get-overall')}}",
                    //async: false,
                    dataType: "json",
                    data: {
                        month: month,
                        game: game,
                        time: time,
                        week: week,
                        fromdate: fromdate,
                        todate: todate,
                        ngay: ngay
                    },
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
                            var cost_hien = 0;
                            var revenue_hien = 0;
                            if (value["cost"] > 0) {
                                cost_hien = addcomma(value["cost"]);
                            }
                            if (value["revenue"] > 0) {
                                revenue_hien = addcomma(value["revenue"]);
                            }
                            var performance_hien = performance;
                            if (performance < 0) {
                                performance_hien = addcomma(performance * -1);
                                performance_hien = "-" + performance_hien;
                            }
                            if (performance > 0) {
                                performance_hien = addcomma(performance);
                            }

                            var cpi = 0;
                            if (value["install"] > 0) {
                                cpi = value["cost"] / value["install"];
                            }
                            var cpi_hien = cpi;
                            if (cpi > 0) {
                                cpi_hien = addcomma(Math.round(cpi));
                            }
                            var $tr = $('<tr></tr>');
                            $tr.append('<td>' + dem + '</td>');
                            $tr.append('<td>' + value["name"] + '</td>');
                            $tr.append('<td>' + value["install"] + '</td>');
                            $tr.append('<td>' + cost_hien + '</td>');
                            $tr.append('<td>' + cpi_hien + '</td>');
                            $tr.append('<td>' + revenue_hien + '</td>');
                            $tr.append('<td>' + performance_hien + '</td>');
                            $tr.append('<td>' + profit + ' %</td>');
                            $tr.append('<td><button type="button" class="btn btn-primary btn-xs" onclick="clickXemCountry(\'' + key + '\',\'' + value["name"] + '\')">Xem</button></td>');

                            $('#divloadoverall').append($tr);
                        });

                        $('#table_overall').DataTable({
                            "pageLength": 30,
                            "bInfo": false,
                            "lengthMenu": [[30, 50, -1], [30, 50, "All"]]
                        });

                        showoverall = 1;
                    },
                    error: function () {
                    }
                });
            }
        }

        function clickXemCountry(countrycode, countryname) {
            $('#divloadoverallcountry').html("<div class='loader'></div>");
            $("html, body").animate({scrollTop: $('#divloadoverallcountry').offset().top}, 1000);
            $.ajax({
                url: "{{route('get-overall-country')}}",
                //async: false,
                dataType: "text",
                data: {
                    month: month,
                    game: game,
                    time: time,
                    week: week,
                    fromdate: fromdate,
                    todate: todate,
                    ngay: ngay,
                    countrycode: countrycode,
                    countryname: countryname
                },
                type: "GET",
                success: function (data) {
                    $('#divloadoverallcountry').html(data);
                },
                error: function () {
                }
            });
        }

        function clickSummary(check = '') {
            if (showsummary == 0 || check == 'tailai') {
                $('#divloadsummary').html("<div class='loader'></div>");

                $.ajax({
                    url: "{{route('get-summary')}}",
                    //async: false,
                    dataType: "text",
                    data: {
                        month: month,
                        game: game,
                        time: time,
                        week: week,
                        fromdate: fromdate,
                        todate: todate,
                        ngay: ngay
                    },
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

        function clickTongrevenuetheokenh(check = '') {
            if (showtongrevenuetheokenh == 0 || check == 'tailai') {
                $('#divloadtongrevenuetheokenh').html("<div class='loader'></div>");

                $.ajax({
                    url: "{{route('get-tongrevenuetheokenh')}}",
                    //async: false,
                    dataType: "text",
                    data: {
                        month: month,
                        game: game,
                        time: time,
                        week: week,
                        fromdate: fromdate,
                        todate: todate,
                        ngay: ngay
                    },
                    type: "GET",
                    success: function (data) {
                        $('#divloadtongrevenuetheokenh').html(data);

                        showtongrevenuetheokenh = 1;
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
                var adsnetworkid = $('#adsnetworkid').val();
                /*if (adsnetworkid == 0) {
                    makeAlertright("Vui lòng chọn Adsnetwork và Tải lại dữ liệu", 3000);
                    return;
                }*/
                $('#divloadcountry' + countrycode).html("<div class='loader'></div>");

                $.ajax({
                    url: "{{route('get-country')}}",
                    //async: false,
                    dataType: "text",
                    data: {
                        ngay: ngay,
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

        function ThongKeGame() {
            $('#divbang3').html("<div class='loader'></div>");
            $.ajax({
                url: "{{route('get-thongkegame')}}",
                //async: false,
                dataType: "text",
                data: {
                    ngay: ngay,
                    month: month,
                    time: time,
                    week: week,
                    fromdate: fromdate,
                    todate: todate
                },
                type: "GET",
                success: function (data) {
                    $('#divbang3').html(data);
                },
                error: function () {
                }
            });
        }

        function clickXem() {

            //if ($('#game').val() != 0) {
            /* makeAlertright("Vui lòng chọn Game", 2000);
             return;
         }else {*/

            if ($('#time-1').is(':checked') === true && $('#week').val() == '') {
                makeAlertright('Vui lòng chọn Tuần.', 3000);
                return;
            }

            if ($('#time-0').is(':checked') === true && $('#ngay').val() == '') {
                makeAlertright('Vui lòng chọn Ngày.', 3000);
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
            //}

            /*if ($('#adsnetworkid').val() == 0) {
                makeAlertright("Vui lòng chọn Adsnetwork", 2000);
                return;
            }*/

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