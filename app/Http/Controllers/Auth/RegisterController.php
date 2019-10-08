<?php

namespace App\Http\Controllers\Auth;

use App\MessageDef;
use App\User;
use App\Http\Controllers\Controller;
use App\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/user';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'per_department_create' => $this->valueChk($data,'per_department_create'),
            'per_department_update' => $this->valueChk($data,'per_department_update'),
            'per_department_delete' => $this->valueChk($data,'per_department_delete'),
            'per_group_create' => $this->valueChk($data,'per_group_create'),
            'per_group_update' => $this->valueChk($data,'per_group_update'),
            'per_group_delete' => $this->valueChk($data,'per_group_delete'),

        ]);
    }
    public function register(Request $request)
    {
       // event(new Registered($user = $this->create($request->all())));

        // $this->guard()->login($user);
        //ユーザ判別処理

        //変数宣言
        $this->validator($request->all())->validate();
        $warehouse = new Warehouse;
        //DBに書き込む処理を開始
        DB::beginTransaction();
        try {
            $this->create($request->all())->save();
            DB::commit();
            return redirect('/user')->with(MessageDef::SUCCESS, MessageDef::SUCCESS_CREATE_WAREHOUSE);
        }catch (\Exception  $e) {
            //エラー時
            DB::rollBack();
            return redirect('/register')->with(MessageDef::ERROR, MessageDef:: ERROR_CREATE );
        }
    }

    public function valueChk($data,$key){
        if ( array_key_exists($key, $data) ) {
             return  $data[$key];
        } else{
            return 0;
        }
    }

}
