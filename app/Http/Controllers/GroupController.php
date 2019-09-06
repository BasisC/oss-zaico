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
use Illuminate\Validation\Rule;

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
        return view('group.add');
    }

    public function create(Request $request){
        $user_id = auth::user()->id;
        $this->validate($request, Group::$create_rules);
        $group = new Group;
        //DBに書き込む処理を開始
        DB::beginTransaction();
        try {
            $group -> group_name = $request ->group_name;
            $group -> user_id = $user_id;
            $group->save();
            DB::commit();
            return redirect('/group')->with(MessageDef::SUCCESS, MessageDef::SUCCESS_CREATE_WAREHOUSE);
        }catch (\Exception  $e) {
            //エラー時
            DB::rollBack();
            return redirect('/group/add')->with(MessageDef::ERROR, MessageDef:: ERROR_CREATE );
        }
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
        $belongs = array();
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

        /*user_id => 所属画面のチェックボックスにチェックされたユーザID
         *already_belong_users => 部署に所属しているユーザのデータ
         * already_belong_user_ids => 部署に所属しているユーザのID
         */
        $warehouse_ids = $request->warehouse_id;
        $already_belong_groups = BelongGroup::where('group_id',$request->group_id)->get();
        $already_belong_warehouse_ids = array();
        //部署に所属しているユーザのIDを配列に格納する
        foreach($already_belong_groups as $already_belong_group){
            array_push($already_belong_warehouse_ids,$already_belong_group['warehouse_id']);
        }
        //チェックが一つもついていないときの処理
        if($warehouse_ids == null) {
            DB::beginTransaction();
            try {//チェックが一つもついていない場合の処理。選択された部署に所属しているユーザの所属を解除する
                BelongGroup::where('group_id', $request->group_id)->delete();
                DB::commit();
            } catch (\Exception $e) {
                //エラー時
                DB::rollBack();
                return redirect('/gourp')->with(MessageDef::ERROR, MessageDef::ERROR_UNEXPECT);
            }
            return redirect('/group')->with(MessageDef::SUCCESS,MessageDef::SUCCESS_BELONG_DEPARTMENT);
        }else {
            //チェックが一つ以上ついているときの処理
            DB::beginTransaction();
            foreach ($already_belong_warehouse_ids as $already_belong_warehouse_id) {
                $chk = in_array($already_belong_warehouse_id, $warehouse_ids, true);
              //  try {
                    if ($chk == false) {
                        BelongGroup::where('warehouse_id', $already_belong_warehouse_id)->where('group_id', $request->group_id)->delete();
                    }
               // } catch (\Exception $e) {
                    //エラー時
                //    DB::rollBack();
                 //   return redirect('/department')->with(MessageDef::ERROR, MessageDef::ERROR_UNEXPECT);
                //}
            }
        }
        //チェックボックスにチェックがついている倉庫をグループに所属させる処理
        foreach($warehouse_ids as $warehouse_id ) {
            $already_belong = BelongGroup::where('group_id', $request->group_id)->where('warehouse_id', $warehouse_id)->first();
            if ($already_belong == null) {
                try {
                    $belong_warehouse = new BelongGroup;
                    $belong_warehouse->warehouse_id = $warehouse_id;
                    $belong_warehouse->group_id = $request->group_id;
                    $belong_warehouse->save();
                }catch (\Exception $e) {
                    //エラー時
                    DB::rollBack();
                    return redirect('/group')->with(MessageDef::ERROR, MessageDef::ERROR_UNEXPECT);
                }
            }
        }
        DB::commit();
        return redirect('/group')->with(MessageDef::SUCCESS,MessageDef::SUCCESS_BELONG_DEPARTMENT);
    }



    public function edit(Request $request){
        $group = Group::find($request->id);
        if($group == null){
            return redirect('/group')->with(MessageDef::ERROR, MessageDef::ERROR_NON_ID);
        }
        //倉庫編集画面を開く
        $group = Group::find($request->id);
        return view('group.edit',['form'=>$group]);
    }

    public function update(Request $request){
        //グループ編集画面から保存する処理
        $group = Group::find($request->id);
        $group_name = $request -> group_name;
        $this->validate($request,$this->edit_rules($group_name));
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

    protected function edit_rules($group_name)
    {
        return [
            'group_name' => [
                Rule::unique('groups', 'group_name')->whereNot('group_name', $group_name),
                'required',
                'string',
                'max:191'
            ]
        ];
    }

    /**
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request){

        $group = Group::find($request->id);
        //削除ユーザを取得できなかった場合の処理
        if($group == null){
            return redirect('/group')->with(MessageDef::ERROR, MessageDef::ERROR_OLD_DELETE);
        }
        //データベースの登録処理開始
        DB::beginTransaction();
        try{
            $group->delete();
            DB::commit();
            return redirect('/group')->with(MessageDef::SUCCESS, MessageDef::SUCCESS_DELETE_GROUP);
        }catch(\Exception $e) {
            //エラー時
            info($e->getMessage());
            DB::rollBack();
            return redirect('/group')->with(MessageDef::ERROR, MessageDef::ERROR_DELETE);
        }
    }

    public function return(Request $request){
        return redirect('/group')->with(MessageDef::ERROR, MessageDef::ERROR_DELETE);
    }
}
