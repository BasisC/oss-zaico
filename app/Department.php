<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    public static $create_rules = array(
        'department_name' => 'required |max:191 |unique:departments',
    );
}
