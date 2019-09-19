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
                        <button type="submit" class="btn btn-primary " onclick="location.href='/department/target_list/{{$department->id}}/target'">
                            操作対象を追加する>>
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
                                <th><a href="/department/target_list/{{$department->id}}?sort=group_id">#</a></th>
                                <th><a href="/department/target_list/{{$department->id}}?sort=group_name">操作対象グループ</a></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($targets as $target)
                            <tr>
                                <td>{{$target->warehouse_id}}</td>
                                <td>{{$target->warehouse_name}}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{$targets->appends(['sort'=>$sort])->links()}}
                    </div>
                </div>
                <hr>
            </div>
        </div>
    </div>
</div>
@endsection
