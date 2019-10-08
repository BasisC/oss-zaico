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

<form class="form-horizontal" method="POST" action="/stock/table/date_add">
    {{ csrf_field() }}
@foreach($col_dates as $col_date)
    @if($col_date[0]!=="id"&&$col_date[0]!=="created_at"&&$col_date[0]!=="updated_at")
        <input type="hidden" name="types[{{$col_date[0]}}]" value= {{$col_date[1]}} >
        <input type="hidden" name="null_ables[{{$col_date[0]}}]" value= {{$col_date[2]}} >
        <input type="hidden" name="col_names[]" value={{$col_date[0]}}>
        @if(strpos($col_date[1],'int') !== false)
                <div class="form-group{{ $errors->has($col_date[0]) ? ' has-error' : '' }}">
                    <label for=$col_date[0] class="col-md-4 control-label">{{$col_date[0]}}!!!!!</label>

                    <div class="col-md-6">
                        <input id=$col_date[0] type="number" class="form-control" name={{$col_date[0]}} value="{{ old($col_date[0]) }}" @if($col_date[2]==="YES" )required @endif>

                        @if ($errors->has($col_date[0]))
                            <span class="help-block">
                                <strong>{{ $errors->first($col_date[0]) }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
        @endif
    @endif
@endforeach
    <div class="form-group">
        <div class="col-md-6 col-md-offset-4">
            <button type="submit" class="btn btn-primary"value="send">
                登録する>>
            </button>
        </div>
    </div>
</form>
