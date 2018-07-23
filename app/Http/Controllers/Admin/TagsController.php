<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use App\Validates\TagValidate;
use Illuminate\Http\Request;

class TagsController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function tagList(Request $request, Tag $tag)
    {
        $search_data = json_decode($request->get('search_data'), true);
        $name = isset_and_not_empty($search_data, 'name');
        if ($name) {
            $tag = $tag->columnLike('name', $name);
        }

        return $this->success($tag->get());
    }

    public function addEditTag(Request $request, Tag $tag, TagValidate $validate)
    {
        $data = $request->all();
        $tag_id = $request->post('id', 0);


        if ($tag_id > 0) {
            $tag = $tag->findOrFail($tag_id);
            $rest_validate = $validate->updateValidate($data, $tag_id);
        } else {
            $rest_validate = $validate->storeValidate($data);
        }
        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);

        $res = $tag->saveData($data);

        if ($res) return $this->message('操作成功');
        return $this->failed('内部错误');

    }

    public function destroy(Tag $tag, TagValidate $validate)
    {
        if (!$tag) return $this->failed('找不到数据', 404);
        $rest_destroy_validate = $validate->destroyValidate($tag);
        if ($rest_destroy_validate['status'] === true) {
            $rest_destroy = $tag->destroyTag();
            if ($rest_destroy['status'] === true) return $this->message($rest_destroy['message']);
            return $this->failed($rest_destroy['message'], 500);
        } else {
            return $this->failed($rest_destroy_validate['message']);
        }
    }
}
