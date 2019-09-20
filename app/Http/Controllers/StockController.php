<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MessageDef;
use App\Warehouse;
use App\SystemDef;
use App\User;
use App\Stock;
use App\Classification;
use App\StockHistory;
use App\BelongDepartment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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
            Log::error($log_text);
            return redirect('/stock/warehouse/'.$request->id)->with(MessageDef::ERROR, MessageDef::ERROR_OLD_DELETE);
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

    public function updateStatus(Request $request)
    {
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
}
