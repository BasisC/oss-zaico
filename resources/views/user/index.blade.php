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
                        <h3>ユーザ管理画面</h3>
                    </div>
                    <div class="col-md-6 ">
                        <br>
                        <p style="text-align: right">
                            <button type="submit" class="btn btn-primary " onclick="location.href='./register'">
                                登録する>>
                            </button>
                        </p>
                    </div>
                <hr>
                <hr>
                <hr>

                <hr>

                <form class="form-horizontal" method="POST" action="/user">
                    {{ csrf_field() }}

                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-1 control-label">Name:</label>

                        <div class="col-md-4">
                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}">
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email" class="col-md-1 control-label">Email:</label>

                        <div class="col-md-4">
                            <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                検索>>
                            </button>
                        </div>
                    </div>
                </form>


                <hr>

                <div class="table-responsive">
                    <div class="col-md-12 ">
                    <table class="table table-striped table-sm">
                        <thead>
                        <tr>
                            <th><a href="/user?sort=id">#</a></th>
                            <th><a href="/user?sort=name">ユーザ名</a></th>
                            <th><a href="/user?sort=type">ユーザ権限</a></th>
                            <th><a href="/user?sort=email">メールアドレス</a></th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->name}}</td>
                                <td>{{$item->typeName($item->type)}}</td>
                                <td>{{$item->email}}</td>
                                <td>
                                    <a href="/user/edit/{{$item->id}}" class="btn btn-primary btn-sm">編集</a>
                                </td>
                                <td>
                                    <form action="/user/delete/{{$item->id}}" method="POST"  onSubmit="return checkSubmit()">
                                        {{ csrf_field() }}
                                        <input type="submit" value="削除" class="btn btn-standard btn-sm btn-dell" >
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                        {{$items->appends(['sort'=>$sort,'name'=>$name,'email'=>$email])->links()}}
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
