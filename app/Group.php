<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = array('id');

    public static $edit_rules = array(
        'group_name' => 'required | max:191',
    );

    public static $create_rules = array(
        'group_name' => 'required | max:191',
    );



}
