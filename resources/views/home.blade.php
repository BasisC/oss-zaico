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
                <h2>実装予定機能リスト</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>機能名</th>
                            <th>場所</th>
                            <th>説明</th>
                            <th>ステータス</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>1</td>
                            <td>ステータス変更機能</td>
                            <td>/stock/warehouse/{id}</td>
                            <td>機器のステータスを変更する機能</td>
                            <td>未着手(2019-09-19)</td>
                        </tr>
                        <tr>
                            <td>1-1</td>
                            <td>持出管理</td>
                            <td>/stock/warehouse/{id}</td>
                            <td>機器のステータスを「持出中にする」</td>
                            <td>未着手(2019-09-19)</td>
                        </tr>
                        <tr>
                            <td>1-2</td>
                            <td>返品管理</td>
                            <td>/stock/warehouse/{id}</td>
                            <td>機器のステータスを「返品中にする」</td>
                            <td>未着手(2019-09-19)</td>
                        </tr>
                        <tr>
                            <td>1-3</td>
                            <td>返却管理</td>
                            <td>/stock/warehouse/{id}</td>
                            <td>機器のステータスを「持出中」から「検品済」にする</td>
                            <td>未着手(2019-09-19)</td>
                        </tr>
                        <tr>
                            <td>1-4</td>
                            <td>設置管理</td>
                            <td>/stock/warehouse/{id}</td>
                            <td>機器のステータスを「持出中」から「設置済」にする</td>
                            <td>未着手(2019-09-19)</td>
                        </tr>
                        <tr>
                            <td>1-5</td>
                            <td>検品管理</td>
                            <td>/stock/warehouse/{id}</td>
                            <td>機器のステータスを「返品中」から「検品済」にする</td>
                            <td>未着手(2019-09-19)</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>一部機能QRコード対応</td>
                            <td>/stock/warehouse/{id}</td>
                            <td>今考えているのは「ステータス」と「登録」</td>
                            <td>未着手（2019-09-19）</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>倉庫移動</td>
                            <td>/stock/warehouse/{id}</td>
                            <td>機器の所属する倉庫を変更できる。（一回考える必要あり）</td>
                            <td>未着手(2019-09-19)</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>トレース機能</td>
                            <td>/stock/warehouse/{id}</td>
                            <td>機器のステータス変更履歴等を確認できる</td>
                            <td>未着手(2019-09-19)</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>まとめて操作機能</td>
                            <td>/stock/warehouse/{id}</td>
                            <td>一部機能にまとめて操作を実装する</td>
                            <td>未着手(2019-09-19)</td>
                        </tr>
                        </tbody>
                    </table>
                    <br>

                    <順序><br>
                        1,トレース機能の実装<br>
                        2,ステータス管理の実装<br>
                        3,一部機能まとめて実行<br>
                        4,倉庫移動の実装<br>
                        5,一部機能QR化<br>
                        ※4,と5を考え中



                    <hr>


                <h2>懸念点・注意事項リスト</h2>
                    <div class="table-responsive">
                    <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>memo</th>
                            <th>日付</th>
                            <th>ステータス</th>
                            <th>備考</th>
                           </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td> 分類と倉庫と機器</td>
                            <td>2019-09-18</td>
                            <td>未着手</td>
                            <td>下に追記有</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>ステータスとトレース</td>
                            <td>2019-09-18</td>
                            <td>未着手</td>
                            <td>下に追記有</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>ページネイト使用箇所</td>
                            <td>2019-09-19</td>
                            <td>未着手</td>
                            <td>ページネイトにありえない数を入れても動く=>処理必要</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>ログの強化</td>
                            <td>2019-09-19</td>
                            <td>ユーザ管理のみ着手</td>
                            <td>ログの強化を行う。（全機能に対して）</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                   </tbody>
                </table>
            </div>
            <br>
            <br>
            </div>
            </div>

            <div class="container">
                <div class="row">
                    <div class="panel panel-default">
                        <h4><b>倉庫と分類と機器[/classification/edit{id}にて]</b><br></h4>
                        <li>分類編集時に所属倉庫を変更できる。</li>
                        <li>分類の下に一つでも機器が登録されている場合、面倒なことになる。</li>
                        よって、以下の仕様にすること
                        <br><br>
                        <div>
                            <h5>①保有する倉庫を変更する（機器所属あり） </h5>
                            全ての機器が変更した倉庫に所属する<br><br>
                            <h5>②保有する倉庫を変更する（機器所属なし） </h5>
                            問題なく変更可能
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="panel panel-default">
                        <h4><b>ステータスとトレース機能[/stock/warehouse/{id}にて]</b><br></h4>
                        <li>一つの機器はステータスを一つ持つ</li>
                        <li>どうやって分類するか？</li>
                        以下のどちらかの仕様にすること
                        <br>
                        <br>
                        <div>
                            <h5>《ステータス一つごとにcreate_atとupdate_atを作る》 </h5>
                            ステータス一つずつにcreate_atとupdate_at（つまり、編集時刻と作成時刻）を
                            作成する。これが最新のものを一覧に表示する。<br>
                            =>トレースできないなぁ...<br><br>
                            <h5>《新しくテーブルを作成する》 </h5>
                            時間があるときに新しいテーブルを追加する（ステータス変更履歴テーブル）<br>
                            =>これらばトレースできるだろう。
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>




@endsection
