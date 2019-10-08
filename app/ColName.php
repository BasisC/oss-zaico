<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ColName extends Model
{
    public function getName(){
        return $this->col_name;
    }
}
