<?php

namespace App\Http\Controllers;

use App\MessageDef;
use App\SystemDef;
use App\Warehouse;
use Illuminate\Http\Request;
use App\Department;
use App\User;
use App\Target;
use App\BelongDepartment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * 部署管理画面を表示する
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request){
        //ソートする項目が選択されていなければ、IDで昇順に並べる
        $sort = $this->setValue($request,'sort','id');
        //ぺジネーション設定してデータを取得
        try {
            $items = Department::orderBy($sort, 'asc')->paginate(SystemDef::PAGE_NUMBER);
        }catch(\Exception $e){
            return redirect ('/department')->with(MessageDef::ERROR, MessageDef::ERROR_UNEXPECT);
        }
        $param = ['items'=>$items,'sort'=>$sort];
        return view ('department.index',$param);
    }

    public function add(Request $request){
        //登録画面を表示する
        return view('department.add');
    }

    /**
     * 部署を新しく登録する
     * 【正常ケース】
     * ・addにて入力したデータで新しく部署を登録する
     *
     * 【エラーケース】
     * ・部署IDに本来存在しないものを選択した => 「/department」に遷移。「ERROR_UNEXPECT」を出力
     * ・バリデーションエラー
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(Request $request){
        //登録に必要なデータを取得する
        $user_id = auth::user()->id;
        $this->validate($request, Department::$create_rules);
        $department = new Department;
        //DBに書き込む処理を開始
        DB::beginTransaction();
        try {
            $department->department_name = $request->department_name;
            $department->user_id = $user_id;
            $department->save();
            DB::commit();
            return redirect('/department')->with(MessageDef::SUCCESS, MessageDef::SUCCESS_CREATE_DEPARTMENT);
        }catch (\Exception  $e) {
            //エラー時
            DB::rollBack();
            return redirect('/department/add')->with(MessageDef::ERROR, MessageDef:: ERROR_CREATE );
        }

    }

    /**
     * 所属するユーザを表示する画面
     * 【正常ケース】
     * ・選択した部署に所属しているユーザを表示する。
     *
     * 【エラーケース】
     * ・部署IDに本来存在しないものを選択した => 「/department」に遷移。「ERROR_UNEXPECT」を出力
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request){
        //必要なデータを取得する
        $department = Department::find($request->id);
        //存在しないIDを取得しようとした時の処理
        if($department == null){
            return redirect('/department')->with(MessageDef::ERROR,MessageDef::ERROR_NON_ID);
        }
        $sort = $request->sort;
        $name = $this->getSessionValue($request,'name',$sort,'find_name_department_detail');
        if($sort == null){
            $sort = 'user_id';
        }
        try {
            //joinを使ってテーブルを結合する
            $belong_department = DB::table('users')->join('belong_departments', 'users.id', '=', 'belong_departments.user_id')
                ->where('department_id', $request->id)
                ->where('name', 'like', "%" . $name . "%")
                ->orderBy($sort, 'asc')
                ->paginate(SystemDef::PAGE_NUMBER);
        }catch(\Exception $e){
            //ありえそうなエラーとしては、ソートにしらない項目を指定される、取得ができなかったパターン
            return redirect("/department/detail/".$request->id)->with(MessageDef::ERROR,MessageDef::ERROR_UNEXPECT);
        }
        $param = ['department'=>$department,'belong_department'=>$belong_department,'sort'=>$sort,'name'=>$name];
        return view('department.detail',$param);
    }


    /**
     * 所属画面を表示する
     * 【正常ケース】
     * ・選択した部署に所属しているユーザにチェックをつけ、所属していないユーザにはチェックをつけないで表示する。
     *
     * 【エラーケース】
     * ・部署IDに本来存在しないものを選択した => 「/department」に遷移。「ERROR_UNEXPECT」を出力
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function belong(Request $request){
        //所属に必要なデータを取得する
        $belong_ids = array();
        $department = Department::find($request->id);
        if($department == null){
            return redirect('/department')->with(MessageDef::ERROR,MessageDef::ERROR_NON_ID);
        }
        $users = User::all();
        $belongs = BelongDepartment::where('department_id',$request->id)->get();
        //既に所属しているユーザのユーザIDを取得する。
        //取得したユーザIDはチェックボックスの初期値としてチェックするか否かを判定するために使用する。
        foreach($belongs as $belong){
            array_push($belong_ids,$belong['user_id']);
        }
        //取得したデータをセットし送信する
        $param = ['department'=>$department,'users'=>$users,'belong_ids'=>$belong_ids];
        return view('department.belong',$param);
    }

    /**
     * 部署にユーザを所属させる
     * 【正常ケース】
     * ・選択したユーザを「所属」させる。チェックを外したユーザを「所属から外す」。
     * 　=> 「/department」に遷移。「SUCCESS_TARGET」を出力。
     *
     * 【エラーケース】
     * ・部署IDに本来存在しないものを選択した => 「/department」に遷移。「ERROR_UNEXPECT」を出力
     * ・所属処理を失敗した => 「/department」に遷移。「ERROR_UNEXPECT」を出力
     * ・所属の解除を失敗した => 「/department」に遷移。「ERROR_UNEXPECT」を出力
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function belonging(Request $request){
       //所属に必要なデータを取得する
        /*user_id => 所属画面のチェックボックスにチェックされたユーザID
         *already_belong_users => 部署に所属しているユーザのデータ
         * already_belong_user_ids => 部署に所属しているユーザのID
         */
        $user_ids = $request->user_id;
        $already_belong_users = BelongDepartment::where('department_id',$request->department_id)->get();
        $already_belong_user_ids = array();
        //部署に所属しているユーザのIDを配列に格納する
        foreach($already_belong_users as $already_belong_user){
            array_push($already_belong_user_ids,$already_belong_user['user_id']);
        }

        if($user_ids == null) {
            DB::beginTransaction();
            try {//チェックが一つもついていない場合の処理。選択された部署に所属しているユーザの所属を解除する
                BelongDepartment::where('department_id', $request->department_id)->delete();
                DB::commit();
            } catch (\Exception $e) {
                //エラー時
                DB::rollBack();
                return redirect('/department')->with(MessageDef::ERROR, MessageDef::ERROR_UNEXPECT);
            }
            return redirect('/department')->with(MessageDef::SUCCESS,MessageDef::SUCCESS_BELONG_DEPARTMENT);
        }else{
            //チェックがついていないユーザのみ削除する
            DB::beginTransaction();
            foreach( $already_belong_user_ids as $already_belong_user_id){
                $chk = in_array($already_belong_user_id, $user_ids,true);
               try {
                   if ($chk == false) {
                       BelongDepartment::where('user_id', $already_belong_user_id)->where('department_id', $request->department_id)->delete();
                   }
               }catch (\Exception $e) {
                   //エラー時
                   DB::rollBack();
                   return redirect('/department')->with(MessageDef::ERROR, MessageDef::ERROR_UNEXPECT);

               }
            }
        }
        //チェックがついているユーザの所属処理
        foreach($user_ids as $user_id) {
            $already_belong = BelongDepartment::where('department_id', $request->department_id)->where('user_id', $user_id)->first();
            if ($already_belong == null) {
                try {
                    $belong_user = new BelongDepartment;
                    $belong_user->department_id = $request->department_id;
                    $belong_user->user_id = $user_id;
                    $belong_user->save();
                }catch (\Exception $e) {
                    //エラー時
                    DB::rollBack();
                    return redirect('/department')->with(MessageDef::ERROR, MessageDef::ERROR_UNEXPECT);
                }
            }
        }
        DB::commit();
        return redirect('/department')->with(MessageDef::SUCCESS,MessageDef::SUCCESS_BELONG_DEPARTMENT);
    }

    /**
     * 部署の編集画面を表示する
     * [エラーケース]
     * ・編集する部署の情報を取得できない時　=> 「/department」に戻る。エラーを表示する
     * ・編集する部署の情報を取得できたが、中身が「null」の時 => 「/department」に戻る。エラーを表示する
     *
     * [正常ケース]
     * ・編集する部署の情報を取得し、中身がある時 => [/department/edit/{department_id}]に遷移する
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit(Request $request){
        try{//編集する部署の情報を取得する
            $form = Department::find($request->id);
            if($form == null){
                //情報を取得できない時（エラーケース）
                return redirect('/department')->with(MessageDef::ERROR,MessageDef::ERROR_NON_ID);
            }else{
                //情報を取得できた時（正常ケース）
                return view('department.edit',['form'=>$form]);
            }
        }catch(\Exception $e){
            //編集する部署情報の取得に失敗した時
            return redirect('/department')->with(MessageDef::ERROR,MessageDef::ERROR_NOT_GET_DB);
        }

    }

    /**
     * 編集画面にて入力した情報をDBに反映する処理
     *
     * 【エラーケース】
     * ・バリデーションエラー => 「/detail/edit/{department_id}」に戻る
     * ・更新処理の失敗 => 「/detail/edit/{department_id}」に戻る。エラー「ERROR_EDIT」を出力。
     *
     * 【正常ケース】
     * ・バリデーションをクリアし、更新処理成功した場合 => 「/detail」に遷移する。「SUCCESS_EDIT_DEPARTMENT」を出力する
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request){
        $this->validate($request,$this->edit_rules($request->department_name));
        $department = Department::find($request->id);
        $id = $request->id;
        DB::beginTransaction();
        try{
            $department->department_name = $request->department_name;
            $department->save();
            DB::commit();
            return redirect('/department')->with(MessageDef::SUCCESS,MessageDef::SUCCESS_EDIT_DEPARTMENT);
        } catch (\Exception  $e) {
            //エラー時
            DB::rollBack();
            return redirect("/department/edit/${id}")->with(MessageDef::ERROR, MessageDef:: ERROR_EDIT );
        }
    }

    /**
     * 選択した部署を削除する
     *
     * 【エラーケース】
     * ・削除する部署を取得できなかった => 「/department」に遷移。エラー「ERROR_OLD_DELETE」を出力する
     * ・部署の削除に失敗した => 「/department」に遷移。エラー「ERROR_DELETE」を出力する
     *
     * 【正常ケース】
     *・削除する部署の取得に成功し削除に成功する =>　「/department 」に遷移。削除完了「SUCCESS_DELETE_DEPARTMENT」を出力する。
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request){
        $department = Department::find($request->id);
        //削除部署を取得できなかった場合の処理
        if($department == null){
            return redirect('/department')->with(MessageDef::ERROR, MessageDef::ERROR_OLD_DELETE);
        }
        //データベースの登録処理開始
        DB::beginTransaction();
        try{
            $department->delete();
            DB::commit();
            return redirect('/department')->with(MessageDef::SUCCESS, MessageDef::SUCCESS_DELETE_DEPARTMENT);
        }catch(\Exception $e) {
            //エラー時
            info($e->getMessage());
            DB::rollBack();
            return redirect('/department')->with(MessageDef::ERROR, MessageDef::ERROR_DELETE);
        }
    }

    /**
     * update時のバリデーションルール
     * @param $department_name
     * @return array
     */
    protected function edit_rules($department_name )
    {
        return [
            'department_name' => [
                Rule::unique('departments', 'department_name')->whereNot('department_name', $department_name),
                'required',
                'string',
                'max:191'
            ]
        ];
    }

    /**
     *操作対象になっている部署を表示する
     *
     * 【エラーケース】
     * ・存在しない部署のIDを選択する => 「/department」に遷移。エラー「ERROR_NON_ID」を出力する
     * ・部署の操作対象になっているグループの取得に失敗する => 「/department」遷移。エラー「ERROR_UNEXPECT」を出力する
     *
     * 【正常ケース】
     * ・存在している部署のIDを選択肢、操作対象になっているグループの取得に成功する
     *    => 「/department/target_list/{department_id}」に遷移する。表示する
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function targetList(Request $request){
        //部署を取得する
        $department = Department::find($request->id);
        /*if($department == null){
            //存在しない部署IDを選択した場合の処理。
            return redirect('/department')->with(MessageDef::ERROR,MessageDef::ERROR_NON_ID);
        }*/
        $sort = $this->setValue($request,'sort','warehouse_id');

       /* try {*/
            //正常ケース
            $targets = DB::table('warehouses')->join('targets', 'warehouses.id', '=', 'targets.warehouse_id')
                ->where('department_id',$request->id)
                ->orderBy($sort, 'asc')
                ->paginate(SystemDef::PAGE_NUMBER);
        /*}catch(\Exception $e){*/
            //情報の取得に失敗したケース
           // return redirect('/department/target_list/'.$request->id)->with(MessageDef::ERROR, MessageDef::ERROR_UNEXPECT);
        //}
        //取得した情報をセットしviewに送る
        $param = ['department'=>$department,'sort'=>$sort,'targets'=>$targets];
        return view('department.warehouse_detail',$param);
    }

    /**
     * 操作対象にする画面を表示する
     * 【エラーケース】
     * ・存在しない部署のIDを選択した => 「/department」に遷移。エラー「ERROR_NON_ID」を出力する
     *
     * 【正常ケース】
     * ・操作対象にする画面を表示する
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function target(Request $request){
        //所属に必要なデータを取得する
        $department = Department::find($request->id);
        if($department == null){
            return redirect('/department')->with(MessageDef::ERROR,MessageDef::ERROR_NON_ID);
        }
        //全てのグループ
        $warehouses = Warehouse::all();
        //操作対象になっているグループのID
        $targets = Target::where('department_id',$request->id)->get();
        $target_warehouses = array();
        foreach($targets as $target){
            //操作対象になっているグループIDを配列に入れる。（array_searchを行うため）
            array_push($target_warehouses,$target['warehouse_id']);
        }
        //取得したデータをセットし送信する
        $param = ['department'=>$department,'warehouses'=>$warehouses,'target_warehouses'=>$target_warehouses];
        return view('department.target',$param);
    }

    /**
     * 操作対象を変更を行う
     * 【正常ケース】
     * ・選択したグループを「操作対象」にする。チェックを外したグループを「操作対象から外す」。
     * 　=> 「/department」に遷移。「SUCCESS_TARGET」を出力。
     *
     * 【エラーケース】
     * ・部署IDに本来存在しないものを選択した => 「/department」に遷移。「ERROR_UNEXPECT」を出力
     * ・操作対象にすることを失敗した => 「/department」に遷移。「ERROR_UNEXPECT」を出力
     * ・操作対象解除を失敗した => 「/department」に遷移。「ERROR_UNEXPECT」を出力
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function targeting(Request $request)
    {
        //操作対象に必要なデータを取得する
        /*group_ids => 操作対象画面のチェックボックスにチェックされたグループID
         *already_target_groups => 操作対象になっているグループのデータ
         *already_targets_group_idsb=> 操作対象になっているグループのID
         */
        $warehouse_ids = $request->warehouse_id;
        $already_target_warehouses = Target::where('department_id',$request->department_id)->get();
        $already_target_warehouses_ids = array();
        Log::debug($already_target_warehouses);

        //操作対象になっているグループのIDを配列に格納する
        foreach($already_target_warehouses as $already_target_warehouse){
            array_push( $already_target_warehouses_ids , $already_target_warehouse['warehouse_id']);
        }
        if($warehouse_ids == null) {
            //チェックが一つもついていない場合
            DB::beginTransaction();
            try {
                //全ての倉庫を操作対象から外す
                Target::where('department_id', $request->department_id)->delete();
                DB::commit();
            } catch (\Exception $e) {
                //エラー時
                DB::rollBack();
                return redirect('/department')->with(MessageDef::ERROR, MessageDef::ERROR_UNEXPECT);
            }
            return redirect('/department')->with(MessageDef::SUCCESS, MessageDef::SUCCESS_TARGET);
        }else{
            //一つでもチェックが入っている場合
            DB::beginTransaction();
            foreach( $already_target_warehouses_ids as $already_target_warehouse_id){
                $chk = in_array($already_target_warehouse_id, $warehouse_ids,true);
                if($chk == false){
                    try{//「操作対象 => 操作対象から外す」処理
                        Target::where('warehouse_id', $already_target_warehouse_id)->where('department_id',$request->department_id)->delete();
                    }catch(\Exception $e){
                        //エラー時処理
                        DB::rollBack();
                        return redirect('/department')->with(MessageDef::ERROR, MessageDef::ERROR_UNEXPECT);
                    }
                }
            }
        }
        //グループを操作対象にする処理
        foreach($warehouse_ids as $warehouse_id) {
            $already_target = Target::where('department_id',$request->department_id)->where('warehouse_id',$warehouse_id)->first();
            if($already_target == null) {
                //既に登録されている場合は登録処理を行わない
                try {
                    $already_target = new Target;
                    $already_target->department_id = $request->department_id;
                    $already_target->warehouse_id = $warehouse_id;
                    $already_target->save();
                }catch(\Exception $e) {
                    //エラー時処理
                    DB::rollBack();
                    return redirect('/department')->with(MessageDef::ERROR, MessageDef::ERROR_UNEXPECT);
                }
            }
        }
        DB::commit();
        return redirect('/department')->with(MessageDef::SUCCESS,MessageDef::SUCCESS_TARGET);
    }

    /**
     * department/delete/{department_id}にアクセス時使用。
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function return(Request $request){
        return redirect('/department')->with(MessageDef::ERROR, MessageDef::ERROR_DELETE);
    }
}

