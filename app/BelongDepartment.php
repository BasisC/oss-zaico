<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BelongDepartment extends Model
{
    public function test(){
      //  return $this->be_depart()->User->name;
        return "aaaa";
    }

    public function be_depart(){
        return $this->belongsTo('App\User','user_id');
    }


}
