<?php

namespace App\Models;

use DB;


class AdvertisementPosition extends Model
{
    protected $fillable = [
        'name', 'type', 'description'
    ];

    public function scopeTypeSearch($query, $value)
    {
        return $query->where('type', $value);
    }


    public function destroyAdvertisementPosition()
    {

        DB::beginTransaction();
        try {
            $this->delete();
            DB::commit();
            return $this->succeed([], '广告位删除成功');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->failed('内部错误');
        }
    }

}
