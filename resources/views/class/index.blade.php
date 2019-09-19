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
                        <h3>分類管理画面</h3>
                    </div>
                    <div class="col-md-6 ">
                        <br>
                        <p style="text-align: right">
                            <button type="submit" class="btn btn-primary " onclick="location.href='/classification/add'">
                                登録する>>
                            </button>
                        </p>
                    </div>
                <hr>
                <hr>
                <hr>

                <hr>
                <form class="form-horizontal" method="POST" action="/classification">
                    {{ csrf_field() }}

                    <div class="form-group{{ $errors->has('classification_name') ? ' has-error' : '' }}">
                        <label for="classification_name" class="col-md-4 control-label text-right">classification_name</label>

                        <div class="col-md-6">
                            <input id="classification_name" type="text" class="form-control" name="classification_name" value="{{ old('classification_name') }}" >

                            @if ($errors->has('classification_name'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('classification_name') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('warehouse_id') ? ' has-error' : '' }}">
                        <label for="warehouse_id" class="col-md-4 control-label text-right">Warehouse_name</label>

                        <div class="col-md-6">
                            <select  class="form-control" name="warehouse_id">
                                <option value="%">全て</option>
                                @foreach($warehouses as $warehouse)
                                    <option value={{$warehouse->id}}>{{$warehouse->warehouse_name}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('warehouse_id'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('warehouse_id') }}</strong>
                                    </span>
                            @endif
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
                            <td><a href="/classification?sort=classifications.id">#</a></td>
                            <td><a href="/classification?sort=warehouse_name">倉庫名</a></td>
                            <th><a href="/classification?sort=classification_name">分類名</a></th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->warehouse_name}}</td>
                                <td>{{$item->classification_name}}</td>
                                <td>
                                    <a href="/classification/edit/{{$item->id}}" class="btn btn-primary btn-sm">編集</a>
                                </td>
                                <td>
                                    <form action="/classification/delete/{{$item->id}}" method="POST"  onSubmit="return checkSubmit()">
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
