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
                <h3>ステータス変更画面（{{$stock->serial_number}}）</h3>
                </div>
                <br>
                <br>
                <hr>
                ※ステータス変更は絶対に分けること（テーブルおかしくなるよ！）
                <br>
                <br>
                <form class="form-horizontal" method="POST" action="/stock/warehouse/{{$warehouse->id}}/status/{{$stock->id}}}/change">
                    {{ csrf_field() }}
                    <input type="hidden"name="stock_id"value="{{$stock->id}}">
                    <input type="hidden" name="warehouse_id" value="{{$warehouse->id}}">

                    <div class="form-group{{ $errors->has('new_stock_status') ? ' has-error' : '' }}">
                        <label for="new_stock_status" class="col-md-4 control-label">変更後ステータス:</label>
                    <div class="col-md-6 control">
                        <select name = "new_status" class="form-control">
                            @if(
                            $stock->status== $status_list["INSPECTED"]||
                            $stock->status== $status_list["CANT_TAKEOUT"]||
                            $stock->status== $status_list["RETURNING"]||
                            $stock->status== $status_list["TAKING_OUT"]||
                            $stock->status == $status_list["INSTALLED"]
                            )
                                <option value=0 @if( $stock->status == 0) selected @endif>検品済</option>;
                            @endif
                            @if(
                            $stock->status== $status_list["INSPECTED"]||
                            $stock->status== $status_list["CANT_TAKEOUT"]
                            )
                                <option value=1 @if( $stock->status == 1) selected @endif>持出不可</option>;
                            @endif
                            @if(
                            $stock->status== $status_list["INSPECTED"]||
                            $stock->status== $status_list["TAKING_OUT"]
                            )
                                <option value=2 @if( $stock->status == 2) selected @endif>持出中</option>;
                            @endif
                            @if(
                            $stock->status == $status_list["TAKING_OUT"]||
                            $stock->status == $status_list["INSTALLED"]
                            )
                                <option value=3 @if( $stock->status == 3) selected @endif>設置済</option>;
                            @endif
                            @if(
                             $stock->status== $status_list["INSPECTED"]||
                            $stock->status== $status_list["CANT_TAKEOUT"]
                            )
                                <option value=4 @if( $stock->status == 4) selected @endif>返品中</option>;
                            @endif
                        </select>
                    </div>
                        <br>
                        <br>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary"value="send">
                                ステータス変更>>
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
