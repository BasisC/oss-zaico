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
                <h3>ユーザ編集画面</h3>
                </div>
                <br>
                <br>
                <hr>
                <form class="form-horizontal" method="POST" action="/user/edit/{{$form->id}}}">
                    {{ csrf_field() }}
                    <input type="hidden"name="id"value="{{$form->id}}">

                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-4 control-label">User_name</label>

                        <div class="col-md-6">
                            <input id="name" type="text" class="form-control" name="name" value="{{ $form->name }}" required autofocus>

                            @if ($errors->has('name'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email" class="col-md-4 control-label">email</label>

                        <div class="col-md-6">
                            <input id="email" type="text" class="form-control" name="email" value="{{ $form->email }}" required>

                            @if ($errors->has('email'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="col-md-4 control-label">Password</label>

                        <div class="col-md-6">
                            <input id="password" type="password" class="form-control" name="password"  >

                            @if ($errors->has('password'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>
                    @if($form->type == 1)
                        <div class="form-group">
                            <label for="permission" class="col-md-4 control-label">付与する役割</label>

                            <div class="col-md-6 col-md-offset-4">
                                <div class="col-md-6">
                                    <input id="per_department_create" type="checkbox"  name="per_department_create" value =1
                                           @if($form->per_department_create == 1)checked @endif> 部署の登録
                                </div>
                                <div class="col-md-6">
                                    <input id="per_group_create" type="checkbox"  name="per_group_create" value = 1
                                       @if($form->per_group_create == 1)checked @endif>  グループの登録
                                </div>
                                <div class="col-md-6">
                                    <input id="per_department_update" type="checkbox"  name="per_department_update" value = 1
                                       @if($form->per_department_update == 1)checked @endif> 部署の編集
                                </div>
                                <div class="col-md-6">
                                    <input id="per_group_update" type="checkbox"  name="per_group_update" value = 1
                                       @if($form->per_group_update == 1)checked @endif> グループの編集
                                </div>
                                <div class="col-md-6">
                                    <input id="per_department_delete" type="checkbox"  name="per_department_delete" value = 1
                                       @if($form->per_department_delete == 1)checked @endif>  部署の削除
                                </div>
                                <div class="col-md-6">
                                    <input id="per_group_delete" type="checkbox"  name="per_group_delete" value = 1
                                       @if($form->per_group_delete == 1)checked @endif> グループの削除
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary"value="send">
                                編集する>>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
