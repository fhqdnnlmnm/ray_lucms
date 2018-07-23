<?php

namespace App\Handlers;

use App\Models\Attachment;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class FileuploadHandler
{
    protected $status = true;
    protected $message = '图片上传成功';
    protected $data = [];
    protected $base_image_up_dir = 'images';
    protected $m_attachment;

    public function __construct()
    {
        $this->m_attachment = new Attachment();
    }


    public function uploadImage($file, $user_id, $max_width = 0, $path = 'avatars')
    {
        $up_dir = $this->base_image_up_dir . '/' . $path;

        if ($file) {
            $rest_upload = $this->data = $file->store($up_dir);
        } else {
            $this->status = false;
            $this->message = '请选择要上传的图片';
            return ['status' => $this->status, 'data' => $this->data, 'message' => $this->message];
        }
        $min_type = $file->getClientMimeType();
        $inser_data = [
            'user_id' => $user_id,
            'ip' => '',
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $min_type,
            'size' => round($file->getClientSize() / 1000, 2),
            'type' => $path,
            'storage_position' => 'local',
            'domain' => config('app.url'),
            'link_path' => 'storage/' . $up_dir,
            'storage_name' => basename($rest_upload),

        ];

        if (in_array($path, ['editors'])) $inser_data['use_status'] = 1;

        $inser_data['storage_path'] = storage_path() . '/app/public/' . $up_dir;
        $inser_data['url'] = $inser_data['domain'] . '/' . $inser_data['link_path'] . '/' . $inser_data['storage_name'];

        try {
            $rest_insert_attachment_table = $this->m_attachment->saveData($inser_data);
            // 如果限制了图片宽度，就进行裁剪
            if ($max_width && $min_type != 'gif') {

                // 此类中封装的函数，用于裁剪图片
                $this->reduceSize($inser_data['storage_path'] . '/' . $inser_data['storage_name'], $max_width);
            }
            $this->data = array_merge($inser_data, ['attachment_id' => $rest_insert_attachment_table->id]);
        } catch (\Exception $e) {
            $this->message = $e;
        }
        return ['status' => $this->status, 'data' => $this->data, 'message' => $this->message];
    }

    public function fileDelete($file_name = [])
    {
        return Storage::delete($file_name);
    }

    protected function reduceSize($file_path, $max_width)
    {
        // 先实例化，传参是文件的磁盘物理路径
        $image = Image::make($file_path);

        // 进行大小调整的操作
        $image->resize($max_width, null, function ($constraint) {

            // 设定宽度是 $max_width，高度等比例双方缩放
            $constraint->aspectRatio();

            // 防止裁图时图片尺寸变大
            $constraint->upsize();
        });

        // 对图片修改后进行保存
        $image->save();
    }
}
