<?php

namespace App\Models;

use DB;

class IpFilter extends Model
{

    protected $fillable = [
        'type', 'ip'
    ];

    public function scopeTypeSearch($query, $value)
    {
        return $query->where('type', $value);
    }

    public function destroyIpFilter()
    {

        DB::beginTransaction();
        try {
            DB::commit();
            $this->delete();
            return $this->succeed([], '删除成功');
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return $this->failed('内部错误');
        }
    }


}
