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
                        <h3>機器管理画面</h3>
                    </div>
                    <div class="col-md-6 ">
                        <br>
                        <p style="text-align: right">
                            <button type="submit" class="btn btn-primary " onclick="location.href='/stock/table'">
                                テーブル追加>>
                            </button>
                        </p>
                    </div>
                <hr>
                <hr>
                <hr>

                <hr>

                <form name="formName" method="post" action="/stock/">
                    {{ csrf_field() }}
                    <div class="form-group{{ $errors->has('classification_name') ? ' has-error' : '' }}">
                        <label for="classification_name" class="col-md-4 control-label text-right">所属部署：</label>

                        <div class="col-md-6">

                            <select name = "department_id" onChange="functionName()" class="form-control">
                                <option value = "null">(部署を選択してください)</option>
                                @foreach($belong_depart as $belong)
                                    <option value={{$belong->department_id}}>{{$belong->department_name}}</option>;
                                @endforeach
                            </select>
                            @if ($errors->has('classification_name'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('classification_name') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>


                    <div class="form-group{{ $errors->has('classification_name') ? ' has-error' : '' }}">
                        <label for="classification_name" class="col-md-4 control-label"></label>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">
                                表示する>>
                            </button>
                        </div>
                    </div>



                </form>
                <br>
                <br>
                <br>
                <br>
                <hr>

                <div class="table-responsive">
                    <div class="col-md-12 ">
                        <table class="table table-striped table-sm">
                            <thead>
                            <tr>
                                <th>倉庫名</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($targets as $target)
                                <tr>
                                    <td>{{$target->warehouse_name}}</td>
                                    <td><a href="/stock/table/create/{{$target->warehouse_id}}">テーブルを作成する</a></td>
                                    <td><a href="/stock/table/edit/{{$target->warehouse_id}}">テーブルを編集する</a></td>
                                    <td><a href="/stock/table/{{$target->warehouse_id}}">テーブルを閲覧する</a></td>
                                    <td>
                                        <form action="/stock/table/delete/{{$target->warehouse_id}}" method="POST"  onSubmit="return checkSubmit()">
                                        {{ csrf_field() }}
                                            <input type="submit" value="削除" class="btn btn-standard btn-sm btn-dell" >
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
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
