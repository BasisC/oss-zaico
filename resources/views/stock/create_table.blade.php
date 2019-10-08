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
        $(document).on("click", ".add", function() {
            $(this).parent().clone(true).insertAfter($(this).parent());
        });
        $(document).on("click", ".del", function() {
            var target = $(this).parent();
            if (target.parent().children().length > 1) {
                target.remove();
            }
        });
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
                <form name="formName" method="post" action="/stock/table/create/{{$warehouse->id}}">
                    {{ csrf_field() }}
                    <input type="hidden" name="warehouse_id" value="{{$warehouse->id}}">
                    <div id="input_pluralBox">
                        <div id="input_plural">
                            項目名： <input type = 'text' name ='col_name[]'  placeholder='カラム名'  required>
                            タイプ：<select name = "field_type[]"   id="field_type" >
                                <option value = "null">タイプを選択してください
                                <option value = "1">一行入力</option>
                                <option value = '2'>複数行入力</option>
                                <option value = "3">数字入力</option>
                                <option value = '4'>単一選択</option>
                                <option value = "5">複数選択</option>
                                <option value = '6'>メールアドレス</option>
                                <option value = "7">画像</option>
                                <option value = "8">日付</option>
                            </select>
                            制約：<select name = 'unique[]'>
                                <option value = 'No'>重複を許す</option>
                                <option value = 'Yes'>重複を許さない</option>
                            </select>
                            <select name = 'null[]' >
                                <option value = 'No'>空白を許す</option>
                                <option value = 'Yes'>空白を許さない</option>
                            </select>

                            選択肢： <input type = 'text' name ='select[]'  placeholder='選択肢' >
                            <br>
                            項目の追加：<input type="button" value="＋" class="add pluralBtn">
                            項目の削除：<input type="button" value="－" class="del pluralBtn">
                            <br>

                            <br>
                        </div>
                    </div>
                    <div style="text-align: center;">
                        <button type="submit" class="btn btn-primary">
                            登録する
                        </button>
                    </div>
                </form>
                <br>
    <script>
        function checkSubmit() {
            return confirm("削除してもよろしいですか？");
        }

    </script>


@endsection

