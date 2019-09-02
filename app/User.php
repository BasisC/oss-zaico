<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\SystemDef;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'per_department_create',
        'per_department_update',
        'per_department_delete',
        'per_group_create',
        'per_group_update',
        'per_group_delete'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function typeName($type){
        if($type == SystemDef::ADMIN ){
            return '管理者';
        }else{
            return '作業者';
        }
    }

    public function belongdepartments(){
        return $this->hasMany('App\BelongDepartment');
    }

    public function user_test(){
        return "testtesttest";
    }

    public function be_depart(){
        return $this->hasOne('App\BelongDepartment');
    }


    /*
    public static $rule = array(
        'name' => 'required|string|max:255|unique:users,name,'.Auth::user()->name.',name',
        'email' => 'required|string|email|max:255|unique:users,email,'.Auth::user()->email.',email',
        'password' => 'string|min:6',
    );*/


}
