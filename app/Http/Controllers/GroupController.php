<?php

namespace App\Http\Controllers;

use App\MessageDef;
use App\SystemDef;
use Illuminate\Http\Request;
use App\Group;
use App\BelongGroup;
use App\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    //【やること】
    //グループの登録機能と削除機能の実装。



    /**
     * グループを一覧確認できます
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index (Request $request){
        $sort = $request->sort;
        if($sort == null){
            $sort = 'id';
        }
        //倉庫の情報を取得。ぺジネーションして表示する
        $items = Group::orderBy($sort,'asc') ->paginate(5);
        $param = ['items'=>$items ,'sort' => $sort];
        return view('group.index',$param);
    }

    public function add(Request $request){

    }

    public function create(Request $request){

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function detail(Request $request){
        //変数宣言
        $belong_warehouses = array();
        $i = 0;
        //ログインしていないまたは権限を持っていないユーザをアクセスさせない
        /*後で実装すること*/

       //ソート初期値設定
        $sort = $request->sort;
        if($sort == null){
            $sort ='id';
        }
        //グループを取得
        $group = Group::find($request->id);
        if($group == null){
            return redirect('/group')->with(MessageDef::ERROR, MessageDef::ERROR_NON_ID);
        }
        //グループに所属している倉庫を取得する
        $belongs = BelongGroup::where('group_id',$request->id)->get();
        foreach($belongs as $belong){
            $ids = $belongs[$i]['warehouse_id'];
            $belong_warehouse = Warehouse::find($ids);
            array_push($belong_warehouses,$belong_warehouse);
            $i += 1;
        }
        $param = ['group'=>$group,'belong_warehouses'=>$belong_warehouses];
        //倉庫編集画面を開く
        return view('group.detail',$param);
    }

    public function belong(Request $request){
        $i  = 0;
        $belongs = array();
        //アクセスできるユーザを認証する

        //グループのステータスを取得する
        $groups = Group::find($request->id);
        //倉庫の情報を取得する
        $warehouses = Warehouse::all();
        //グループに所属する倉庫のIDのみを取得！！
        $already_belongs = BelongGroup::where('group_id',$request->id)->get();
        foreach($already_belongs as $already_belong){
            array_push($belongs,$already_belong['warehouse_id']);
        }
        //値をセットする。
        $param = ['groups'=>$groups,'warehouses'=>$warehouses,'belongs'=>$belongs];
        return view ('group.belong',$param);
    }

    public function belonging(Request $request){
        //変数宣言
        //やりたいことはbelong_warehouseテーブルに登録すること！
       $i = 0;
       $belong_group = new BelongGroup;
       $chk = 0;
       $belongs = [];
       DB::beginTransaction();
       $del_belongs = BelongGroup::where('group_id',$request->group_id)->get();
        foreach($del_belongs as $del_belong){
            array_push($belongs,$del_belong['warehouse_id']);
        }
       //チェックボックスにチェックされている倉庫をグループのDBに所属させる時の処理
       foreach($request->warehouse_id as $warehouse_id ){
           $belonged = BelongGroup::where('group_id',$request->group_id)->where("warehouse_id",$warehouse_id)->first();
           if($belonged == null){
               try{
                   $belong_group -> warehouse_id = $warehouse_id;
                   $belong_group -> group_id = $request->group_id;
                   $belong_group->save();
                   DB::commit();
                   $chk += 1;
               }catch(\Exception  $e){
                   return redirect('/home')->with(MessageDef::ERROR, "予想外すぎるエラー");
                   DB::rollBack();
               }
           }else{

           }
       }
       $cnt = "失敗";
       /*
        * $request->warehouse_id ====フォームから送られてきた倉庫ID
        * $belongs====所属している倉庫のID
        * チェック外したら削除する処理はまだ実装していない。
        *
        * [やりたいこと]
        * ①$belongsと$request->warehouse_idを比較する
        * ②$belongsのみにあるIDのレコードを削除する
        * ③共通する項目は無視する。
       */
       //return redirect('/group')->with(MessageDef::SUCCESS, MessageDef::SUCCESS_BELONG_WAREHOUSE);
        $flg = 0;
        $count = count($del_belongs);
        for($i = 0;$i<$count;$i++){
            $result = array_search($del_belongs[$i]['warehouse_id'],$request->warehouse_id);
            if($result == false){
               $del =BelongGroup::where('warehouse_id',$del_belongs[$i]['warehouse_id']);
               $result = $del->delete();
                $flg += 100;
            }else{
                $flg += 1;
            }
        }
       // return redirect('/group')->with(MessageDef::SUCCESS, $request->warehouse_id[1]);
        return redirect('/group')->with(MessageDef::SUCCESS,$flg);
    }

    public function edit(Request $request){
        /*if($warehouse == null){
            return redirect('/warehouse')->with(MessageDef::ERROR, MessageDef::ERROR_NON_ID);
        }*/
        //倉庫編集画面を開く
        $group = Group::find($request->id);
        return view('group.edit',['form'=>$group]);
    }

    public function update(Request $request){
        //グループ編集画面から保存する処理
        $group = Group::find($request->id);
        $id = $request->id;
        $form = $request->all();
        unset($form['_token']);
        DB::beginTransaction();
        try{
            $group ->fill($form)->save();
            DB::commit();
            return redirect('/group')->with(MessageDef::SUCCESS, MessageDef::SUCCESS_EDIT_GROUP);
        } catch (\Exception  $e) {
            //エラー時
            DB::rollBack();
            return redirect("/group/edit/${id}")->with(MessageDef::ERROR, MessageDef:: ERROR_EDIT );
        }
    }




    /**
     * @param $login_chk => ログインしているユーザか否か（null or notnull）
     * @param $auth_user => ログインしているユーザのステータス
     * @return string => エラー時、判定を出力する。
     */
    public function user_chk($login_chk,$auth_user){
        if($login_chk == null){
            //return redirect ('/home')->with(MessageDef::ERROR, MessageDef::ERROR_PERMISSION);
            return 'permission';
        }
        if($auth_user->type == SystemDef::EMPLOYEE) {
            //return redirect('/home')->with(MessageDef::ERROR, MessageDef:: ERROR_TYPE);
            return 'employee';
        }
    }
}
