<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    public function be_depart(){
        return $this->belongsTo('App\Department','department_id');
    }

    public function be_group(){
        return $this->belongsTo('App\Group','group_id');
    }
}
