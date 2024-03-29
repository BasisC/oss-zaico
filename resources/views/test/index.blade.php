<html>
<head>
    <title>Multiple Button Confirm</title>
    <script src="../multiple-action-confirm-box/javascripts/maconfirm.js" type="text/javascript"></script>
    <script src="http://code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
    <link rel="stylesheet" media="screen" href="../multiple-action-confirm-box/stylesheets/maconfirm.css">
    <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">

</head>
<body>
<a href="javascript:void(0);" id="confirmLink">Multiple Button Confirm Link</a>
</body>
<script type="text/javascript">
    $(function(){
        $("#confirmLink").click(function(){
            uploadConfirm({
                id: 'confirm',
                title: "override confirm",
                message: 'We have a same name file already. Do you override it?',
                buttons: {
                    "Yes": {
                        "class": "btn btn-info btn-small",
                        action: function() {
                            alert('replaced a file.');
                        }
                    },
                    "Yes To All": {
                        "class": "btn btn-info btn-small",
                        action: function() {
                            alert('replaced all files.');
                        }
                    },
                    "No": {
                        "class": "btn btn-small",
                        action: function() {
                            alert('original')
                        }
                    },
                    "No To All": {
                        "class": "btn btn-small",
                        action: function() {
                            alert('original')
                        }
                    }
                }
            });
        });
    });
</script>
<br>

<table class="table table-striped table-sm">
    <thead>
    <tr>
        <th>カラム名</th>
        <th>データ型</th>
        <th>空白を許可する</th>
        <th>キー制約</th>
        <th>初期値</th>
        <th>その他</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($col_dates as $col_date)
        <tr>
            <td>{{$col_date[0]}}</td>
            <td>{{$col_date[1]}}</td>
            <td>{{$col_date[2]}}</td>
            <td>{{$col_date[3]}}</td>
            <td>{{$col_date[4]}}</td>
            <td>{{$col_date[5]}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<a href="/stock/table/create/{{$warehouse_id}}">登録画面>></a><br>
<a href="/stock/table/{{$warehouse_id}}">表示画面>></a><br>
<a href="/stock/table/date_add/{{$warehouse_id}}">データ追加画面>></a>
</html>
