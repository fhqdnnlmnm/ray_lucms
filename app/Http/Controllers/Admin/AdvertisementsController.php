<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\AdvertisementCollection;
use App\Models\Advertisement;
use App\Validates\AdvertisementValidate;
use Illuminate\Http\Request;
use Purifier;

class AdvertisementsController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function advertisementList(Request $request, Advertisement $advertisement)
    {
        $per_page = $request->get('per_page', 10);

        $search_data = json_decode($request->get('search_data'), true);
        $name = isset_and_not_empty($search_data, 'name');
        if ($name) {
            $advertisement = $advertisement->columnLike('name', $name);
        }

        $enable = isset_and_not_empty($search_data, 'enable');
        if ($enable) {
            $advertisement = $advertisement->enable('enable', $enable);
        }

        $advertisement_positions_id = isset_and_not_empty($search_data, 'advertisement_positions_id');
        if ($advertisement_positions_id) {
            $advertisement = $advertisement->advertisementPositionSearch($advertisement_positions_id);
        }

        return new AdvertisementCollection($advertisement->with('advertisementPosition')->paginate($per_page));
    }

    public function show(Advertisement $advertisement)
    {
        $advertisement->advertisementPosition;
        return $this->success($advertisement);
    }

    public function store(Request $request, Advertisement $advertisement, AdvertisementValidate $validate)
    {
        $insert_data = $request->all();
        $insert_data['cover_image'] = $insert_data['cover_image']['attachment_id'];

        if ($insert_data['advertisement_positions_type'] == 'model') {
            $model_column_value = $insert_data['model_column_value'];
            if (!$model_column_value['column'] || !$model_column_value['model'] || !$model_column_value['value']) {
                return $this->failed('跳转模型类广告位，必须填写key');
            }
        } else {
            $insert_data['model_column_value'] = [
                'model' => '',
                'column' => '',
                'value' => ''
            ];
        }
        $rest_validate = $validate->storeValidate($insert_data);

        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);


        $res = $advertisement->storeAdvertisement($insert_data);
        if ($res['status'] === true) return $this->message($res['message']);
        return $this->failed($res['message']);
    }

    public function update(Request $request, Advertisement $advertisement, AdvertisementValidate $validate)
    {
        $insert_data = $request->all();
        $insert_data['cover_image'] = $insert_data['cover_image']['attachment_id'];

        if ($insert_data['advertisement_positions_type'] == 'model') {
            $model_column_value = $insert_data['model_column_value'];
            if (!$model_column_value['column'] || !$model_column_value['model'] || !$model_column_value['value']) {
                return $this->failed('跳转模型类广告位，必须填写key');
            }
        } else {
            $insert_data['model_column_value'] = [
                'model' => '',
                'column' => '',
                'value' => ''
            ];
        }
        $rest_validate = $validate->updateValidate($insert_data, $advertisement->id);

        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);


        $res = $advertisement->updateAdvertisement($insert_data);
        if ($res['status'] === true) return $this->message($res['message']);
        return $this->failed($res['message']);
    }


    public function destroy(Advertisement $advertisement)
    {
        if (!$advertisement) return $this->failed('找不到数据', 404);
        $rest_destroy = $advertisement->destroyAdvertisement();
        if ($rest_destroy['status'] === true) return $this->message($rest_destroy['message']);
        return $this->failed($rest_destroy['message'], 500);
    }
}
