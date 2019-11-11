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
                    <div class="col-md-6 ">
                        <h3>倉庫名：{{$warehouse_name->warehouse_name}}</h3>
                    </div>

                <br>
                <br>
                <hr>


                <div class="table-responsive">
                    <div class="col-md-12 ">
                        <table class="table table-striped table-sm">
                            <thead>
                            <tr>
                                <th>ID</th>
                                @foreach ($forms as $form)
                                    <th> {{$form->col_name->getName()}}</th>
                                @endforeach
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($warehouses as $warehouse)
                                <tr>
                                    <td>{{$warehouse->id}}</td>
                                @for ($i = 1; $i <= $col_count; $i++)
                                    @php
                                        $ans = "col_".$i;
                                        $list = $i - 1;
                                    @endphp
                                    @if($forms[$list]['form_type_id'] == 7)
                                            <td><img src="{{ asset("storage/".$warehouse_id."_warehouse_img/". $warehouse->$ans) }}" alt="no-img" width="96" height="65"/></td>
                                    @elseif($forms[$list]['form_type_id'] == 4)
                                        @if($warehouse->$ans == null)
                                            <td>    </td>
                                            @else
                                                <td>{{$select_array[$ans][$warehouse->$ans]}}</td>
                                            @endif
                                    @elseif($forms[$list]['form_type_id'] == 5)
                                        @if($warehouse->$ans == null)
                                            <td>   </td>
                                        @else
                                            <td>
                                            @foreach($select_array[$warehouse->id] as $select_id)
                                                      {{$select_array[$ans][$select_id]}}
                                            @endforeach
                                            </td>
                                        @endif
                                    @else
                                            <td>{{$warehouse->$ans}}</td>
                                    @endif

                                @endfor
                                    <td>
                                        <a href="/stock/table/{{$warehouse_id}}/edit/{{$warehouse->id}}" class="btn btn-primary btn-sm">編集</a>
                                    </td>
                                    <td>
                                        <form action="/stock/table/{{$warehouse_id}}/delete/{{$warehouse->id}}" method="POST"  onSubmit="return checkSubmit()">
                                            {{ csrf_field() }}
                                            <input type="submit" value="削除" class="btn btn-standard btn-sm btn-dell" >
                                        </form>
                                    </td>
                                </tr>
                            @endforeach


                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="table-responsive">
                    <div class="col-md-12 ">
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
                    </div>
                    <div class="col-md-12 ">
                        <a href="/stock/table/create/{{$warehouse_id}}">登録画面>></a><br>
                        <a href="/stock/table/{{$warehouse_id}}">表示画面>></a><br>
                        <a href="/stock/table/data_add/{{$warehouse_id}}">データ追加画面>></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>



    <script>
        function checkSubmit() {
            return confirm("削除してもよろしいですか？");
        }

    </script>


@endsection

