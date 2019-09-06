<?php
namespace App;

class MessageDef
{
    //成功時・失敗時
    const SUCCESS = "success_message";
    const ERROR = "error_message";


    //エラー
    const INPUT_DATA_ERROR = "入力データに誤りがあります";
    const COMPANY_NO_SELECT = "会社を一つ以上選択してください";
    const NO_ENTRY_COMPANY_RECORD = "会社が登録されていません。会社情報を登録してください";
    const ERROR_ABNORMAL_USER = "登録できないユーザを追加しようとしました";
    const ERROR_ABNORMAL_PARAM = "不正なパラメーターが指定されました";
    const NO_PERMISSION = "権限がありません";
    const NOT_EXISTS_USER = "指定したユーザは存在しません";
    const DUPLICATE_ERROR = "すでに登録されています";
    const DELETE_ERROR = "削除に失敗しました";
    const ERROR_EDIT = "編集に失敗しました";
    const ERROR_CREATE = "登録に失敗しました";
    const ERROR_DELETE = "削除に失敗しました";
    const ERROR_LOGIN = "ログインしてください";
    const ERROR_OLD_DELETE = "すでに削除されています";
    const ERROR_NON_ID = "このIDは存在しません";
    const ERROR_TYPE = "本ユーザの権限ではアクセスできません";
    const ERROR_PERMISSION = "本ユーザの役割ではアクセスできません";
    const ERROR_UNEXPECT = "予期できないエラーです";
    const ERROR_NOT_GET_DB = "データを取得できませんでした";
    const ERROR_NOT_PARAMETER = "この項目でソートはできません";


    //警告

    //正常
    const SUCCESS_ENTRY_USER = "ユーザを追加しました";
    const SUCCESS_UPDATE_USER = "ユーザを編集しました";
    const SUCCESS_ENTRY_COMPANY = "会社を追加しました";
    const SUCCESS_UPDATE_COMPANY = "会社を編集しました";
    const SUCCESS_DELETE_COMPANY = "会社を削除しました";
    const SUCCESS_NO_DELETE_COMPANY = "会社削除をキャンセルしました";
    const SUCCESS_CREATE_WAREHOUSE = "倉庫を登録しました";
    const SUCCESS_EDIT_WAREHOUSE = "倉庫を編集しました";
    const SUCCESS_DELETE_WAREHOUSE = "倉庫を削除しました";
    const SUCCESS_BELONG_WAREHOUSE = "グループ所属を変更しました";
    const SUCCESS_EDIT_GROUP = "グループを編集しました";
    const SUCCESS_DELETE_GROUP = "グループを削除しました";
    const SUCCESS_EDIT_USER = "ユーザを編集しました";
    const SUCCESS_DELETE_USER = "ユーザを削除しました";
    const SUCCESS_BELONG_DEPARTMENT = "部署に所属しました";
    const SUCCESS_DELETE_DEPARTMENT = "部署を削除しました";
    const SUCCESS_EDIT_DEPARTMENT = "部署を編集しました";
    const SUCCESS_TARGET = "操作対象しました";

}
