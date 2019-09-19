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
                <h3>分類編集画面</h3>
                </div>
                <br>
                <br>
                <hr>
                <form class="form-horizontal" method="POST" action="/classification/edit/{{$form->id}}}">
                    {{ csrf_field() }}
                    <input type="hidden"name="id"value="{{$form->id}}">

                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="classification_name" class="col-md-4 control-label">Classification_name</label>

                        <div class="col-md-6">
                            <input id="classification_name" type="text" class="form-control" name="classification_name" value="{{ $form->classification_name }}" required autofocus>

                            @if ($errors->has('classification_name'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('classification_name') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        <label for="address" class="col-md-4 control-label">address</label>

                        <div class="col-md-6">
                            <input id="address" type="text" class="form-control" name="address" value="{{ $form->address }}" required>

                            @if ($errors->has('address'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('warehouse_id') ? ' has-error' : '' }}">
                        <label for="warehouse_id" class="col-md-4 control-label">Warehouse_name</label>

                        <div class="col-md-6">
                            <select  class="form-control" name="warehouse_id">
                                @foreach($warehouses as $warehouse)
                                    <option @if($warehouse->id ==$form->warehouse_id) selected @endif value={{$warehouse->id}}>{{$warehouse->warehouse_name}}</option>
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
