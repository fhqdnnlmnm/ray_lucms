<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\LogCollection;
use App\Models\Log;
use Illuminate\Http\Request;

class LogsController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function logList(Request $request, Log $log)
    {
        $per_page = $request->get('per_page', 10);

        $search_data = json_decode($request->get('search_data'), true);
        $type = isset_and_not_empty($search_data, 'type');
        if ($type) {
            $log = $log->typeSearch($type);
        }
        $table_name = isset_and_not_empty($search_data, 'table_name');
        if ($table_name) {
            $log = $log->tableNameSearch($table_name);
        }

        $user_name = isset_and_not_empty($search_data, 'user_name');
        if ($user_name) {
            $log = $log->userNameSearch($user_name);
        }

        return new LogCollection($log->with('user')->recent()->paginate($per_page));

    }
}
