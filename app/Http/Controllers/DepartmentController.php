<?php

namespace App\Http\Controllers;

use App\MessageDef;
use App\SystemDef;
use Illuminate\Http\Request;
use App\Department;
use App\User;
use App\BelongDepartment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder  ;

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
        $sort = $request->sort;
        if($sort == null){
            $sort = 'id';
        }
        //ぺジネーション設定してデータを取得
        $items = Department::orderBy($sort,'asc')->paginate(5);
        $param = ['items'=>$items,'sort'=>$sort];
        return view ('department.index',$param);
    }





    public function add(Request $request){
        //登録画面を表示する
        return view('department.add');
    }

    /**
     * 部署を新しく登録する
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
            return redirect('/department')->with(MessageDef::SUCCESS, MessageDef::SUCCESS_CREATE_WAREHOUSE);
        }catch (\Exception  $e) {
            //エラー時
            DB::rollBack();
            return redirect('/department/add')->with(MessageDef::ERROR, MessageDef:: ERROR_CREATE );
        }

    }
    /**
     * 所属するユーザを表示する画面
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request){
        //必要なデータを取得する
        $department = Department::find($request->id);
        $belong_department = BelongDepartment::where('department_id',$request->id)->get();
        $param = ['department'=>$department,'belong_department'=>$belong_department];
        return view('department.detail',$param);
    }

    /**
     * 所属画面を表示する
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function belong(Request $request){
        //所属に必要なデータを取得する
        $belong_ids = array();
        $department = Department::find($request->id);
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
     * 部署に新しくユーザを所属させる
     * 部署に所属しているユーザの所属を外す
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function belonging(Request $request){
       //所属に必要なデータを取得する
        /*user_id => 所属画面のチェックボックスにチェックされたユーザID
         *belong_list => 部署に所属しているユーザのデータ
         */
        $user_id = $request->user_id;
        $belong_list = BelongDepartment::where('department_id',$request->group_id)->get();
        //部署所属画面にて、どのユーザにもチェックがついていない場合（こちらは全てのチェックが外れている時）
        //処理としては、部署に所属していたユーザ全ての所属を解除する
        if($user_id == null){
            foreach($belong_list as $belong){
                try {
                    DB::beginTransaction();
                    BelongDepartment::where('user_id', $belong->user_id)->delete();
                    DB::commit();
                }catch(\Exception $e){
                    //エラー時
                    DB::rollBack();
                    return redirect('/department')->with(MessageDef::ERROR,MessageDef::ERROR_UNEXPECT);
                }
            }
            return redirect('/department')->with(MessageDef::SUCCESS,MessageDef::SUCCESS_BELONG_DEPARTMENT);
        }else{
            //チェックボックスにチェックがついていたユーザの、チェックを外した時の処理。（こちらは一つでもチェックがついている時）
            //処理としてはチェックを外したユーザの所属を解除する
            foreach($belong_list as $belong){
                DB::beginTransaction();
                //所属済のユーザとチェックを外したユーザのIDを特定する
                $chk=array_search($belong->user_id, $user_id);
                //もし所属済リストの中にユーザIDがあるかつ、送信された値にユーザIDがなかった時、削除
                if($chk == false){
                    try{
                        BelongDepartment::where('user_id',$belong->user_id)->delete();
                        DB::commit();
                    }catch(\Exception $e){
                        DB::rollBack();
                        return redirect('/department')->with(MessageDef::ERROR,MessageDef::ERROR_UNEXPECT);
                    }
                }
            }
        }
        //まだ登録されていないユーザだった場合の処理。
        foreach($user_id as $id){
            DB::beginTransaction();
            $belong_department = new BelongDepartment;
            $belonged = BelongDepartment::where('department_id',$request->group_id)->where("user_id",$id)->first();
            if($belonged == null){
                try{
                    $belong_department -> department_id = $request->group_id;
                    $belong_department -> user_id = $id;
                    $belong_department->save();
                    DB::commit();
                }catch(\Exception $e){
                    DB::rollBack();
                    return redirect('/department')->with(MessageDef::ERROR,MessageDef::ERROR_UNEXPECT);
                }
            }
        }
        return redirect('/department')->with(MessageDef::SUCCESS,MessageDef::SUCCESS_BELONG_DEPARTMENT);
    }

    public function edit(Request $request){
        $department = Department::find($request->id);
        return view('department.edit',['form'=>$department]);
    }

    public function update(Request $request){

    }
}
