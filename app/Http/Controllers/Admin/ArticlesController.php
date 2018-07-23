<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\ArticleCollection;
use App\Models\Article;
use App\Validates\ArticleValidate;
use Illuminate\Http\Request;
use Auth;

class ArticlesController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function articleList(Request $request, Article $article)
    {
        $per_page = $request->get('per_page', 10);
        $search_data = json_decode($request->get('search_data'), true);
        $filter = 'default';
        $user_id = 0;
        $title = isset_and_not_empty($search_data, 'title');
        $tag_id = 0;
        $category_id = isset_and_not_empty($search_data, 'category_id');
        $recommend = isset_and_not_empty($search_data, 'recommend');
        $top = isset_and_not_empty($search_data, 'top');
        $enable = isset_and_not_empty($search_data, 'enable');
        $year = '';
        $month = '';
        $order = 'created_at';
        $order_type = 'desc';
        $list = $article->getArticlesWithFilter($filter, $user_id, $title, $tag_id, $category_id, $recommend, $top, $enable, $year, $month, $order, $order_type, $per_page);
        return new ArticleCollection($list);
    }


    public function show(Article $article)
    {
        $article_tag = $article->tags->toArray();

        $article->tagids = array_column($article_tag, 'id');
        return $this->success($article);
    }

    public function store(Request $request, Article $article, ArticleValidate $validate)
    {
        $insert_data = $request->all();
        $insert_data['cover_image'] = $insert_data['cover_image']['attachment_id'];
        $insert_data = array_merge($insert_data, ['created_year' => date('Y'), 'created_month' => date('m')]);

        $rest_validate = $validate->storeValidate($insert_data);

        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);


        $res = $article->storeArticle($insert_data);
        if ($res['status'] === true) return $this->message($res['message']);
        return $this->failed($res['message']);
    }

    public function update(Request $request, Article $article, ArticleValidate $validate)
    {
        if (!$article) return $this->failed('找不到数据', 404);

        $update_data = $request->all();
        $update_data['cover_image'] = $update_data['cover_image']['attachment_id'];

        $rest_validate = $validate->updateValidate($update_data, $article->id);

        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);


        $res = $article->updateArticle($update_data);
        if ($res['status'] === true) return $this->message($res['message']);
        return $this->failed($res['message']);
    }

    public function destroy(Article $article, ArticleValidate $validate)
    {
        if (!$article) return $this->failed('找不到数据', 404);
        $rest_destroy_validate = $validate->destroyValidate($article);
        if ($rest_destroy_validate['status'] === true) {
            $rest_destroy = $article->destroyArticle();
            if ($rest_destroy['status'] === true) return $this->message($rest_destroy['message']);
            return $this->failed($rest_destroy['message'], 500);
        } else {
            return $this->failed($rest_destroy_validate['message']);
        }
    }
}
