<?php

use Illuminate\Database\Seeder;

class FormTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('form_types')->insert([
            [
                'id' => 1,
                'form_type'=>'text_box',
                'date_type'=>'string'
            ],
            [
                'id' => 2,
                'form_type'=>'text_area',
                'date_type'=>'string'
            ],
            [
                'id' => 3,
                'form_type'=>'number',
                'date_type'=>'integer'
            ],
            [
                'id' => 4,
                'form_type'=>'radio_button',
                'date_type'=>'integer'
            ],
            [
                'id' => 5,
                'form_type'=>'check_box',
                'date_type'=>'string'
            ],
            [
                'id' => 6,
                'form_type'=>'email',
                'date_type'=>'string'
            ],
            [
                'id' => 7,
                'form_type'=>'img',
                'date_type'=>'string'
            ],
            [
                'id' => 8,
                'form_type'=>'date_time',
                'date_type'=>'dateTime'
            ]
        ]);
    }
}
