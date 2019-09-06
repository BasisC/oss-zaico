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
                        <h3>グループ登録画面</h3>
                    </div>
                    <br>
                    <br>
                    <hr>
                    <form class="form-horizontal" method="POST" action="/group/add">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('group_name') ? ' has-error' : '' }}">
                            <label for="group_name" class="col-md-4 control-label">group_name</label>

                            <div class="col-md-6">
                                <input id="group_name" type="text" class="form-control" name="group_name" value="{{ old('group_name') }}" required autofocus>

                                @if ($errors->has('group_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('group_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary"value="send">
                                    登録する>>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
