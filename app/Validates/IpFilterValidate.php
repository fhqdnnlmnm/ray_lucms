<?php

namespace App\Validates;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DB;

class  IpFilterValidate extends Validate
{
    protected $message = '操作成功';
    protected $data = [];

    public function storeValidate($request_data)
    {
        $rules = [
            'type' => [
                'required',
                Rule::in(['white', 'black']),
            ],
            'ip' => [
                'required',
                'ip',
                'unique:ip_filters'
            ],
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {
            return $this->succeed($this->data, $this->message);
        } else {
            $this->message = $rest_validate;
            return $this->failed($this->message);
        }

    }

    public function updateValidate($request_data, $id = 0)
    {
        $rules = [
            'type' => [
                'required',
                Rule::in(['white', 'black']),
            ],
            'ip' => [
                'required',
                'ip',
                Rule::unique('ip_filters')->ignore($id),
            ],
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {
            return $this->succeed($this->data, $this->message);
        } else {
            $this->message = $rest_validate;
            return $this->failed($this->message);
        }

    }


    protected function validate($request_data, $rules)
    {
        $message = [
            'type.required' => '类型不能为空',
            'type.in' => '类型格式不正确',
            'ip.required' => 'ip不能为空',
            'ip.ip' => '请填写正确的 ip 地址',
            'ip.unique' => 'ip已经存在',
        ];
        $validator = Validator::make($request_data, $rules, $message);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        return true;
    }
}
