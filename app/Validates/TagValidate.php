<?php

namespace App\Validates;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DB;

class  TagValidate extends Validate
{
    protected $message = '操作成功';
    protected $data = [];

    public function storeValidate($request_data)
    {
        $rules = [
            'name' => [
                'required',
                'between:2,20',
                'unique:tags'
            ],
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {
            return $this->succeed($this->data,$this->message);
        } else {
            $this->message = $rest_validate;
            return $this->failed($this->message);
        }

    }

    public function updateValidate($request_data, $tag_id = 0)
    {
        $rules = [
            'name' => [
                'required',
                'between:2,20',
                Rule::unique('tags')->ignore($tag_id),
            ],
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {
            return $this->succeed($this->data,$this->message);
        } else {
            $this->message = $rest_validate;
            return $this->failed($this->message);
        }

    }

    public function destroyValidate($category)
    {
        $is_model_has_this_tag = DB::table('role_has_tags')
            ->where('tag_id', $category->id)
            ->count();
        if ($is_model_has_this_tag) return $this->failed('有模型在使用该标签,无法删除');
        return $this->succeed($this->data, $this->message);
    }

    protected function validate($request_data, $rules)
    {
        $message = [
            'name.required' => '名称不能为空',
            'name.between' => '名称只能在:min-:max个字符范围',
            'name.unique' => '名称已经被占用',
        ];
        $validator = Validator::make($request_data, $rules, $message);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        return true;
    }
}
