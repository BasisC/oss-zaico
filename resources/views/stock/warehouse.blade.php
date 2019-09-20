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
                        <h3>機器管理画面（{{$warehouse->warehouse_name}}）  </h3>
                    </div>
                <div class="col-md-6 ">
                    <br>
                    <p style="text-align: right">
                        <button type="submit" class="btn btn-primary " onclick="location.href='/stock/warehouse/{{$warehouse->id}}/add'">
                            登録する>>
                        </button>
                    </p>
                </div>
                <br>
                <br>
                <br>
                <hr>
                <form class="form-horizontal" method="POST" action="/stock/warehouse/{{$warehouse->id}}">
                    {{ csrf_field() }}

                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="classification_name" class="col-md-1 control-label">分類:</label>

                        <div class="col-md-4">
                            <select name = "classification_id" class="form-control">
                                <option value = " ">(分類名を選択してください)</option>
                                @foreach($classifications as $classification)
                                    @if($classification->warehouse_id == $warehouse->id)
                                        <option value={{$classification->id}}>{{$classification->classification_name}}</option>;
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <label for="status" class="col-md-1 control-label">状態:</label>

                        <div class="col-md-4">
                            <select name = "status_id" class="form-control">
                                <option value = " ">(機器のステータスを選択してください)</option>
                                <option value=0>検品済</option>;
                                <option value=1>持出不可</option>;
                                <option value=2>持出中</option>;
                                <option value=3>設置済</option>;
                                <option value=4>返品中</option>;
                            </select>
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="serial_number" class="col-md-1 control-label">製造番号:</label>

                        <div class="col-md-4">
                            <input id="serial_number" type="text" class="form-control" name="serial_number" value="{{ old('serial_number') }}">
                        </div>


                    </div>


                    <div class="col-md-4">
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
                                <th><a href="/stock/warehouse/{{$warehouse->id}}?sort=id">#</a></th>
                                <th><a href="/stock/warehouse/{{$warehouse->id}}?sort=classification_id">分類名</a></th>
                                <th><a href="/stock/warehouse/{{$warehouse->id}}?sort=serial_number">製造番号</a></th>
                                <th><a href="/stock/warehouse/{{$warehouse->id}}?sort=status">機器ステータス</a></th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($stocks as $stock)
                                <tr>
                                    <td>{{$stock->id}}</td>
                                    <td>{{$stock->getName()}}</td>
                                    <td>{{$stock->serial_number}}</td>
                                    <td>{{$stock->setStatusName($stock->status)}}</td>
                                    <td>  <a href="/stock/warehouse/{{$warehouse->id}}/edit/{{$stock->id}}" class="btn btn-primary btn-sm">編集</a></td>
                                    <td>  <a href="/stock/warehouse/{{$warehouse->id}}/detail/{{$stock->id}}" class="btn btn-primary btn-sm">詳細</a></td>
                                    <td>  <a href="/stock/warehouse/{{$warehouse->id}}/status/{{$stock->id}}" class="btn btn-primary btn-sm">ステータス変更</a></td>
                                    <td>
                                        <form action="/stock/warehouse/{{$warehouse->id}}/delete/{{$stock->id}}" method="POST"  onSubmit="return checkSubmit()">
                                            {{ csrf_field() }}
                                            <input type="submit" value="削除" class="btn btn-standard btn-sm btn-dell" >
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{$stocks->appends(['sort'=>$sort])->links()}}
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
