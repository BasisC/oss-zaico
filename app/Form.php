<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    public function col_name(){
        return $this->hasOne('App\ColName');
    }

    public function form_type(){
        return $this->belongsTo('App\FormType','form_type_id');
    }

    public function getName(){
        return $this->form_type->form_type;
    }

    public function selects(){
        return $this->hasMany('App\Select');
    }

}
