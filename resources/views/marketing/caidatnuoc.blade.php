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
            <div class="card-body">
                @if(!empty($mess))
                    <div class="row">
                        <div class="col-md-12 alert alert-success">{{$mess}}</div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-3" style="height: 80px;">
                        <div class="form-group input-group-sm">
                            <label class="radio-inline mr-3">Country</label>
                            <select name="country" id="country" class="form-control chosen-select">
                                <option value="0">--Chọn Country--</option>
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
        </div>

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>
    </div>

    <form action="{{route('get-caidatnuoc')}}" method="GET" id="filter-frm">
        <input type="hidden" name="json" id="json" value='{{$json}}'>
    </form>

    <link rel="stylesheet" href="{{asset('chosen/chosen.min.css')}}">
    <script type="text/javascript" src="{{asset('chosen/chosen.jquery.min.js')}}"></script>

    <!-- Page level plugins -->
    <script>

        $('.chosen-select').chosen();

        $(function () {
            vebang();
        });

        function clickThem() {
            var country = $('#country').val();
            var country_text = $('#country option:selected').text();
            var json = JSON.parse($("#json").val());
            var check = false;
            $.each(json, function (key, value) {
                if (value.code == country) {
                    check = true;
                }
            });

            if (check == true) {
                makeAlertright("Nước này đã được thêm rồi!", 2000);
                return;
            }

            json.push({
                'code': country,
                'name': country_text,
            });
            $('#json').val(JSON.stringify(json));

            $('#country').val(0).trigger('chosen:updated');

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

            $.each(json, function (key, value) {
                var $tr = $('<tr></tr>');
                $tr.append('<td>' + value.name + '</td>');
                $tr.append('<td><button class="btn btn-danger btn-sm" onclick="clickXoa(' + key + ')"><i class="fas fa-times"></i></button></td>');
                $tbody.append($tr);
            });

            $table.append($tbody);

            $('#divbang').html($table);
        }

        function clickXoa(i) {
            var json = JSON.parse($("#json").val());
            json.splice(i, 1);
            $('#json').val(JSON.stringify(json));

            vebang();
        }

        function clickXacNhan() {
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