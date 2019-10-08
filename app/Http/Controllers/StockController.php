<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\ColName;
use App\Department;
use Illuminate\Http\Request;
use App\MessageDef;
use App\Warehouse;
use App\SystemDef;
use App\Form;
use App\FormType;
use App\Select;
use App\User;
use App\Stock;
use App\Classification;
use App\StockHistory;
use App\BelongDepartment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Mockery\Tests\React_WritableStreamInterface;

class StockController extends Controller
{
    /**
     * 機器管理画面を表示する
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request){
        //ユーザの所属している部署を表示する
        $warehouse_id = $this->setValue($request,'warehouse_id','null');

        $date = $this->getUserInfo("id");

        //操作ユーザが所属している部署を全て取得
        $belong_depart = $this->getBelongDepart($date);
        $target_depart_ids = array();
        foreach($belong_depart as $belong){
            array_push($target_depart_ids,$belong->department_id);
        }
        //部署が操作対象にしている倉庫を取得
        $targets = DB::table('targets')->join('warehouses','targets.warehouse_id','=','warehouses.id')
            ->where('department_id',$request->department_id)->get();
        //ソートを設定する。初期値はID順
        $sort = $this->setValue($request,'sort','id');
        $warehouses = Warehouse::all();
        try {
            $items = Warehouse::orderBy($sort, 'asc')->paginate(SystemDef::PAGE_NUMBER);
        }catch(\Exception $e){
            return redirect ('/home')->with(MessageDef::ERROR, MessageDef::ERROR_UNEXPECT);
        }
        //値の受け渡し
        $param = [
            'warehouses'=>$warehouses,
            'items'=>$items,
            'sort'=>$sort,
            'date'=>$date,
            'belong_depart'=>$belong_depart,
            'targets' =>  $targets,
        ];

        return view('stock.index',$param);
    }

    /**
     * 選択した倉庫に登録されている機器を表示する。
     * 機器は、検索可能。検索もここで行う
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
   public function warehouse(Request $request){
       //変数宣言
       $sort = $request->sort;
       //検索項目のデータを決定する
       $classification_id = $this->getSessionValue($request,'classification_id',$sort,'find_classification_id');
       $serial_number = $this->getSessionValue($request,'serial_number',$sort,'find_serial_number');
       $status_id = $this->getSessionValue($request,'status_id',$sort,'find_status_id');
       //ソートの値を決定する
       $sort = $this->setValue($request,'sort','id');
       if($sort !== 'id'&& $sort !== 'classification_id'&& $sort !== 'serial_number'&& $sort !== 'status'){
           //$sortの値が指定したもの以外である時
           return redirect('/stock')->with(MessageDef::ERROR,MessageDef::ERROR_NON_ID);
       }
       $warehouse = Warehouse::find($request->id);
       if($warehouse == null){
           //情報を取得できない時（エラーケース）
           return redirect('/stock')->with(MessageDef::ERROR,MessageDef::ERROR_NON_ID);
       }

       $classifications = Classification::all();
       try{
           //条件にあうレコードを検索する
           $stocks = Stock::where('warehouse_id',$request->id)
               ->where('classification_id','like',$classification_id)
               ->where('serial_number','like','%'.$serial_number.'%')
               ->where('status','like',$status_id)
               ->orderBy($sort, 'asc')
               ->paginate(SystemDef::PAGE_NUMBER);
       }catch(\Exception $e){
           //エラー時処理
           redirect ('/stock')->with(MessageDef::ERROR, MessageDef::ERROR_UNEXPECT);
       }

       $param = ['warehouse'=>$warehouse,'stocks'=>$stocks,'classifications'=>$classifications,'sort'=>$sort];
       return view('stock.warehouse',$param);
   }

    /**
     * 登録画面を表示する
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
   public function add(Request $request){
       $warehouse = Warehouse::find($request->id);
       $classifications = Classification::where('warehouse_id',$warehouse->id)->get();
       $param = ['warehouse'=>$warehouse,'classifications'=>$classifications];
       return view('stock.add',$param);
   }

    /**
     * 入力されたデータをもとに、新規作成を行う
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
   public function create(Request $request){
       $user_id = auth::user()->id;
       //バリデーションを行う
       $this->validate($request, Stock::$create_rules);
       $stock = new Stock;
       $stock_history = new StockHistory;
       //DBに書き込む処理を開始
       DB::beginTransaction();
       try {
           //stockテーブルにレコードを作成する
           $stock->serial_number = $request->serial_number;
           $stock->classification_id = $request->classification_id;
           $stock->status = SystemDef::INSPECTED;
           $stock->warehouse_id = $request->warehouse_id;
           $stock->save();
           //新規作成したレコードを取得する（serial_numberが一意なのでそれで特定する）
           $new_stock = Stock::where('serial_number',$request->serial_number)->first();
           $stock_id = $new_stock->id;
           //取得したレコードからstock_idを取得し、historyテーブルにレコードを作成する
           $stock_history->user_id = $user_id;
           $stock_history->stock_id = $stock_id;
           $stock_history->status = SystemDef::INSPECTED;
           $stock_history->save();
           DB::commit();
           return redirect('/stock/warehouse/'.$request->warehouse_id)->with(MessageDef::SUCCESS, MessageDef::SUCCESS_CREATE_WAREHOUSE);
       }catch (\Exception  $e) {
           //エラー時
           DB::rollBack();
           return redirect('/stock/warehouse'.$request->warehouse_id.'/add')->with(MessageDef::ERROR, MessageDef:: ERROR_CREATE );
       }
   }

    /**
     * 編集画面を表示する
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
   public function edit(Request $request){
       $warehouse = Warehouse::find($request->id);
       if($warehouse == null){
           //IDで検索を行う。
           //取得失敗時の処理(倉庫の情報を取得)
           return redirect('/stock')->with(MessageDef::ERROR, MessageDef:: ERROR_NON_ID );
       }
       $stock = Stock::find($request->stock_id);
       if($stock == null){
           //機器IDで検索を行う。
           //取得失敗時の処理
           return redirect('/stock/warehouse/'.$warehouse->id)->with(MessageDef::ERROR, MessageDef:: ERROR_NON_ID );
       }
       try{
           //倉庫IDから分類IDを検索する(機器の情報を取得する)。
           $classifications = Classification::where('warehouse_id',$warehouse->id)->get();
       }catch(\Exception $e){
           //失敗時の処理
           return redirect('/stock/warehouse/'.$warehouse->id)->with(MessageDef::ERROR, MessageDef:: ERROR_UNEXPECT );
       }
       $status_list = array(
           "INSPECTED" => SystemDef::INSPECTED,//検品済
           "CANT_TAKEOUT" => SystemDef::CANT_TAKEOUT,//持出不可
           "TAKING_OUT"=>SystemDef::TAKING_OUT,//持出中
           "INSTALLED" => SystemDef::INSTALLED,//設置済
           "RETURNING"=>SystemDef::RETURNING) ;//返品中
       //値をセットし受け渡す
       $param = ['warehouse'=>$warehouse,"stock"=>$stock,"classifications"=>$classifications,'status_list'=>$status_list];
       return view('stock.edit',$param);
   }

    /**
     * 編集した内容を反映する処理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
   public function update(Request $request)
   {
       //フォームにて入力された情報を取得する
       $stock = Stock::find($request->stock_id);
       $stock_serial = $stock->serial_number;
       $stock_classification_id = $stock->classification_id;
       //バリデーション
       $this->validate($request, $this->rules($stock_serial, $stock_classification_id));
       //編集の対象になるユーザの情報を取得
       DB::beginTransaction();
       try {
           //変更処理
           $stock->serial_number = $request->serial_number;
           $stock->classification_id = $request->classification_id;
           $stock->save();
           DB::commit();
           return redirect("/stock/warehouse/".$request->id)->with(MessageDef::SUCCESS, MessageDef::SUCCESS_EDIT_USER);
       } catch (\Exception $e) {
           //変更処理が失敗した時の処理
           DB::rollBack();
           return redirect("/stock/warehouse/".$request->id."edit/".$request->stock_id)->with(MessageDef::ERROR, MessageDef::ERROR_EDIT);
       }
   }

    /**
     * [update]時に使用するバリデーション。
     * unique設定をカスタマイズするのにCotrollerのほうが都合がよいためこちらに記述する。
     *
     * @param $serial_number => 製造番号が一意であるかを判別。
     * @param $classification => 想定している値であるか判別する。
     * @return array
     */
    protected function rules($serial_number ,$classification)
    {
        return [
            'serial_number' => [
                Rule::unique('stocks', 'serial_number')->whereNot('serial_number', $serial_number),
                'required',
                'max:191'
            ],
            'classification_id' => [
                'string',
                'max:191'
            ]
        ];
    }

