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
                        <h3>グループ管理画面</h3>
                    </div>
                    <div class="col-md-6 ">
                        <br>
                        <p style="text-align: right">
                            <button type="submit" class="btn btn-primary " onclick="location.href='./group/add'">
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
                            <th><a href="/group?sort=id">#</a></th>
                            <th><a href="/group?sort=group_name">グループ名</a></th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td> <a href="/group/detail/{{$item->id}}">{{$item->group_name}}</a></td>
                                <td>
                                    <a href="/group/edit/{{$item->id}}" class="btn btn-primary btn-sm">編集</a>
                                </td>
                                <td>
                                    <form action="/group/delete/{{$item->id}}" method="POST"  onSubmit="return checkSubmit()">
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
