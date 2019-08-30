<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BelongGroup extends Model
{
    public function warehouses(){
        return $this->hasMany('App\Warehouse');
    }

    public function get_name()
    {
        //
    }
    public function getData(){
        return "aaaaa";
    }


}
