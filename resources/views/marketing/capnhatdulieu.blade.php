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
                    @if(isset($_GET['capnhatdulieu']))
                        <div class="col-xs-12 col-md-12 alert alert-success" style="text-align: center">
                            Dữ liệu đã được cập nhật.
                        </div>
                    @else
                        <div class="col-xs-3 col-md-3">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">Cập nhật dữ
                                liệu
                            </button>
                        </div>
                        <div class="col-xs-9 col-md-9" id="thongbao" style="display: none">
                            <code>Dữ liệu đang được cập nhật. Quá trình cập nhật có thể mất 10p. Vui lòng đợi và không
                                tắt
                                trang này.</code>
                        </div>
                    @endif
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
                    Cập nhật dữ liệu có thể mất 10p. Bạn có muốn cập nhật?
                </div>
                <div class="modal-footer">
                    <form action="{{ route('get-capnhatdulieu') }}" method="GET" id="filter-frm">
                        <input type="hidden" name="capnhatdulieu" id="capnhatdulieu" value="1">
                        <button type="button" class="btn btn-primary" onclick="clickCapnhat()" id="btnsubmit" style="margin-right: 10px">Cập nhật
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Page level plugins -->
    <script>

        function clickCapnhat() {
            event.preventDefault();

            $('#exampleModal').modal('hide');
            $('#thongbao').show();

            $('.btn').attr('disabled', 'disabled');
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

    </script>

@endsection