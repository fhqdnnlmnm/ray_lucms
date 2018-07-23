<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Validates\CategoryValidate;
use Illuminate\Http\Request;

class CategoriesController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function categoryList(Request $request, Category $category)
    {
        $search_data = json_decode($request->get('search_data'), true);
        $name = isset_and_not_empty($search_data, 'name');
        if ($name) {
            $category = $category->columnLike('name', $name);
        }

        return $this->success($category->get());
    }

    public function allCategories(Category $category)
    {
        return $this->success(collect($category->get())->keyBy('id'));
    }

    public function addEditCategory(Request $request, Category $category, CategoryValidate $validate)
    {
        $data = $request->all();
        $category_id = $request->post('id', 0);
        $data['cover_image'] = $data['cover_image']['attachment_id'];
        if (is_null($data['description'])) unset($data['description']);

        if ($category_id > 0) {
            $category = $category->findOrFail($category_id);
            $rest_validate = $validate->updateValidate($data, $category_id);
            if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);
            $res = $category->updateCategory($data);
        } else {
            $rest_validate = $validate->storeValidate($data);
            if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);
            $res = $category->storeCategory($data);
        }

        if ($res['status'] === true) return $this->message($res['message']);
        return $this->failed($res['message']);
    }

    public function destroy(Category $category, CategoryValidate $validate)
    {
        if (!$category) return $this->failed('找不到数据', 404);
        $rest_destroy_validate = $validate->destroyValidate($category);
        if ($rest_destroy_validate['status'] === true) {
            $rest_destroy = $category->destroyCategory();
            if ($rest_destroy['status'] === true) return $this->message($rest_destroy['message']);
            return $this->failed($rest_destroy['message'], 500);
        } else {
            return $this->failed($rest_destroy_validate['message']);
        }
    }
}
