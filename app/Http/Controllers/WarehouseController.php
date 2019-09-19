<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Warehouse;
use App\SystemDef;
use App\MessageDef;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class WarehouseController extends Controller
{
    /**
     * 登録されている倉庫を表示する
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
	public function index(Request $request ){
        //$sortの初期値を設定する
	    $sort = $this->setValue($request,'col','id');
	    //倉庫の情報を取得。ぺジネーションして表示する
        try {
            $items = Warehouse::orderBy($sort, 'asc')->paginate(5);
        }catch(\Exception $e){
	        return redirect('/warehouse')->with(MessageDef::ERROR,MessageDef::ERROR_UNEXPECT);
        }
	    $param = ['items'=>$items ,'sort' => $sort];
		return view('warehouse.index',$param);
	}

    /**
     * 倉庫の登録画面を開く処理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function add(Request $request){
        //登録画面を開く
	    return view('warehouse.add');
    }

    /**
     * 倉庫を登録をする処理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request){
        //変数宣言
        $user_id = auth::user()->id;
        $this->validate($request, Warehouse::$create_rules);
        $warehouse = new Warehouse;
        //DBに書き込む処理を開始
        DB::beginTransaction();
        try {
            $warehouse->warehouse_name = $request->warehouse_name;
            $warehouse->address = $request->address;
            $warehouse->tel_number = $request->tel_number;
            $warehouse->user_id = $user_id;
            $warehouse->save();
            DB::commit();
            return redirect('/warehouse')->with(MessageDef::SUCCESS, MessageDef::SUCCESS_CREATE_WAREHOUSE);
        }catch (\Exception  $e) {
            //エラー時
            DB::rollBack();
            return redirect('/warehouse/add')->with(MessageDef::ERROR, MessageDef:: ERROR_CREATE );
        }

    }

    /**
     * 編集画面を表示する
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit(Request $request){
        $warehouse = Warehouse::find($request->id);
        //もし、倉庫の値を取得できなかったら、エラー出力
        if($warehouse == null){
            return redirect('/warehouse')->with(MessageDef::ERROR, MessageDef::ERROR_NON_ID);
        }
        //倉庫編集画面を開く
	    return view('warehouse.edit',['form'=>$warehouse]);
    }

    /**
     * 編集したデータをDBに反映させる
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request){
        //ログインしていないまたは権限を持っていないユーザをアクセスさせない
        $edit_warehouse_id = $request->id;
        $this->validate($request,$this->edit_rules($request -> warehouse_name,$request -> address, $request -> tel_number));

        //編集する倉庫を表示する
        $warehouse = Warehouse::find( $edit_warehouse_id);
        $form = $request->all();
        unset($form['_token']);
        DB::beginTransaction();
	    try{
	        //正常ケース
	        $warehouse ->fill($form)->save();
            DB::commit();
            return redirect('/warehouse')->with(MessageDef::SUCCESS, MessageDef::SUCCESS_EDIT_WAREHOUSE);
        } catch (\Exception  $e) {
	        //エラー時
            DB::rollBack();
        return redirect("/warehouse/edit/${edit_warehouse_id}")->with(MessageDef::ERROR, MessageDef:: ERROR_EDIT );
	    }
    }

    /**
     * 倉庫を削除する
     * @param Request $request
     * @param $id =>　削除する倉庫のID
     * @return \Illuminate\Http\RedirectResponse
     */

    public function delete(Request $request ,$id){
        $warehouse = Warehouse::find($id);
        //削除倉庫を取得できなかった場合の処理
        if($warehouse == null){
            return redirect('/home')->with(MessageDef::ERROR, MessageDef::ERROR_OLD_DELETE);
        }
        //データベースの登録処理開始
        DB::beginTransaction();
        try{
            $warehouse->delete();
            DB::commit();
            return redirect('/warehouse')->with(MessageDef::SUCCESS, MessageDef::SUCCESS_DELETE_WAREHOUSE);
        }catch(\Exception $e) {
            //エラー時
            info($e->getMessage());
            DB::rollBack();
            return redirect('/warehouse')->with(MessageDef::ERROR, MessageDef::ERROR_DELETE);
        }
    }

    /**
     * アクセスするユーザが適切か判定する
     * @param $login_chk => ユーザのログイン状態がわかる
     * @param $auth_user => ログインしているユーザのステータスを知る
     * @return string => エラーの内容を出力する
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

    protected function edit_rules($warehouse_name ,$address,$tel_number)
    {
        return [
            'warehouse_name' => [
                Rule::unique('warehouses', 'warehouse_name')->whereNot('warehouse_name', $warehouse_name),
                'required',
                'string',
                'max:191'
            ],
            'address' => [
                Rule::unique('warehouses','address')->whereNot('address',$address),
                'required',
                'max:191',
            ],
            'tel_number' =>[
                Rule::unique('warehouses','tel_number')->whereNot('tel_number',$tel_number),
                'digits_between:1,191',
                'required',
            ]
        ];
    }

    public function return(Request $request){
        return redirect('/warehouse')->with(MessageDef::ERROR, MessageDef::ERROR_DELETE);
    }


}
