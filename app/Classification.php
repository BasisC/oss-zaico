<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{
    public static $create_rules = array(
        'classification_name' => 'required ',
        'warehouse_id' => 'required',
        'address' => 'string',
    );

    public function getName(){
        return $this->classification_name;
    }

}
