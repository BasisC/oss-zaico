<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SystemDef;


class Stock extends Model
{
    public static $create_rules = array(
        'serial_number' => 'required | max:191 | unique:stocks',
        'classification_id' => 'required | max:191 ',
    );

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
    public function classification(){
        return $this->belongsTo('App\Classification','classification_id');
    }

    public function getName(){
        return $this->classification->classification_name;
    }

}
