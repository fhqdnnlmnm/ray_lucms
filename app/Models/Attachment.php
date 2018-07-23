<?php

namespace App\Models;

use App\Handlers\FileuploadHandler;
use DB;


class Attachment extends Model
{
    protected $fillable = [
        'user_id', 'ip', 'original_name', 'mime_type', 'size', 'type', 'storage_position',
        'domain', 'storage_path', 'link_path', 'storage_name', 'enable', 'use_status', 'remark'
    ];

    protected function setIpAttribute()
    {
        $this->attributes['ip'] = get_client_ip();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function scopeUseStatusSearch($query, $value)
    {
        return $query->where('use_status', $value);
    }

    public function scopeTypeSearch($query, $value)
    {
        return $query->where('type', $value);
    }

    public function scopeStoragePositionSearch($query, $value)
    {
        return $query->where('storage_position', $value);
    }

    public function destroyAttachment()
    {
        $base_image_up_dir = 'images';
        $rest_delet_file = (new FileuploadHandler)->fileDelete($base_image_up_dir . '/' . $this->type . '/' . $this->storage_name);

        DB::beginTransaction();
        try {
            if ($rest_delet_file) {
                $this->delete();
            } else {
                DB::rollBack();
                return $this->failed('附件片删除错误');
            }
            DB::commit();
            return $this->succeed([], '附件限删除成功');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->failed('内部错误');
        }
    }

}
