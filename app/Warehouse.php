<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $guarded = array('id');

    public static $create_rules = array(
        'warehouse_name' => 'required | max:191 | unique:warehouses',
        'address' => 'required | max:191 |unique:warehouses',
        'tel_number' => 'required | digits_between:1,191 | max:191 | unique:warehouses'
    );

    public static $edit_rules = array(
        'warehouse_name' => 'required | max:191',
        'address' => 'required | max:191',
        'tel_number' => 'required | max:191'
    );

    public function belong_group(){
        return $this->hasOne("App\BelongGroup");
    }

    public function getData(){
        return 'warehouse_name' ;
    }

    public function get_name(){
        return 'warehouse_name';
    }



}