    /**
     * 削除処理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request){

        $stock = Stock::find($request->stock_id);
        //削除機器を取得できなかった場合の処理
        if($stock == null){
            $log_text = $this->setLogTextUser('delete','ID is NON');
        }
        //データベースの処理開始
        DB::beginTransaction();
        try{
            $stock->delete();
            DB::commit();
            return redirect('/stock/warehouse/'.$request->id)->with(MessageDef::SUCCESS, MessageDef::SUCCESS_DELETE_USER);
        }catch(\Exception $e) {
            //エラー時
            info($e->getMessage());
            DB::rollBack();
            return redirect('/stock/warehouse/'.$request->id)->with(MessageDef::ERROR, MessageDef::ERROR_DELETE);
        }
    }

    public function status(Request $request){
        $stock = Stock::find($request->stock_id);
        $warehouse = Warehouse::find($request->id);
        $stock_history = StockHistory::where('stock_id',$request->stock_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $param = ['stock'=>$stock,'warehouse'=>$warehouse,'stock_history'=>$stock_history];
        return view('stock.status',$param);
    }

    public function changeStatus(Request $request){
        $stock = Stock::find($request->stock_id);
        $warehouse = Warehouse::find($request->id);
        $status_list = array(
            "INSPECTED" => SystemDef::INSPECTED,//検品済
            "CANT_TAKEOUT" => SystemDef::CANT_TAKEOUT,//持出不可
            "TAKING_OUT"=>SystemDef::TAKING_OUT,//持出中
            "INSTALLED" => SystemDef::INSTALLED,//設置済
            "RETURNING"=>SystemDef::RETURNING) ;//返品中
        $param = ['stock'=>$stock,'warehouse'=>$warehouse,'status_list'=>$status_list];
        return view('stock.change',$param);
    }

    public function updateStatus(Request $request){
        //フォームにて入力された情報を取得する
        $user_id = $this->getUserInfo('id');
        $stock_history = new StockHistory;
        $stock = Stock::find($request->stock_id);
        //編集の対象になるユーザの情報を取得
        DB::beginTransaction();
        try {
            //変更処理
            $stock->status = $request->new_status;
            $stock->save();
            $stock_history->user_id = $user_id;
            $stock_history->stock_id = $request->stock_id;
            $stock_history->status = $request->new_status;
            $stock_history->save();
            DB::commit();
            return redirect("/stock/warehouse/".$request->id)->with(MessageDef::SUCCESS,MessageDef::SUCCESS_EDIT_USER);
        } catch (\Exception $e) {
            //変更処理が失敗した時の処理
            DB::rollBack();
            return redirect("/stock/warehouse/".$request->id."edit/".$request->stock_id)->with(MessageDef::ERROR, MessageDef::ERROR_EDIT);
        }
    }


    /////////////ここからテスト機能になる//////////////////

    /**
     * この関数は作成したテーブルを閲覧するために使用する。
     * 以下の場合はエラーとする
     * ・テーブルが作成されていない場合
     * ・テーブルの取得に失敗した場合
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function table (Request $request){
        //変数宣言
        $warehouse_id = $request->id;
        try {
            //選択した倉庫とその倉庫に対応するテーブルを取得
            $warehouse_name = Warehouse::find($request->id);
            $warehouses = DB::table($warehouse_id . "_warehouses")->get();
            $forms = Form::where('warehouse_id', $request->id)->get();
        }catch(\Exception $e){
            //ない場合、エラーを返す
            Log::error($this->setErrorLog('table(get_table)',$e));
            return redirect('/stock')->with(MessageDef::ERROR, MessageDef::ERROR_NOT_TABLE_VIEW);
        }
        $col_count = count($forms);
        //以降の処理は場合によっては必要ないかもしれない
        try{
            //カラムの状態を取得する
           $pdo = DB::connection()->getPdo();
           $col_dates = $pdo->query("show columns from ".$warehouse_id."_warehouses");
        } catch(PDOException $e){
            //カラムの状態を取得できなかった際にエラーを返す
            Log::error($this->setErrorLog('table(get_col)',$e));
            return redirect('/stock')->with(MessageDef::ERROR, MessageDef::ERROR_NOT_TABLE_VIEW);
        }
        //受け渡す値
        $param  = [
            'warehouses'=>$warehouses,//作成したテーブルに登録したレコード
            'col_dates'=>$col_dates,//取得したカラムのデータ
            'warehouse_id'=>$warehouse_id,//作成したテーブルに対応する倉庫のID
            'warehouse_name'=>$warehouse_name,//
            'forms'=>$forms,
            'col_count'=>$col_count,
        ];
        return view('stock.table',$param);
    }

    /**
     * テーブルの作成画面を開く
     * 登録していない倉庫のテーブルを作成することができない
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tableAdd(Request $request){
        try{
            //テーブルが存在するならばリターンする
            DB::table($request->id."_warehouses")->exists();
            return redirect ('/stock');
        }catch(\Exception $e){
            //テーブルが存在しないならば処理続行
        }
        try {
            //作成されている倉庫を選択しているか
            $warehouse = Warehouse::find($request->id);
            if($warehouse == null){
                return redirect('/stock')->with(MessageDef::ERROR, MessageDef::ERROR_NOT_ADD_TABLE);
            }
        }catch(\Exception $e){
            Log::error($this->setErrorLog('tableAdd',$e));
            return redirect('/stock')->with(MessageDef::ERROR, MessageDef::ERROR_NOT_ADD_TABLE);
        }
         $param = ['warehouse' => $warehouse];//登録してある倉庫の情報
        return view('stock.create_table',$param);
    }


    /**
     * tableAddにて表示された画面で登録項目を入力した後、こちらを使う。
     * こちらでは実際にテーブルの作成を行う
     * テーブルの命名規則は[$warehouse_id_warehouses]。例えば$warehouse_idが2の時、
     * [2_warehouses]テーブルが作成される
     *
     * チェックボックスを作成する際に選択肢が選ばれないと空白の選択肢が登録される。
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function tableCreate(Request $request)
    {
        //変数宣言。Schemaでは変数の受け渡しができないためglobal変数を使用する。
        $warehouse = Warehouse::find($request->id);
        global $table_create_field_type,$table_create_col_name,$warehouse_id,$table_create_unique,$table_create_null,$create_selects;

        $table_create_field_type = $request->field_type;//
        $table_create_col_name= $request->col_name;//カラムの名前

        /*ユニーク制約が選択されたか
        ・Yesならば、ユニーク制約がつく。テーブルでは[chk_unique]に1が入力される
        ・Noならば、重複してもよい（ユニーク制約はつかない）。テーブルでは[chk_unique]に0が入力される
        */
        $table_create_unique = $request->unique;//ユニーク制約

