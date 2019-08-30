<?php

namespace App\Http\Controllers;

use App\MessageDef;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * ユーザ管理画面を表示する
     * ソート対象の項目が決まっていたら、その対象を昇順にソートする。
     * 検索項目が入力されていたら、対象の項目で検索を行い結果を出力する
     * 出力件数は5ページごと。ぺジネーションが可能
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
   public function index(Request $request){
       //変数宣言
       $sort = $request->sort;
       //検索項目のデータを決定する
       $name = self::getValue($request,'name',$sort,'find_name');
       $email = self::getValue($request,'email',$sort,'find_email');
       //ソートの項目が選択されていない場合、IDが選択される
       if($sort == null){
           $sort = 'id';
       }
       //表示するデータを取得
       try{
           //$sortで選択された値を昇順(asc)で並び替え、出力する。
           $items = User::orderBy($sort,'asc')
               //検索項目の条件にあうものを出力する。
               ->where('name','like',"%".$name."%")
               ->where('email','like',"%".$email."%")
               ->paginate(5);
       }catch(\Exception $e){
           //エラー時。ホーム画面に移動しエラー出力
           return redirect('/home')->with(MessageDef::ERROR, MessageDef::ERROR_UNEXPECT);
       }
       //値をセットし渡す
       $param = ['items'=>$items,'sort'=>$sort,'name'=>$name,'email'=>$email];
       return view('user.index',$param);
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
   protected function getValue(Request $request,$caram,$sort,$key){
       if($request->$caram == null){//検索条件が指定されていない時
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
           $request->session()->put($key,$request->$caram);
           return $request->$caram;
       }

   }

    /**
     * 編集画面を開くのに必要な処理。
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
   public function edit(Request $request){
       try{
           $form = User::find($request->id);
           if($form == null){
               return redirect('/user')->with(MessageDef::ERROR,MessageDef::ERROR_NON_ID);
           }else{
               return view('user.edit',['form'=>$form]);
           }
       }catch(\Exception $e){
           return redirect('/user')->with(MessageDef::ERROR,MessageDef::ERROR_NOT_GET_DB);
       }
   }

    /**
     * 選択したユーザの情報を変更する機能
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
   public function update(Request $request){
       //変数宣言
       $user = User::find($request->id);
       $userName = $user->name;
       $userEmail = $user->email;
       //バリデーション
       $this->validate($request,self::rules($userName,$userEmail));
       //編集の対象になるユーザの情報を取得
       $user = User::find($request->id);
       DB::beginTransaction();
       try {
           //変更処理
           //パスワード項目に値が入力されている時のみ、処理を実行する
           if ($request->password !== null) {
               $user->password = Hash::make($request->password);
           }
           $user->name = $request->name;
           $user->email = $request->email;
           $user->per_department_create = self::setValue($request->per_department_create);
           $user->per_department_update = self::setValue($request->per_department_update);
           $user->per_department_delete = self::setValue($request->per_department_delete);
           $user->per_group_create = self::setValue($request->per_group_create);
           $user->per_group_update = self::setValue($request->per_group_update);
           $user->per_group_delete = self::setValue($request->per_group_delete);
           $user->save();
           DB::commit();
           return redirect("/user")->with(MessageDef::SUCCESS,MessageDef::SUCCESS_EDIT_USER);
       }catch(\Exception $e){
           //変更処理が失敗した時の処理
           DB::rollBack();
           return redirect("/user/edit/{id}")->with(MessageDef::ERROR,MessageDef::ERROR_EDIT);
       }
   }

    /**
     * [update]にて使用。
     * セットした値$valueが1の時、1を返す。
     * $valueの値が空白の場合は0を返す
     * @param $value
     * @return int
     */
    protected function setValue($value){
        if($value == 1){
            return 1;
        }
        else{
            return 0;
        }
    }

    /**
     * [update]時に使用するバリデーション。
     * unique設定をカスタマイズするのにCotrollerのほうが都合がよいためこちらに記述する。
     *
     * @param $name => 編集ユーザーの名前が入る。この名前は重複していても編集ができる。
     * @param $email => 編集ユーザのメールアドレスが入る。このメールアドレスは重複していても編集ができる。
     * @return array
     */
    protected function rules($name ,$email)
    {
        return [
            'name' => 'required|string|max:191|unique:users,name,'.$name.',name',
            'email' => 'required|string|email|max:191|unique:users,email,'.$email.',email',
            'password' => 'string|min:6|nullable',
        ];
    }

    /**
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
   public function delete(Request $request){

       $user = User::find($request->id);
       //削除倉庫を取得できなかった場合の処理
       if($user == null){
           return redirect('/home')->with(MessageDef::ERROR, MessageDef::ERROR_OLD_DELETE);
       }
       //データベースの登録処理開始
       DB::beginTransaction();
       try{
           $user->delete();
           DB::commit();
           return redirect('/user')->with(MessageDef::SUCCESS, MessageDef::SUCCESS_DELETE_USER);
       }catch(\Exception $e) {
           //エラー時
           info($e->getMessage());
           DB::rollBack();
           return redirect('/user')->with(MessageDef::ERROR, MessageDef::ERROR_DELETE);
       }
   }
}
