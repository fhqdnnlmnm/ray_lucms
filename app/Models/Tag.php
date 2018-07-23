<?php

namespace App\Models;

use DB;

class Tag extends Model
{

    protected $fillable = [
        'name',
    ];

    public function articles()
    {
        return $this->morphToMany(
            'App\Models\Article',
            'model',
            'model_has_tags',
            'tag_id'
        );
    }


    public function destroyTag()
    {

        DB::beginTransaction();
        try {
            DB::commit();
            return $this->succeed([], '删除成功');
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return $this->failed('内部错误');
        }
    }

}
