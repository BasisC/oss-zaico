@extends('layouts.app_before_login')

@section('content')
    <script>
        @if (session('success_message'))
        $(function () {
            toastr.success('{{ session('success_message') }}');
        });
        @endif
        @if (session('error_message'))
        $(function () {
            toastr.error('{{ session('error_message') }}');
        });
        @endif
    </script>
<div class="container">
    <div class="row">
        <div class="col-md-11 ">
            <div class="panel panel-default">
                    <div class="col-md-6 ">
                        <h3>部署管理画面</h3>
                    </div>
                    <div class="col-md-6 ">
                        <br>
                        <p style="text-align: right">
                            <button type="submit" class="btn btn-primary " onclick="location.href='./department/add'">
                                登録する>>
                            </button>
                        </p>
                    </div>
                <hr>
                <hr>
                <hr>

                <hr>


                <div class="table-responsive">
                    <div class="col-md-12 ">
                    <table class="table table-striped table-sm">
                        <thead>
                        <tr>
                            <th><a href="/department?sort=id">#</a></th>
                            <th><a href="/department?sort=department_name">部署名</a></th>


                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>
                                    {{$item->department_name}}　
                                   <!--! <a href="/department/detail/{{$item->id}}">ユーザを所属させる </a>/
                                    <a href="/department/warehouse_detail/{{$item->id}}">倉庫を操作対象にする </a>-->
                                </td>
                                <td>
                                    <a href="/department/detail/{{$item->id}}" class="btn btn-primary btn-sm">ユーザ所属</a>
                                </td>
                                <td>
                                    <a href="/department/target_list/{{$item->id}}" class="btn btn-primary btn-sm">操作対象</a>
                                </td>
                                <td>
                                    <a href="/department/edit/{{$item->id}}" class="btn btn-primary btn-sm">編集</a>
                                </td>
                                <td>
                                    <form action="/department/delete/{{$item->id}}" method="POST"  onSubmit="return checkSubmit()">
                                        {{ csrf_field() }}
                                        <input type="submit" value="削除" class="btn btn-standard btn-sm btn-dell" >
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                        {{$items->appends(['sort'=>$sort])->links()}}
                </div>
                </div>
            </div>
        </div>

    </div>
</div>
    <script>
        function checkSubmit() {
            return confirm("削除してもよろしいですか？");
        }
    </script>

@endsection
