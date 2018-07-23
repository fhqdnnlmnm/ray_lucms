<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\IpFilterCollection;
use App\Models\IpFilter;
use App\Validates\IpFilterValidate;
use Illuminate\Http\Request;

class IpFiltersController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function ipFilterList(Request $request, IpFilter $ipFilter)
    {
        $per_page = $request->get('per_page', 10);
        $search_data = json_decode($request->get('search_data'), true);
        $ip = isset_and_not_empty($search_data, 'ip');
        if ($ip) {
            $ipFilter = $ipFilter->columnLike('ip', $ip);
        }
        $type = isset_and_not_empty($search_data, 'type');
        if ($type) {
            $ipFilter = $ipFilter->typeSearch($type);
        }

        return new IpFilterCollection($ipFilter->paginate($per_page));
    }

    public function addEditIpFilter(Request $request, IpFilter $ipFilter, IpFilterValidate $validate)
    {
        $data = $request->all();
        $filter_id = $request->post('id', 0);


        if ($filter_id > 0) {
            $ipFilter = $ipFilter->findOrFail($filter_id);
            $rest_validate = $validate->updateValidate($data, $filter_id);
        } else {
            $rest_validate = $validate->storeValidate($data);
        }
        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);

        $res = $ipFilter->saveData($data);

        if ($res) return $this->message('操作成功');
        return $this->failed('内部错误');

    }

    public function destroy(IpFilter $ipFilter)
    {
        if (!$ipFilter) return $this->failed('找不到数据', 404);
        $rest_destroy = $ipFilter->destroyIpFilter();
        if ($rest_destroy['status'] === true) return $this->message($rest_destroy['message']);
        return $this->failed($rest_destroy['message'], 500);
    }
}
