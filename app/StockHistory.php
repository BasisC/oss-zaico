<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    public function setStatusName($status){
        if($status == SystemDef::INSPECTED){
            return "検品済";
        } else if ($status == SystemDef::CANT_TAKEOUT){
            return "持ち出し不可";
        }else if ($status == SystemDef::TAKING_OUT){
            return "持出中";
        }else if ($status == SystemDef::INSTALLED){
            return "設置済";
        }else{
            return "返品中";
        }
    }

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

    public function getName(){
        return $this->user->name;
    }
}
