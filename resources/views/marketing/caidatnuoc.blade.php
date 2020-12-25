@section('title','admin')
@extends('master')
@section('noidung')

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Cài đặt quốc gia</h1>
        </div>

        <!-- Content Row -->
        <div class="card shadow mb-4">
            <form action="{{route('post-caidatnuoc')}}" method="post" id="filter-frm">
                <div class="card-body">
                    @if(Session::has('mess'))
                        <div class="row">
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
                                        @if(in_array($item->gameid,$permission) || $permission[0] == 'admin')
                                            <option value="{{$item->gameid}}">{{$item->gamename}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" style="height: auto;">
                            <div class="form-group input-group-sm">
                                <label class="radio-inline mr-3">Country</label>
                                <select name="country[]" id="country" multiple class="form-control chosen-select">
                                    {{--<option value="0">--Chọn Country--</option>--}}
                                    @foreach($country as $item)
                                        <option value="{{$item->code}}">{{$item->name}} ({{$item->code}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" style="height: 80px;padding-top: 30px;">
                            <button type="button" class="btn btn-primary btn-sm" onclick="clickThem()">Thêm</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="divbang">

                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12" style="text-align: center">
                            <button type="button" class="btn btn-primary" onclick="clickXacNhan()">Xác nhận</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-md-12" id="divload">

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

        $('.chosen-select').chosen();

        function changeGame() {
            var gameid = $('#gameid').val();
            if (gameid == 0) {
                $("#divbang").empty();
            } else {
                $("#divbang").html('<div class="loader"></div>');

                $.ajax({
                    url: "{{route('get-countrygame')}}",
                    //async: false,
                    dataType: "json",
                    data: {gameid: gameid},
                    type: "GET",
                    success: function (data) {
                        $('#json').val(JSON.stringify(data));
                        vebang();
                    },
                    error: function () {
                    }
                });
            }
        }

        function clickThem() {
            var gameid = $('#gameid').val();
            if (gameid == 0) {
                makeAlertright('Vui lòng chọn Game!', 2000);
                return;
            }

            var country = $('#country').val();
            if (country == '' || country == null) {
                makeAlertright('Vui lòng chọn Country!', 2000);
                return;
            }
            //var country_text = $('#country option:selected').text();
            var json = JSON.parse($("#json").val());
            var check = false;
            var nuocthemroi = '';
            $.each(json, function (key, value) {
                $('#country :selected').each(function () {
                    if ($(this).val() == value.code) {
                        check = true;
                        nuocthemroi += $(this).text() + ", ";
                    }
                });
            });

            if (check == true) {
                makeAlertright("Nước " + nuocthemroi + " đã được thêm rồi!", 3000);
                return;
            }

            $('#country :selected').each(function () {
                json.push({
                    'code': $(this).val(),
                    'name': $(this).text(),
                });
            });

            $('#json').val(JSON.stringify(json));

            vebang();
        }

        function vebang() {
            var json = JSON.parse($("#json").val());
            var $table = $('<table class="table table-bordered table-hover" width="100%" cellspacing="0"></table>');
            var $thead = $('<thead></thead>');
            var $tr = $('<tr></tr>');
            $tr.append($('<th>Nước</th>'));
            $tr.append($('<th>Xoá</th>'));
            $thead.append($tr);
            $table.append($thead);

            var $tbody = $('<tbody></tbody>');

            /*$.each(json, function (key, value) {
                var $tr = $('<tr></tr>');
                $tr.append('<td>' + value.name + '</td>');
                $tr.append('<td><button class="btn btn-danger btn-sm" onclick="clickXoa(' + key + ')"><i class="fas fa-times"></i></button></td>');
                $tbody.append($tr);
            });*/

            for (var i = json.length - 1; i >= 0; i--) {
                var emp = json[i];
                var $tr = $('<tr></tr>');
                $('#country :selected').each(function () {
                    if ($(this).val() == emp.code) {
                        $tr = $('<tr class="newline"></tr>');
                    }
                });
                $tr.append('<td>' + emp.name + '</td>');
                $tr.append('<td><button class="btn btn-danger btn-sm" onclick="clickXoa(' + i + ')"><i class="fas fa-times"></i></button></td>');
                $tbody.append($tr);
            }

            $table.append($tbody);

            $('#divbang').html($table);

            $('.newline').css("background-color", "yellow");
            setTimeout(function () {
                $('.newline').css("background-color", "white");
            }, 300);

            $('#country').val('').trigger('chosen:updated');
        }

        function clickXoa(i) {
            var json = JSON.parse($("#json").val());
            json.splice(i, 1);
            $('#json').val(JSON.stringify(json));

            vebang();
        }

        function clickXacNhan() {
            var gameid = $('#gameid').val();
            if (gameid == 0) {
                makeAlertright('Vui lòng chọn Game!', 2000);
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