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
                <div class="table-responsive">
                    <div class="col-md-12 ">
                        <table class="table table-striped table-sm">
                            <thead>
                            <tr>
                                <th><a href="/department/detail/{{$department->id}}?sort=id">#</a></th>
                                <th><a href="/department/detail/{{$department->id}}?sort=name">ユーザ名</a></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($belong_department as $obj)
                            <tr>

                                    <td> {{$obj->user_id}}</td>
                                    <td> {{$obj->be_depart->name}}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr>
            </div>
        </div>
    </div>
</div>

@endsection