        /*null制約が選択されたか
        ・Yesならば、空白が許されない。テーブルでは[chk_nullable]に0が入力される
        ・Noならば、空白が許される。テーブルでは[chk_nullable]に1が入力される
        */
        $table_create_null = $request->null;//空白制約
        $warehouse_id = $warehouse["id"];
        $create_selects = $request->select;//選択肢
        DB::beginTransaction();
        try {
            //テーブルの作成処理
            Schema::create($GLOBALS['warehouse_id'] . '_warehouses', function (Blueprint $table) {
                $field_types = $GLOBALS['table_create_field_type'];

                $cont = 1;
                $number = 0;
                //作成処理
                $table->increments('id');
                foreach ($field_types as $field_type) {
                    $field_type_name = $this->getFormTypeCol($field_type, "date_type");
                    /*null判定かつ、unique判定をもらった場合の処理*/
                    if ($GLOBALS['table_create_unique'][$number] == "Yes" && $GLOBALS['table_create_null'][$number] == "No") {
                        switch ($field_type_name) {
                            case "integer";
                            case "dateTime";
                                $table->$field_type_name('col_' . $cont)->unique()->nullable();
                                break;
                            case "string":
                                $table->$field_type_name('col_' . $cont, 191)->unique()->nullable();
                                break;
                            default:
                                return redirect('/home');
                        }
                    } //ユニーク判定の処理
                    else if ($GLOBALS['table_create_unique'][$number] == "Yes") {
                        switch ($field_type_name) {
                            case "integer";
                            case "dateTime";
                                $table->$field_type_name('col_' . $cont)->unique();
                                break;
                            case "string":
                                $table->$field_type_name('col_' . $cont, 191)->unique();
                                break;
                            default:
                                return redirect('/home');
                        }
                    } //null判定の処理
                    else if ($GLOBALS['table_create_null'][$number] == "No") {
                        switch ($field_type_name) {
                            case "integer";
                            case "dateTime";
                                $table->$field_type_name('col_' . $cont)->nullable();
                                break;
                            case "string":
                                $table->$field_type_name('col_' . $cont, 191)->nullable();
                                break;
                            default:
                                return redirect('/home');
                        }
                    } //特に制約がない時の処理
                    else {
                        $table->$field_type_name('col_' . $cont);
                    }
                    $number += 1;
                    $cont += 1;
                }
                $table->timestamps();
            });
            //$cntの定義
            $cont = 1;
            $page = 0;
            //フォームテーブルに登録とセレクトテーブルに追加
            foreach ($GLOBALS['table_create_field_type'] as $type) {
                //フォームテーブルの作成
                $forms = new Form;
                $forms->warehouse_id = $GLOBALS['warehouse_id'];
                $forms->col_fictitious_name = "col_" . $cont;
                $forms->form_type_id = $type;
                $forms->form_order = $cont;
                //ユニーク制約
                if ($GLOBALS['table_create_unique'][$page] == "Yes") {
                    $forms->chk_unique = SystemDef::unique;//一意であること
                } else {
                    $forms->chk_unique = SystemDef::not_unique;//重複してもよい
                }
                //null制約
                if ($GLOBALS['table_create_null'][$page] == "No") {
                    $forms->chk_nullable = SystemDef::null_able;//空白でもよい
                } else {
                    $forms->chk_nullable = SystemDef::not_null;//空白を許さない
                }
                $forms->save();
                if ($type == SystemDef::radio_button || $type == SystemDef::check_box) {
                    //作成したレコードのform_type_idが選択肢付きのタイプだった場合に走る処理
                    $form = Form::where('warehouse_id', $GLOBALS['warehouse_id'])->where('col_fictitious_name', "col_" . $cont)->first();
                    $select_list = explode(',', $GLOBALS['create_selects'][$page]);
                    foreach ($select_list as $select) {
                        $select_table = new Select;
                        $select_table->form_id = $form["id"];
                        $select_table->select_value = $select;
                        $select_table->save();
                    }
                }
                $cont += 1;
                $page += 1;
            }
            //$cntの再定義
            $cnt = 1;
            //col_namesテーブルにカラムの名前を登録する
            foreach ($GLOBALS['table_create_col_name'] as $col_name) {
                $form = Form::where('warehouse_id', $GLOBALS['warehouse_id'])->where('col_fictitious_name', "col_" . $cnt)->first();
                Log::debug($form);
                $col_names = new ColName;
                $col_names->form_id = $form["id"];
                $col_names->col_name = $col_name;
                $col_names->save();
                $cnt += 1;
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            Log::error($this->setErrorLog('tableCreate',$e));
            return redirect('/stock')->with(MessageDef::ERROR, MessageDef::ERROR_NOT_CREATE_TABLE);
        }
        return redirect('/stock/table/'.$warehouse_id);
    }

