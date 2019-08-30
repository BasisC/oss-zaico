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
                <h3>倉庫編集画面</h3>
                </div>
                <br>
                <br>
                <hr>
                <form class="form-horizontal" method="POST" action="/warehouse/edit/{{$form->id}}}">
                    {{ csrf_field() }}
                    <input type="hidden"name="id"value="{{$form->id}}">

                    <div class="form-group{{ $errors->has('warehouse_name') ? ' has-error' : '' }}">
                        <label for="warehouse_name" class="col-md-4 control-label">Warehouse_name</label>

                        <div class="col-md-6">
                            <input id="warehouse_name" type="text" class="form-control" name="warehouse_name" value="{{ $form->warehouse_name }}" required autofocus>

                            @if ($errors->has('warehouse_name'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('warehouse_name') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        <label for="address" class="col-md-4 control-label">Address</label>

                        <div class="col-md-6">
                            <input id="address" type="text" class="form-control" name="address" value="{{ $form->address }}" required>

                            @if ($errors->has('address'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('tel-number') ? ' has-error' : '' }}">
                        <label for="password" class="col-md-4 control-label">Tel_number</label>

                        <div class="col-md-6">
                            <input id="tel_number" type="text" class="form-control" name="tel_number"  value="{{ $form->tel_number }}" required>

                            @if ($errors->has('tel_number'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('tel_number') }}</strong>
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
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
