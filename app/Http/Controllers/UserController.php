<?php

namespace App\Http\Controllers;

use App\MessageDef;
use App\SystemDef;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
       $name = $this->getSessionValue($request,'name',$sort,'find_name');
       $email = $this->getSessionValue($request,'email',$sort,'find_email');
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
               ->paginate(SystemDef::PAGE_NUMBER);
       }catch(\Exception $e){
           //エラー時。ホーム画面に移動しエラー出力
           return redirect('/user')->with(MessageDef::ERROR, MessageDef::ERROR_UNEXPECT);
       }
       //値をセットし渡す
       $param = ['items'=>$items,'sort'=>$sort,'name'=>$name,'email'=>$email];
       return view('user.index',$param);
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
       $this->validate($request,$this->rules($userName,$userEmail));
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
           $user->per_department_create = $request->per_department_create == SystemDef::OWN_PERMISSION ? SystemDef::OWN_PERMISSION: SystemDef::NO_PERMISSION;
           $user->per_department_update= $request->per_department_update == SystemDef::OWN_PERMISSION ? SystemDef::OWN_PERMISSION: SystemDef::NO_PERMISSION;
           $user->per_department_delete = $request->per_department_delete == SystemDef::OWN_PERMISSION ? SystemDef::OWN_PERMISSION: SystemDef::NO_PERMISSION;
           $user->per_group_create = $request->per_group_create == SystemDef::OWN_PERMISSION ? SystemDef::OWN_PERMISSION: SystemDef::NO_PERMISSION;
           $user->per_group_update = $request->per_group_update == SystemDef::OWN_PERMISSION ? SystemDef::OWN_PERMISSION: SystemDef::NO_PERMISSION;
           $user->per_group_delete = $request->per_group_delete == SystemDef::OWN_PERMISSION ? SystemDef::OWN_PERMISSION: SystemDef::NO_PERMISSION;
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
           // 'name' => 'required|string|max:191|unique:users,name,'.$name.',name',
            //'email' => 'required|string|email|max:191|unique:users,email,'.$email.',email',
            'password' => 'string|min:6|nullable',
            'email' => [
                Rule::unique('users', 'email')->whereNot('email', $email),
                'required',
                'email',
                'max:191'
            ],
            'name' => [
                Rule::unique('users', 'name')->whereNot('name', $name),
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

       $user = User::find($request->id);
       //削除ユーザを取得できなかった場合の処理
       if($user == null){
           return redirect('/user')->with(MessageDef::ERROR, MessageDef::ERROR_OLD_DELETE);
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
