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
                <h3>ステータス変更履歴画面（{{$stock->serial_number}}）</h3>
                </div>
                <div class="col-md-6 ">
                    <br>
                    <p style="text-align: right">
                        <button type="submit" class="btn btn-primary " onclick="location.href='/stock/warehouse/{{$warehouse->id}}/status/{{$stock->id}}/change'">
                            ステータスを変更する>>
                        </button>
                    </p>
                </div>
                <br>
                <br>
                <br>
                <hr>

                <div class="table-responsive">
                    <label for="classification_id" class=" control-label"><h4>&nbsp;&nbsp;&nbsp;&nbsp;現在のステータス:{{$stock->setStatusName($stock->status)}}</h4></label>
                    <div class="col-md-12 ">
                        <table class="table table-striped table-sm">
                            <thead>
                            <tr>
                                <th>変更日時</th>
                                <th>変更者</th>
                                <th>変更後ステータス</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($stock_history as $history)
                                <tr>
                                    <td>{{$history->created_at}}</td>
                                    <td>{{$history->getName()}}</td>
                                    <td>{{$history->setStatusName($history->status)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
