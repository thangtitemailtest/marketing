@section('title','admin')
@extends('master')
@section('noidung')

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Danh sách module</h1>
        </div>

        <style>
            .btn-xs {
                padding: 1px 5px;
                font-size: 12px;
                line-height: 1.5;
                border-radius: 3px;
            }

            .btn-sm {
                padding: 5px 10px;
                font-size: 12px;
                line-height: 1.5;
                border-radius: 3px;
            }
        </style>

        <!-- Content Row -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row" id="divbang">
                    @if(Session::has('mess_succ'))
                        <div class="alert alert-success col-md-12">
                            {{Session::get('mess_succ')}}
                        </div>
                    @endif
                    <form action="{{ route('post-phanquyenuser') }}" method="POST" class="mb-5" id="filter-frm"
                          style="width: 100%">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" id="id" value="{{$id}}">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0" id="dataTable">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>url</th>
                                    <th>Name</th>
                                    <th>Action<br><input type="checkbox" id="checkall" onclick="clickCheckall()"></th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($listModule as $key => $item)
                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td>{{$item->url}}</td>
                                        <td>{{$item->name}}</td>
                                        <td style="text-align: center">
                                            <input type="checkbox" name="checkname[]" class="checkall"
                                                   {{in_array($item->url,$permission) ? "checked" : ""}} value="{{$item->url}}">
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-xs-12" style="margin-top: 20px;text-align: center">
                            <button type="submit" class="btn btn-sm btn-primary">Phân quyền</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>
    </div>

     <script>
        function clickCheckall() {
            if ($('#checkall').is(':checked')) {
                $('.checkall').prop('checked', true);
            } else {
                $('.checkall').prop('checked', false);
            }
        }
    </script>

@endsection