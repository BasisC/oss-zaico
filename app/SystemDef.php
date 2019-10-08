<?php
namespace App;

use Illuminate\Support\Facades\Log;

class SystemDef
{
    //権限
    const ADMIN = 0;
    const EMPLOYEE = 1;

    //メッセージ種類
    const SUCCESS = "success";
    const WARN = "warning";
    const ERROR = "error";

    //成功失敗
    const SECCESS = 0;
    const ABNORMAL = -1;

    //IDを得る
    const ID_RULE = 'integer|required|between:1, 2147483647';

    //役割を所有しているかどうか
    const OWN_PERMISSION = 1;
    const NO_PERMISSION = 0;

    //ページ数
    const PAGE_NUMBER = 5;

    //ユーザコントローラ
    const USER_CONTROLLER = 'UserController';

    //機器のステータス
    const INSPECTED = 0;
    const CANT_TAKEOUT = 1;
    const TAKING_OUT = 2;
    const INSTALLED = 3;
    const RETURNING = 4;

    //フォームの種類
    const text_box = 1;
    const text_area = 2;
    const number = 3;
    const radio_button = 4;
    const check_box = 5;
    const email = 6;
    const img = 7;
    const date_time = 8;

    //ユニーク制約
    const unique =1;
    const not_unique = 0;

    //null制約
    const not_null = 0;
    const null_able = 1;

}
