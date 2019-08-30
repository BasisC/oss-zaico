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
                        <h3>グループ所属画面</h3>
                    </div>
                    <br>
                    <br>
                    <hr>
                    <form class="form-horizontal" method="POST" action="/group/detail/{id}/belong">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('warehouse_name') ? ' has-error' : '' }}">
                            <label for="warehouse_name" class="col-md-4 control-label">グループ名：</label>
                            <div class="col-md-6 col-md-offset-4">
                                {{$groups->group_name}}
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('warehouse_name') ? ' has-error' : '' }}">
                            <label for="warehouse_name" class="col-md-4 control-label">所属倉庫：</label>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    @foreach($warehouses as $warehouse)
                                        <h4><input type="checkbox" name="warehouse_id[]"  value="{{ $warehouse->id }}" @if(in_array($warehouse->id, $belongs,true))checked @endif> {{$warehouse->warehouse_name}}</h4>
                                    @endforeach
                            </div>
                            </div>
                        </div>
                        <input type = "hidden" name="group_id" value ="{{$groups->id}}">


                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary"value="send">
                                    所属させる>>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
