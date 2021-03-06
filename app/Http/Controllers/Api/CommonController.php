<?php

namespace App\Http\Controllers\Api;

use App\Traits\TableStatusTrait;
use Illuminate\Http\Request;
use DB;
use Auth;

class CommonController extends ApiController
{
    use TableStatusTrait;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function switchEnable(Request $request)
    {
        switch ($request->table) {
            case 'users':
                if (Auth::id() == $request->id) return $this->failed('操作对象不能是你自己');
                break;
            case 'attachments':
                break;
            case 'advertisements':
                break;
            default:
                return $this->failed('不允许的操作');
                break;

        }
        $rest = DB::table($request->table)
            ->where('id', $request->id)
            ->update(['enable' => $request->value]);
        if ($rest) return $this->message('操作成功');
        return $this->failed('出错了');
    }

    public function getTableStatus($table_name, $column_name = '')
    {
        return $this->success($this->getBaseStatus($table_name, $column_name));
    }
}
