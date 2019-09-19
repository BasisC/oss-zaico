<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\SystemDef;
use App\MessageDef;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param $request => リクエストで受け取った値
     * @param $col => リクエストで受けとった値から取得したい項目
     * @param $default => 初期値。$colがnullの時に返す値
     * @return mixed
     *
     */
    public function setValue($request,$col,$default){
        $setValue = $request->$col;
        if($setValue == null){
            return $default;
        }
        else{
            return $setValue;
        }
    }

    /**
     * 検索機能で使用する。
     * 検索時に必要な値をセッション等から探して値を決定する
     *
     * ①検索条件が指定されていない&&ソートが選択されていない
     *   =>セッションに保存した値を削除し、ワイルドカードを返す
     * ②検索条件が指定されていない&&ソートが選択されている
     *   =>セッションに保存した値を返す。
     * ③検索条件が指定されている場合
     * 　=>セッションに値を保存。検索を行う
     *
     * @param Request $request => フォームから送られた値
     * @param $caram => フォームからの値の中から必要なデータ名
     * @param $sort => indexにて最初に定義している値。
     * @param $key => session内でデータを操作する際に必要
     * @return mixed|string => 出力した値を元に検索を行い、結果を表示する。
     */

    public function getSessionValue(Request $request,$col,$sort,$key){
        if($request->$col == null){//検索条件が指定されていない時
            if($sort == null){
                //セッションに保存された値を削除し、ワイルドカードを返す
                $request->session()->forget($key);
                return "%";
            }else{
                //セッションに保存した値を返す。失敗した場合は、ワイルドカードを返す
                return $value = $request->session()->get($key,"%");
            }

        }else{//検索条件が指定さてている時
            //セッションに検索条件を保存
            $request->session()->put($key,$request->$col);
            return $request->$col;
        }

    }

    /**
     * ログに出力するフォーマットを作成する。（エラー時処理）
     *
     * @param $controller => コントローラ―ファイルの名前
     * @param $function => 処理の名前
     * @param $e => エラー名
     * @return string => フォーマット
     */
    public function setLogText($controller,$function,$e){
        return $controller."(".$function.")_".$e;
    }

    /**
     * ログに出力するフォーマットを作成する。（正常時処理）
     *
     * @param $controller => コントローラーファイルの名前
     * @param $function => 処理の名前
     * @return string => フォーマット
     */
    public function setLogTextSuccess($controller,$function){
        return $controller."(".$function.")_success";
    }

    public function getBelongDepart($user_id){
        $belong_depart = DB::table('belong_departments')->join('users', 'belong_departments.user_id', '=', 'users.id')
            ->join('departments','belong_departments.department_id','=','departments.id')
            ->where('belong_departments.user_id',$user_id)->get();

        return $belong_depart;
    }

    public function getTarget($target_depart_ids){
        DB::table('targets')->join('groups','targets.group_id','=','groups.id')
            ->whereIn('department_id',$target_depart_ids)->get();
    }

    public function getUserInfo($col){
        $user = Auth::user();
        $date = $user->$col;
        return $date;
    }

}
