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
                        <h3>部署所属画面</h3>
                    </div>
                    <br>
                    <br>
                    <hr>
                    <form class="form-horizontal" method="POST" action="/department/detail/{id}/belong">
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
                            <label for="belong_user" class="col-md-3 control-label">所属ユーザ：</label>
                            <div class="form-group">
                                <div >
                                    <input type = "hidden" name="group_id" value ="{{$department->id}}">

                                    <div class="table-responsive">
                                        <div class="col-md-10 ">
                                            <table class="table table-striped table-sm">
                                                <thead>
                                                <tr>
                                                    <th>   </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($users as $user)
                                                    <tr>
                                                        <td><input type="checkbox" name="user_id[]"  value="{{ $user->id }}" @if(in_array($user->id, $belong_ids,true))checked @endif> : {{$user->name}} </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>




                                    </div>




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
