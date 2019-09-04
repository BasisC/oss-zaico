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
        <div class="col-md-11 ">
            <div class="panel panel-default">
                <div class="col-md-6 ">
                    <h3> {{$department->department_name}}</h3>
                </div>
                <div class="col-md-6 ">
                    <br>
                    <p style="text-align: right">
                        <button type="submit" class="btn btn-primary " onclick="location.href='/department/detail/{{$department->id}}/belong'">
                            所属させる>>
                        </button>
                    </p>
                </div>
                <br>
                <br>
                <hr>
                <hr>
                <form class="form-horizontal" method="POST" action="/department/detail/{{$department->id}}">
                    {{ csrf_field() }}

                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-1 control-label">Name:</label>

                        <div class="col-md-4">
                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                検索>>
                            </button>
                        </div>
                    </div>
                </form>
                <hr>
                <div class="table-responsive">
                    <div class="col-md-12 ">
                        <table class="table table-striped table-sm">
                            <thead>
                            <tr>
                                <th><a href="/department/detail/{{$department->id}}?sort=user_id">#</a></th>
                                <th><a href="/department/detail/{{$department->id}}?sort=name">所属ユーザ名</a></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($belong_department as $obj)
                            <tr>

                                    <td> {{$obj->user_id}}</td>
                                    <td> {{$obj->name}}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{$belong_department->appends(['sort'=>$sort,'name'=>$name])->links()}}
                    </div>
                </div>
                <hr>
            </div>
        </div>
    </div>
</div>
@endsection