    /**
     * 作成したテーブルを削除する処理
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function tableDelete(Request $request){
        DB::beginTransaction();
        try {
            //テーブルの削除
            Schema::drop($request->id.'_warehouses');
            $forms = Form::where('warehouse_id',$request->id)->get();
            Form::where('warehouse_id', $request->id)->delete();
            foreach ($forms as $form) {
                Log::debug($form);
                //項目名と選択肢も削除。
                Select::where('form_id', $form['id'])->delete();
                ColName::where('form_id', $form['id'])->delete();
                // TODO: 画像フォルダの削除処理も追加すること
            }
            DB::commit();
        }catch(Exception $e){
            Log::debug($e);
            DB::rollBack();
            return redirect("/stock/")->with(MessageDef::ERROR, MessageDef::ERROR_TABLE_DELETE);
        }
        return redirect("/stock/")->with(MessageDef::SUCCESS, MessageDef::SUCCESS_DELETE_DEPARTMENT);
    }


    /**
     *
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addTableData(Request $request){
        $forms = Form::where('warehouse_id',$request->id)->get();
        if($forms == null){
            return redirect('/stock/table/'.$request->id);
        }

        $warehouse = Warehouse::find($request->id);
        if($warehouse == null){
            return redirect('/stock/table/'.$request->id);
        }

        $param = ['forms' =>$forms,'warehouse'=>$warehouse];
        return view('stock.table_data_add',$param);
    }

    /**
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function createTableData(Request $request){
        $forms = Form::where('warehouse_id',$request->warehouse_id)->get();
        $col_count = count($forms);
        //バリデーション
        $validate = $this->makeValidate($request,$col_count,$request->warehouse_id);
        $request->validate($validate);

        $array = array();
        for($i=1;$i<=$col_count;$i++) {
            $pre_col_name = "col_" . $i;
            $form_type = Form::where('warehouse_id',$request->warehouse_id)->where("col_fictitious_name",$pre_col_name)->first();
            if ($form_type["form_type_id"] == SystemDef::check_box) {
                $ans = null;
                foreach ($request->$pre_col_name as $array_box) {
                    $ans .= $array_box.",";
                }
                $ans_array = array($ans);
                $array[$pre_col_name] = $ans_array[0];

            }else if($form_type["form_type_id"] == SystemDef::img){
                $filename = $request->$pre_col_name->store('public/'.$request->warehouse_id.'_warehouse_img');
                $array[$pre_col_name] = basename($filename);

            } else {
                $array[$pre_col_name] = $request->$pre_col_name;
                Log::debug(gettype($array[$pre_col_name]));
            }
        }
        DB::table($request->warehouse_id.'_warehouses')-> insert($array);
        return redirect('/stock/table/'.$request->warehouse_id);
    }


    /**
     * @param $request
     * @param $col_count
     * @param $warehouse_id
     * @return array
     */
    public function makeValidate($request,$col_count,$warehouse_id){
        $array = array();
        for($i = 1;$i<=$col_count;$i++){
           $data = Form::where('warehouse_id',$warehouse_id)->where('col_fictitious_name',"col_".$i)->first();
            switch($data['form_type_id']){
                case 1;
                case 2;
                case 6;
                //DB登録時の文字型がstringの時の処理
                switch($data['chk_unique']){
                    case 0:
                        //ユニーク制約なし
                        switch($data['chk_nullable']){
                            case 0:
                                //ユニーク制約なしかつnullを非許可
                                $array['col_'.$i] = "string|max:255|required";
                                break;
                            case 1:
                                //ユニーク制約なしかつnullを許可する
                                $array['col_'.$i] = "string|max:191|nullable";
                                break;
                        }
                        break;
                    case 1:
                        //ユニーク制約あり
                        switch($data['chk_nullable']){
                            case 0:
                                //ユニーク制約ありかつnull非許可
                                $array['col_'.$i] = "string|unique:".$warehouse_id."_warehouses|max:191|required";
                                break;
                            case 1:
                                //ユニーク制約ありかつnullを許可
                                $array['col_'.$i] = "string|unique:".$warehouse_id."_warehouses|nullable| max:191";
                                break;
                        }
                }
                break;
                case 3;
                case 4;
                //DB登録時の文字型が数字の時
                switch($data['chk_unique']){
                    case 0:
                        //ユニーク制約なし
                        switch($data['chk_nullable']){
                            case 0:
                                //ユニーク制約なしかつnullを非許可
                                $array['col_'.$i] = "integer|digits_between:1,11|required";
                                break;
                            case 1:
                                //ユニーク制約なしかつnullを許可
                                $array['col_'.$i] = "integer|nullable|digits_between:1,11";
                                break;
                        }
                        break;
                    case 1:
                        //ユニーク制約あり
                        switch($data['chk_nullable']){
                            case 0:
                                //ユニーク制約ありかつnullを非許可
                                $array['col_'.$i] = "integer|unique:".$warehouse_id."_warehouses|digits_between:1,11|required";
                                break;
                            case 1:
                                //ユニーク制約ありかつnullを許可
                                $array['col_'.$i] = "integer|unique:".$warehouse_id."_warehouses|nullable|digits_between:1,11";
                                break;
                        }
                }
                break;
                case 5:
                    //飛んでくるデータ配列の時
                    switch($data['chk_unique']){
                        case 0:
                            //ユニーク制約なし
                            switch($data['chk_nullable']){
                                case 0:
                                    //ユニーク制約なしかつnull非許可
                                    $array['col_'.$i] = "max:255|required|array";
                                    break;
                                case 1:
                                    //ユニーク制約なしかつnull許可
                                    $array['col_'.$i] = "max:191|nullable|array";
                                    break;
                            }
                            break;
                        case 1:
                            //ユニーク制約あり
                            switch($data['chk_nullable']){
                                case 0:
                                    //ユニーク制約ありかつnull非許可
                                    $array['col_'.$i] = "unique:".$warehouse_id."_warehouses|max:191|required|array";
                                    break;
                                case 1:
                                    //ユニーク制約ありかつnull許可
                                    $array['col_'.$i] = "unique:".$warehouse_id."_warehouses|nullable| max:191|array";
                                    break;
                            }
                    }
                    break;
                case 8:
                    switch($data['chk_unique']){
                        case 0:
                            //ユニーク制約なし
                            switch($data['chk_nullable']){
                                case 0:
                                    //ユニーク制約なしかつnull非許可
                                    $array['col_'.$i] = "max:255|required|date";
                                    break;
                                case 1:
                                    //ユニーク制約なしかつnull許可
                                    $array['col_'.$i] = "max:191|nullable|date";
                                    break;
                            }
                            break;
                        case 1:
                            //ユニーク制約あり
                            switch($data['chk_nullable']){
                                case 0:
                                    //ユニーク制約ありかつnull非許可
                                    $array['col_'.$i] = "unique:".$warehouse_id."_warehouses|max:191|required|date";
                                    break;
                                case 1:
                                    //ユニーク制約ありかつnull許可
                                    $array['col_'.$i] = "unique:".$warehouse_id."_warehouses|nullable| max:191|date";
                                    break;
                            }
                    }
                    break;
                case 7:
                    switch($data['chk_unique']) {
                        case 0;
                        case 1;
                            //ユニーク制約なし
                            switch ($data['chk_nullable']) {
                                case 0:
                                    //ユニーク制約なしかつnull非許可
                                    //required|file|image|mimes:jpeg,png,jpg,gif|max:2048
                                    $array['col_' . $i] = "required|file|image|mimes:jpeg,png,jpg,gif|max:2048";
                                    break;
                                case 1:
                                    //ユニーク制約なしかつnull許可
                                    $array['col_' . $i] = "required|file|image|mimes:jpeg,png,jpg,gif|max:2048";
                                    break;
                            }
                            break;
                    }
            }
        }
        Log::debug($array);
        return $array;
    }



