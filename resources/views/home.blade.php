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

                        <form method="POST" action="/upload" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{--成功時のメッセージ--}}
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            {{-- エラーメッセージ --}}
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                @if ($user->avatar_filename)
                                    <p>
                                        <img src="{{ asset('storage/avatar/1' . $user->avatar_filename) }}" alt="avatar" />
                                    </p>
                                @endif
                                <input type="file" name="file">
                            </div>

                            <div class="form-group">
                                <input type="submit">
                            </div>
                        </form>
                </div>

        </div>
    </div>

</div>




@endsection
