<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
        [
          'id' => 1,
          'name' => '管理者',
          'email' => 'admin@test-corp.jp',
          'password' => Hash::make('Basis0318'),
          'type' => 0,
          'remember_token'=>null,
          'per_department_create'=>0,
          'per_department_update'=>0,
          'per_department_delete'=>0,
          'per_group_create'=>0,
          'per_group_update'=>0,
          'per_group_delete'=>0,
          'created_at'=>'2019-06-07 08:55:32',
          'updated_at'=>'2019-06-07 08:55:32',
        ]
      ]);
    }
}