    public function editTable(Request $request){
        $warehouse = Warehouse::find($request->warehouse_id);
        if($warehouse == null){
            //IDで検索を行う。
            //取得失敗時の処理(倉庫の情報を取得)
            return redirect('/stock')->with(MessageDef::ERROR, MessageDef:: ERROR_NON_ID );
        }
        $warehouse_table = DB::table($request->warehouse_id."_warehouses")->get();
        //$warehouse_table_cols = Form::where('warehouse_id',$request->warehouse_id);
        $warehouse_table_cols = DB::table('forms')->where('warehouse_id',$request->warehouse_id)->get();

        Log::debug($warehouse_table_cols);
        $param = [
            'warehouse'=>$warehouse,
            'warehouse_table'=>$warehouse_table,
            'warehouse_table_cols'=>$warehouse_table_cols
        ];
        return view('stock.table_edit',$param);
    }



    /**
     * @param $id
     * @param $col
     * @return mixed
     */
    public function getFormTypeCol($id,$col){
        $selected_type = FormType::find($id);
        return $selected_type[$col];
    }



    /**
     * @param $function
     * @return string
     */
    public function setSuccessLog( $function)
    {
        return parent::setLogTextSuccess('stock_controller', $function); // TODO: Change the autogenerated stub
    }



    /**
     * @param $function
     * @param $e
     * @return string
     */
    public function setErrorLog($function, $e)
    {
        return parent::setLogText('stock_controller', $function, $e); // TODO: Change the autogenerated stub
    }

}
