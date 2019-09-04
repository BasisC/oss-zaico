<?php
namespace App;

class SystemDef
{
    const ADMIN = 0;
    const EMPLOYEE = 1;

    const SUCCESS = "success";
    const WARN = "warning";
    const ERROR = "error";

    const SECCESS = 0;
    const ABNORMAL = -1;

    const ID_RULE = 'integer|required|between:1, 2147483647';

    const OWN_PERMISSION = 1;
    const NO_PERMISSION = 0;

    const PAGE_NUMBER = 5;


}
