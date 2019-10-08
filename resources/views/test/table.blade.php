<html>
<head>
    <title>Multiple Button Confirm</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript">
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
    <link rel="stylesheet" media="screen" href="../multiple-action-confirm-box/stylesheets/maconfirm.css">
    <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">

</head>
<body>
<a href="javascript:void(0);" id="confirmLink">Multiple Button Confirm Link</a>
</body>
<form name="formName" method="post" action="/stock/table/create/{{$warehouse->id}}">
    {{ csrf_field() }}
    <input type="hidden" name="warehouse_id" value="{{$warehouse->id}}">
    <div id="input_pluralBox">
        <div id="input_plural">
           項目名： <input type = 'text' name ='col_name[]' class = 'form-control' placeholder='カラム名を入力してください'  required>
            項目のタイプ：<select name = "field_type[]"  class="form-control" id="field_type" onchange="entryChange2();">
                <option value = "null">項目タイプを選択してください
                <option value = "1">一行入力</option>
                <option value = '2'>複数行入力</option>
                <option value = "3">数字入力</option>
                <option value = '4'>単一選択</option>
                <option value = "5">複数選択</option>
                <option value = '6'>メールアドレス</option>
                <option value = "7">画像</option>
                <option value = "8">日付</option>
            </select>
            制約：<select name = 'unique[]' class = 'form-control'>
                <option value = 'No'>重複を許す</option>
                <option value = 'Yes'>重複を許さない</option>
            </select>
            <select name = 'null[]' class = 'form-control'>
                <option value = 'No'>空白を許す</option>
                <option value = 'Yes'>空白を許さない</option>
            </select>
            選択肢： <input type = 'text' name ='select[]' class = 'form-control' placeholder='選択肢を入力してください' >
            <input type="button" value="＋" class="add pluralBtn">
            <input type="button" value="－" class="del pluralBtn">
            <br>

            <br>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-6 col-md-offset-4">
            <button type="submit" class="btn btn-primary"value="send">
                登録する>>
            </button>
        </div>
    </div>
</form>




</html>
