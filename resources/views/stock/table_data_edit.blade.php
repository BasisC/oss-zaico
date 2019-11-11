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
                        <h3>倉庫名：{{$warehouse->warehouse_name}}</h3>
                    </div>

                <br>
                <br>
                <hr>

                <form class="form-horizontal" method="POST" action="/stock/table/{{$warehouse->id}}/edit/{{$record->id}}" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <input type="hidden" name="warehouse_id" value="{{$warehouse->id}}">
                    @php
                        $i = 1;
                    @endphp
                    @foreach($forms as $form)
                        {{$form->form_type_id}}
                        @php
                            $col_name = "col_".$i;
                        @endphp
                        @switch($form->form_type_id)
                            @case(1)
                            text_box
                            <div class="form-group{{ $errors->has($col_name) ? ' has-error' : '' }}">
                                <label for="classification_id" class="col-md-4 control-label">{{$form->col_name->getName()}}</label>
                                <div class="col-md-6">
                                    <input type="text" class ="form-control" name="{{$form->col_fictitious_name}}" value="{{$record->$col_name}}">
                                    @if ($errors->has($col_name))
                                        <span class="help-block">
                                        <strong>{{ $errors->first($col_name) }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @break

                            @case(2)
                            text_area
                            <div class="form-group{{ $errors->has($col_name) ? ' has-error' : '' }}">
                                <label for="classification_id" class="col-md-4 control-label">{{$form->col_name->getName()}}</label>
                                <div class="col-md-6">
                                    <textarea name="{{$form->col_fictitious_name}}" rows="4" cols="40"  class ="form-control"  value="{{$record->$col_name}}"></textarea>
                                    @if ($errors->has($col_name))
                                        <span class="help-block">
                                        <strong>{{ $errors->first($col_name) }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @break

                            @case(3)
                            number
                            <div class="form-group{{ $errors->has($col_name) ? ' has-error' : '' }}">
                                <label for="classification_id" class="col-md-4 control-label">{{$form->col_name->getName()}}</label>
                                <div class="col-md-6">
                                    <input type="number"  class ="form-control" name="{{$form->col_fictitious_name}}" value="{{$record->$col_name}}">
                                    @if ($errors->has($col_name))
                                        <span class="help-block">
                                        <strong>{{ $errors->first($col_name) }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @break

                            @case(4)
                            radio_button
                            <div class="form-group{{ $errors->has($col_name) ? ' has-error' : '' }}">
                                <label for="classification_id" class="col-md-4 control-label">{{$form->col_name->getName()}}</label>
                                <div class="col-md-6">
                                    @foreach($form->selects as $obj)
                                        <input type="radio" name="{{$form->col_fictitious_name}}" value= "{{$obj->id}}" @if($record->$col_name == $obj->id)checked="checked"@endif> {{$obj->select_value}}<br>
                                    @endforeach
                                        @if ($errors->has($col_name))
                                            <span class="help-block">
                                        <strong>{{ $errors->first($col_name) }}</strong>
                                    </span>
                                        @endif
                                </div>
                            </div>
                            @break

                            @case(5)
                            check_box
                            <div class="form-group{{ $errors->has($col_name) ? ' has-error' : '' }}">
                                <label for="classification_id" class="col-md-4 control-label">{{$form->col_name->getName()}}</label>
                                <div class="col-md-6">
                                    @foreach($form->selects as $obj)
                                        <input type="checkbox" name="{{$form->col_fictitious_name}}[]" value="{{$obj->id}}"@if(in_array($obj->id,$checkbox_value_array)) checked="checked" @endif  >{{$obj->select_value}}<br>

                                        {{$record->$col_name}}<br>
                                    @endforeach
                                        @if ($errors->has($col_name))
                                            <span class="help-block">
                                        <strong>{{ $errors->first($col_name) }}</strong>
                                    </span>
                                        @endif
                                </div>
                            </div>
                            @break

                            @case(6)
                            email
                            <div class="form-group{{ $errors->has($col_name) ? ' has-error' : '' }}">
                                <label for="classification_id" class="col-md-4 control-label">{{$form->col_name->getName()}}</label>
                                <div class="col-md-6">
                                    <input type="email" name="{{$form->col_fictitious_name}}" size="30" maxlength="40"  class ="form-control">
                                    @if ($errors->has($col_name))
                                        <span class="help-block">
                                        <strong>{{ $errors->first($col_name) }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @break
                            @case(7)
                            img
                            <div class="form-group{{ $errors->has($col_name) ? ' has-error' : '' }}">
                                <label for="classification_id" class="col-md-4 control-label">{{$form->col_name->getName()}}</label>
                                <div class="col-md-6">
                                    <input type="file" name="{{$form->col_fictitious_name}}">
                                    @if ($errors->has($col_name))
                                        <span class="help-block">
                                        <strong>{{ $errors->first($col_name) }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            @break

                            @case(8)
                        datetime
                            <div class="form-group{{ $errors->has($col_name) ? ' has-error' : '' }}">
                                <label for="classification_id" class="col-md-4 control-label">{{$form->col_name->getName()}}</label>
                                <div class="col-md-6">
                                    <input type="datetime-local" name="{{$form->col_fictitious_name}}" value="2019-01-01T12:00">
                                    @if ($errors->has($col_name))
                                        <span class="help-block">
                                        <strong>{{ $errors->first($col_name) }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @break

                            @default
                            デフォルトのケース
                        @endswitch
                        @php $i += 1;@endphp
                    @endforeach
                    <div class="form-group{{ $errors->has('classification_name') ? ' has-error' : '' }}">
                        <label for="classification_name" class="col-md-4 control-label"></label>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">
                                追加する>>
                            </button>
                        </div>
                    </div>
                </form>
                <br>

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

