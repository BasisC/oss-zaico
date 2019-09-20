@extends('layouts.app_before_login')

@section('content')
    <script>
        @if (session('error_message'))
        $(function () {
            toastr.error('{{ session('error_message') }}');
        });
        @endif
    </script>
<div class="container">
    <div class="row">
        <div class="col-md-10 ">
            <div class="panel panel-default">
                <div class="col-md-6 ">
                <h3>編集画面（{{$stock->serial_number}}</h3>
                </div>
                <br>
                <br>
                <hr>
                ※ステータス変更は絶対に分けること（テーブルおかしくなるよ！）
                <br>
                <br>
                <form class="form-horizontal" method="POST" action="/stock/warehouse/{{$warehouse->id}}/edit/{{$stock->id}}}">
                    {{ csrf_field() }}
                    <input type="hidden"name="stock_id"value="{{$stock->id}}">
                    <input type="hidden" name="warehouse_id" value="{{$warehouse->id}}">
                    <div class="form-group{{ $errors->has('classification_id') ? ' has-error' : '' }}">
                        <label for="classification_id" class="col-md-4 control-label">分類</label>

                        <div class="col-md-6">
                            <select name = "classification_id" class="form-control">
                                @foreach($classifications as $classification)
                                    @if($classification->warehouse_id == $warehouse->id)
                                        <option value={{$classification->id}}>{{$classification->classification_name}}</option>;
                                    @endif
                                @endforeach
                            </select>
                            @if ($errors->has('classification_id'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('classification_id') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('serial_number') ? ' has-error' : '' }}">
                        <label for="serial_number" class="col-md-4 control-label">製造番号</label>

                        <div class="col-md-6">
                            <input id="serial_number" type="text" class="form-control" name="serial_number" value="{{$stock->serial_number }}" required>

                            @if ($errors->has('serial_number'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('serial_number') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary"value="send">
                                編集する>>
                            </button>
                        </div>
                    </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
