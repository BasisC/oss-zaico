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
    public function index(Request $request){
        //$items = Department::all();
        $sort = $request->sort;
        if($sort == null){
            $sort = 'id';
        }
        $items = Department::orderBy($sort,'asc')->paginate(5);
        $param = ['items'=>$items,'sort'=>$sort];
        return view ('department.index',$param);
    }

    public function add(Request $request){
        return view('department.add');
    }

    public function create(Request $request){
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

    public function detail(Request $request){
        $department = Department::find($request->id);
        $user = User::all();
        $belong_list = array();
        $belong_department = BelongDepartment::where('department_id',$request->id)->get();
        foreach($belong_department as $belonged){
            $belong_user = User::find($belonged->user_id);
            array_push($belong_list,$belong_user);
        }
        $param = ['department'=>$department,'user'=>$user,'belong_list'=>$belong_list];
        return view('department.detail',$param);
    }

    public function belong(Request $request){
        $department = Department::find($request->id);
        $users = User::all();
        $belongs = BelongDepartment::where('department_id',$request->id)->get();
        $belong_user_id = array();
        foreach($belongs as $belong){
            array_push($belong_user_id,$belong->user_id);
        }
        $param = ['department'=>$department,'users'=>$users,'belong_user_id'=>$belong_user_id];
        return view('department.belong',$param);
    }

    public function belonging(Request $request){
        $belong_department = new BelongDepartment;
        $user_id = $request->user_id;
        $belong_list = BelongDepartment::where('department_id',$request->group_id)->get();
        $test = 0;

        if($user_id == null){
            return redirect('/user')->with(MessageDef::SUCCESS,"test");
        }

        foreach($belong_list as $belong){
            $chk=array_search($belong->user_id, $user_id);
            if($chk == false){
                try{
                    $test += $belong->user_id;
                    BelongDepartment::where('user_id',$belong->user_id)->delete();
                }catch(\Exception $e){
                    DB::rollBack();
                    return redirect('/department')->with(MessageDef::ERROR,MessageDef::ERROR_UNEXPECT);
                }
            }
        }
        DB::beginTransaction();
        foreach($user_id as $id){
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
}
