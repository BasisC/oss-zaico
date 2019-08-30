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
                <div class="panel-heading"><h3>ホーム画面</h3></div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                   <p> You are logged in!</p>
                   <p>ログイン成功です！ ようこそ{{ Auth::user()->name }}さん</p>
                </div>
                <h2>Section title</h2>
                    <div class="table-responsive">
                    <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                           <th>#</th>
                               <th>Header</th>
                               <th>Header</th>
                               <th>Header</th>
                               <th>Header</th>
                           </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1,001</td>
                            <td>Lorem</td>
                            <td>ipsum</td>
                            <td>dolor</td>
                            <td>sit</td>
                        </tr>
                        <tr>
                            <td>1,002</td>
                            <td>amet</td>
                            <td>consectetur</td>
                            <td>adipiscing</td>
                            <td>elit</td>
                        </tr>
                        <tr>
                            <td>1,003</td>
                            <td>Integer</td>
                            <td>nec</td>
                            <td>odio</td>
                            <td>Praesent</td>
                        </tr>
                   </tbody>
                </table>
            </div>
            <br>
            <br>
            </div>
        </div>
    </div>
</div>
@endsection
