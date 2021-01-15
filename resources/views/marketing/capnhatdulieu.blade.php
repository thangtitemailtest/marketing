@section('title','admin')
@extends('master')
@section('noidung')

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Cập nhật dữ liệu</h1>
        </div>

        <!-- Content Row -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row">
                    @if(Session::has('mess'))
                        <div class="col-xs-12 col-md-12 alert alert-success" style="text-align: center">
                            {{Session::get('mess')}}
                        </div>
                    @endif
                    <div class="col-xs-3 col-md-2">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">Cập nhật dữ
                            liệu
                        </button>
                    </div>
                    <div class="col-sm-3" style="height:85px">
                        <div class="form-group input-group-sm">
                            <label class="radio-inline mr-3">Ngày
                            </label>
                            <input type="date" name="ngay" class="form-control" id="ngay" onchange="changeNgay()"
                                   value="{{date('Y-m-d', strtotime(date('Y-m-d') . " -1 day"))}}">
                        </div>
                    </div>
                    <div class="col-md-3" style="height: 80px;">
                        <div class="form-group input-group-sm">
                            <label class="radio-inline mr-3">Adsnetwork &nbsp; &nbsp; </label>
                            <select name="adsnetworkid" id="adsnetworkid" class="form-control chosen-select"
                                    onchange="changeAds()">
                                <option value="0">--Chọn Adsnetwork--</option>
                                <option value="ironsource">Ironsource</option>
                                <option value="adwords">GoogleAds</option>
                                <option value="unity">Unity</option>
                                <option value="searchads">SeachAds</option>
                                <option value="applovin">AppLovin</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3" id="divchontaikhoan" style="height: 80px; display: none;">
                        <div class="form-group input-group-sm">
                            <label class="radio-inline mr-3">Tài khoản &nbsp; &nbsp; </label>
                            <select name="taikhoan" id="taikhoan" class="form-control chosen-select"
                                    onchange="changeTK()">
                                <option value="0">--Chọn Tài khoản--</option>
                                <option value="256-247-7293">1: 256-247-7293</option>
                                <option value="184-236-4088">2: 184-236-4088</option>
                                <option value="761-776-7486">3: 761-776-7486</option>
                                <option value="501-116-6276">4: 501-116-6276</option>
                                <option value="877-912-8370">5: 877-912-8370</option>
                                <option value="484-840-6491">6: 484-840-6491</option>
                                <option value="766-066-7300">7: 766-066-7300</option>
                                <option value="741-406-3586">8: 741-406-3586</option>
                                <option value="510-000-4823">9: 510-000-4823</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-9 col-md-9" id="thongbao" style="display: none">
                        <code>Dữ liệu đang được cập nhật. Quá trình cập nhật có thể mất vài phút. Vui lòng đợi và
                            không
                            tắt
                            trang này.</code>
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

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cập nhật dữ liệu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Cập nhật dữ liệu có thể mất vài phút. Bạn có muốn cập nhật?
                </div>
                <div class="modal-footer">
                    <form action="{{ route('get-capnhatdulieu-submit') }}" method="GET" id="filter-frm">
                        <input type="hidden" name="capnhatdulieu" id="capnhatdulieu" value="1">
                        <input type="hidden" name="date" id="date"
                               value="{{date('Y-m-d', strtotime(date('Y-m-d') . " -1 day"))}}">
                        <input type="hidden" name="adsnetwork" id="adsnetwork"
                               value="">
                        <input type="hidden" name="taikhoandachon" id="taikhoandachon"
                               value="">
                        <button type="button" class="btn btn-primary" onclick="clickCapnhat()" id="btnsubmit"
                                style="margin-right: 10px">Cập nhật
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Page level plugins -->
    <script>

        function changeNgay() {
            var ngay = $('#ngay').val();
            $('#date').val(ngay);
        }

        function changeAds() {
            var adsnetworkid = $('#adsnetworkid').val();
            $('#adsnetwork').val(adsnetworkid);

            if (adsnetworkid == 'adwords') {
                $('#divchontaikhoan').show();
            } else {
                $('#divchontaikhoan').hide();
            }
        }

        function changeTK() {
            var taikhoan = $('#taikhoan').val();
            $('#taikhoandachon').val(taikhoan);
        }

        function clickCapnhat() {
            event.preventDefault();

            var conf = confirm('Bạn có muốn cập nhật không?');
            if (conf) {

                if ($('#date').val() == '') {
                    makeAlertright('Vui lòng chọn Ngày.', 3000);
                    return;
                }

                if ($('#adsnetwork').val() == '') {
                    makeAlertright('Vui lòng chọn Adsnetwork.', 3000);
                    return;
                } else {
                    if ($('#adsnetwork').val() == 'adwords') {
                        if ($('#taikhoandachon').val() == '') {
                            makeAlertright('Vui lòng chọn Tài khoản.', 3000);
                            return;
                        }
                    }
                }

                $('#exampleModal').modal('hide');
                $('#thongbao').show();

                $('.btn').attr('disabled', 'disabled');
                $('#divload').html("<div class='loader'></div>");

                $('#filter-frm').submit();
            }
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