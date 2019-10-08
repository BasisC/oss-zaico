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
                <h3>編集画面（{{$warehouse->warehouse_name}})</h3>
                </div>
                <br>
                <br>
                <hr>
                {{$warehouse_table}}
                {{$warehouse_table_cols}}

                <br>
                <br>

            </div>
        </div>
    </div>
</div>
@endsection
