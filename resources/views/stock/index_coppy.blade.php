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

    <script type = "text/javascript">
        function functionName()
        {
            var select1 = document.forms.formName.department_id; //変数select1を宣言
            var select2 = document.forms.formName.group_id; //変数select2を宣言

            select2.options.length = 0; // 選択肢の数がそれぞれに異なる場合、これが重要


            if (select1.options[select1.selectedIndex].value !== "null") {
                @foreach($targets as $target)
                    select2.options[0] = new Option("(操作対象のグループを選択してください)","null");
                    if(select1.options[select1.selectedIndex].value == {{$target->department_id}}){
                        select2.options[{{$target->warehouse_id}}] = new Option("{{$target->warehouse_name}}",{{$target->warehouse_id}});
                    }
                @endforeach
            }
            else {
                select2.options[0] = new Option("(操作対象のグループを選択してください)","null");
            }

        }
        function test(){
            var select2 = document.forms.formName.group_id; //変数select2を宣言
        }

    </script>


    <body bgcolor onLoad="functionName()">

    <div class="container">
    <div class="row">
        <div class="col-md-11 ">
            <div class="panel panel-default">
                    <div class="col-md-6 ">
                        <h3>機器管理画面</h3>
                    </div>
                    <div class="col-md-6 ">
                        <br>
                        <p style="text-align: right">
                            <button type="submit" class="btn btn-primary " onclick="location.href='/stock/add'">
                                登録する>>
                            </button>
                        </p>
                    </div>
                <hr>
                <hr>
                <hr>

                <hr>

                <form name="formName" method="post" action="/stock/">
                    {{ csrf_field() }}
                    <div class="form-group{{ $errors->has('classification_name') ? ' has-error' : '' }}">
                        <label for="classification_name" class="col-md-4 control-label text-right">所属部署：</label>

                        <div class="col-md-6">

                            <select name = "department_id" onChange="functionName()" class="form-control">
                                <option value = "null">(部署を選択してください)</option>
                                @foreach($belong_depart as $belong)
                                    <option value={{$belong->department_id}}>{{$belong->department_name}}</option>;
                                @endforeach
                            </select>
                            @if ($errors->has('classification_name'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('classification_name') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>


                    <div class="form-group{{ $errors->has('classification_name') ? ' has-error' : '' }}">
                        <label for="classification_name" class="col-md-4 control-label text-right">操作対象：</label>


                        <div class="col-md-6">

                            <select name = "group_id" class="form-control">
                            </select>
                            @if ($errors->has('classification_name'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('classification_name') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>


                    <div class="form-group{{ $errors->has('classification_name') ? ' has-error' : '' }}">
                        <label for="classification_name" class="col-md-4 control-label"></label>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">
                                検索>>
                            </button>
                        </div>
                    </div>



                </form>
                <br>
                <br>
                <br>
                <br>
                <hr>

            </div>
        </div>
    </div>
    </div>
    </body>
    <script>
        function checkSubmit() {
            return confirm("削除してもよろしいですか？");
        }

    </script>


@endsection
