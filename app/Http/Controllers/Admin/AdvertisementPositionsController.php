<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\AdvertisementPositionCollection;
use App\Models\AdvertisementPosition;
use App\Validates\AdvertisementPositionValidate;
use Illuminate\Http\Request;

class AdvertisementPositionsController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function advertisementPositionList(Request $request, AdvertisementPosition $advertisementPosition)
    {
        $per_page = $request->get('per_page', 10);

        $search_data = json_decode($request->get('search_data'), true);
        $name = isset_and_not_empty($search_data, 'name');
        if ($name) {
            $advertisementPosition = $advertisementPosition->columnLike('name', $name);
        }
        $type = isset_and_not_empty($search_data, 'type');
        if ($type) {
            $advertisementPosition = $advertisementPosition->typeSearch($type);
        }

        return new AdvertisementPositionCollection($advertisementPosition->paginate($per_page));
    }

    public function allAdvertisementPositions(AdvertisementPosition $advertisementPosition)
    {
        return $this->success(collect($advertisementPosition->get())->keyBy('id'));
    }


    public function addEditAdvertisementPosition(Request $request, AdvertisementPosition $advertisementPosition, AdvertisementPositionValidate $validate)
    {
        $data = $request->only('id', 'name', 'type', 'description');
        $advertisement_position_id = $request->post('id', 0);

        if (is_null($data['description'])) unset($data['description']);

        if ($advertisement_position_id > 0) {
            $advertisementPosition = $advertisementPosition->findOrFail($advertisement_position_id);
            $rest_validate = $validate->updateValidate($data, $advertisement_position_id);
        } else {
            $rest_validate = $validate->storeValidate($data);
        }


        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);

        $res = $advertisementPosition->saveData($data);
        if ($res) return $this->message('操作成功');
        return $this->failed('内部错误');
    }

    public function destroy(AdvertisementPosition $advertisementPosition, AdvertisementPositionValidate $advertisementPositionValidate)
    {
        if (!$advertisementPosition) return $this->failed('找不到数据', 404);
        $rest_destroy_validate = $advertisementPositionValidate->destroyValidate($advertisementPosition);
        if ($rest_destroy_validate['status'] === true) {
            $rest_destroy = $advertisementPosition->destroyAdvertisementPosition();
            if ($rest_destroy['status'] === true) return $this->message($rest_destroy['message']);
            return $this->failed($rest_destroy['message'], 500);
        } else {
            return $this->failed($rest_destroy_validate['message']);
        }
    }
}
