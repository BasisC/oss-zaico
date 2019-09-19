<?php

namespace App\Http\Controllers;

use App\Classification;
use App\Group;
use App\MessageDef;
use App\Warehouse;
use App\SystemDef;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClassificationController extends Controller
{
    public function index(Request $request){
        $warehouses = Warehouse::all();
        $sort = $request->sort;
        $warehouse_id = $this->getSessionValue($request,'warehouse_id',$sort,'classification_find_warehouse_id');
        $classification_name = $this->getSessionValue($request,'classification_name',$sort,'classification_find_classification_name');
        if($sort == null){
            $sort = 'classifications.id';
        }
        try {
            $items = DB::table('warehouses')->join('classifications', 'warehouses.id', '=', 'classifications.warehouse_id')
                ->where('warehouse_id', 'like', $warehouse_id)
                ->where('classification_name', 'like', "%" . $classification_name . "%")
                ->orderBy($sort, 'asc')
                ->paginate(SystemDef::PAGE_NUMBER);
        }catch(\Exception $e){
            return redirect('/classification')->with(MessageDef::ERROR,MessageDef::ERROR_ABNORMAL_PARAM);
        }
        $param = ['items'=>$items, 'sort'=>$sort , 'warehouses'=>$warehouses,];
        return view('class.index',$param);
    }

    public function add(Request $request){
        $warehouses = Warehouse::all();
        $param = ['warehouses'=>$warehouses];
        return view('class.add',$param);
    }

    public function create(Request $request){
        $this->validate($request, Classification::$create_rules);
        //倉庫内に同じ分類名を持ちたくない。
        //分類名が重複時trueを返す
        $chk = $this->findDistinct('Classification','warehouse_id','classification_name',$request);
        //true=重複なので、エラーを出力
        if($chk == true){
            return redirect('/classification/add')->with(MessageDef::ERROR,MessageDef::ERROR_NOT_CREATE_CL );
        }
        $classification = new Classification;
        //DBに書き込む処理を開始
        DB::beginTransaction();
        try {
            $classification->classification_name = $request->classification_name;
            $classification->warehouse_id = $request->warehouse_id;
            $classification->address = $request->address;
            $classification->save();
            DB::commit();
            return redirect('/classification')->with(MessageDef::SUCCESS, MessageDef::SUCCESS_CREATE_DEPARTMENT);
        }catch (\Exception  $e) {
            //エラー時
            DB::rollBack();
            return redirect('/classification/add')->with(MessageDef::ERROR, MessageDef:: ERROR_CREATE );
        }
    }

    public function edit(Request $request){
        try {
            $form = Classification::find($request->id);
            if($form == null) {
                //情報を取得できない時（エラーケース）
                return redirect('/classification')->with(MessageDef::ERROR, MessageDef::ERROR_NON_ID);
            }
            $warehouses = Warehouse::all();
            $param = ['form' => $form, 'warehouses' => $warehouses];
            return view('class.edit', $param);
        }catch(\Exception $e){
            return redirect('/classification')->with(MessageDef::ERROR,MessageDef::ERROR_NOT_GET_DB);
        }
    }

    public function update(Request $request){
        $this->validate($request,$this->edit_rules($request));
        $id = $request->id;
        $classification = Classification::find($request->id);
        //倉庫内で同じ分類名を持ちたくない。
        $cl_name_chk = $this->findDistinct('Classification','warehouse_id','classification_name',$request);
        //true = 重複時なのでエラー出力。
        if($cl_name_chk == true){
            return redirect("/classification/edit/${id}")->with(MessageDef::ERROR,MessageDef::ERROR_NOT_CREATE_CL );
        }
        $id = $request->id;
        DB::beginTransaction();
        try{
            $classification->classification_name = $request->classification_name;
            $classification->warehouse_id = $request->warehouse_id;
            $classification->address = $request->address;
            $classification->save();
            DB::commit();
            return redirect('/classification')->with(MessageDef::SUCCESS,MessageDef::SUCCESS_EDIT_DEPARTMENT);
        } catch (\Exception  $e) {
            //エラー時
            DB::rollBack();
            return redirect("/classification/edit/${id}")->with(MessageDef::ERROR, MessageDef:: ERROR_EDIT );
        }
    }


    private function findDistinct($table,$col1,$col2,$request){
        $chk = Classification::where($col1,$request->$col1)->where($col2,$request->$col2)->first();
        if($chk == null){
            return false;
        }else{
            return true;
        }
    }

    protected function edit_rules($request)
    {
        return [
            'classification_name' => [
                'required',
                'string',
            ],
            'address'=>[
                'required',
                'string'
            ],
            'warehouse_id'=>[
                'required',
                'integer'
            ]
        ];
    }

    public function delete(Request $request){
        $classification = Classification::find($request->id);
        //削除部署を取得できなかった場合の処理
        if($classification == null){
            return redirect('/classification')->with(MessageDef::ERROR, MessageDef::ERROR_OLD_DELETE);
        }
        //データベースの登録処理開始
        DB::beginTransaction();
        try{
            $classification->delete();
            DB::commit();
            return redirect('/classification')->with(MessageDef::SUCCESS, MessageDef::SUCCESS_DELETE_CLASSIFICATION);
        }catch(\Exception $e) {
            //エラー時
            info($e->getMessage());
            DB::rollBack();
            return redirect('/classification')->with(MessageDef::ERROR, MessageDef::ERROR_DELETE);
        }
    }

    public function return(Request $request){
        return redirect('/classification')->with(MessageDef::ERROR, MessageDef::ERROR_DELETE);
    }
}
