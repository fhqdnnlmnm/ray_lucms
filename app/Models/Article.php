<?php

namespace App\Models;

use App\Models\Traits\ArticleFilterTrait;
use DB;
use Illuminate\Support\Facades\Auth;

class Article extends Model
{
    use ArticleFilterTrait;

    protected $fillable = [
        'title', 'keywords', 'description', 'cover_image', 'content', 'view_count', 'vote_count', 'comment_count', 'collection_count',
        'enable', 'recommend', 'top', 'weight', 'access_type', 'access_value', 'created_year', 'created_month', 'category_id',
    ];


    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    protected function setContentAttribute($value)
    {
        $this->attributes['content'] = clean($value, 'article_content');
    }

    protected function getCoverImageAttribute($value)
    {
        $attachment_info = Attachment::enable()->find($value);
        if ($attachment_info) {
            $url = $attachment_info->domain . '/' . $attachment_info->link_path . '/' . $attachment_info->storage_name;
            $attachment_id = $attachment_info->id;
        } else {
            $url = '';
            $attachment_id = $value;
        }
        return [
            'url' => $url,
            'attachment_id' => $attachment_id
        ];
    }

    public function storeArticle($input)
    {
        DB::beginTransaction();
        try {
            if ($input['cover_image']) {
                $this->saveAttachmentAfterSave($input['cover_image']);
            }
            $this->fill($input);
            $this->user_id = Auth::id();
            $this->save();

            if (is_array($input['tags']) && count($input['tags']) > 0) {
                $this->syncTag($input['tags']);
            }

            DB::commit();
            return $this->succeed([], '操作成功');
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return $this->failed('内部错误');
        }
    }

    public function updateArticle($input)
    {
        $old_cover_image = $this->cover_image['attachment_id'];
        $new_cover_image = $input['cover_image'];
        DB::beginTransaction();
        try {
            if (($old_cover_image != $new_cover_image)) {
                if ($old_cover_image > 0) {
                    $this->updateAttachmentAfterNotUseAgain($old_cover_image);
                }
                if ($new_cover_image > 0) {
                    $this->saveAttachmentAfterSave($new_cover_image);
                }
            }
            $this->fill($input)->save();

            DB::commit();
            return $this->succeed([], '操作成功');
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return $this->failed('内部错误');
        }
    }

    public function destroyArticle()
    {
        DB::beginTransaction();
        try {
            $attachment_id = $this->cover_image['attachment_id'];
            if ($attachment_id) {
                $this->deleteAttachmentAfterDelete($attachment_id);
            }
            $this->tags()->detach();
            $this->delete();

            DB::commit();
            return $this->succeed([], '删除成功');
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return $this->failed('内部错误');
        }
    }

}
