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
                        <h3>操作対象画面</h3>
                    </div>
                    <br>
                    <br>
                    <hr>
                    <form class="form-horizontal" method="POST" action="/department/target_list/{id}/target">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('department_name') ? ' has-error' : '' }}">
                            <label for="department_name" class="col-md-3 control-label">部署名：</label>
                            <div class="col-md-6 ">
                                <label class="control-label">
                                    {{$department->department_name}}
                                </label>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('warehouse_name') ? ' has-error' : '' }}">
                            <label for="belong_user" class="col-md-3 control-label">操作対象グループ：</label>
                            <div class="form-group">
                                <div >
                                    <input type = "hidden" name="department_id" value ="{{$department->id}}">

                                    <div class="table-responsive">
                                        <div class="col-md-10 ">
                                            <table class="table table-striped table-sm">
                                                <thead>
                                                <tr>
                                                    <th>   </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($warehouses as $warehouse)
                                                    <tr>
                                                        <td><input type="checkbox" name="warehouse_id[]"  value="{{ $warehouse->id }}"
                                                                   @if(in_array($warehouse->id, $target_warehouses,true))checked @endif> : {{$warehouse->warehouse_name}} </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary"value="send">
                                    操作対象にする>>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
