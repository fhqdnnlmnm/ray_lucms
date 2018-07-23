<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Validates\UserValidate;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class UserController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
//        $this->middleware('passport-administrators');
        $this->middleware('auth:api');
    }

    public function usersList(Request $request, User $user)
    {
        $per_page = $request->get('per_page', 10);
        $search_data = json_decode($request->get('search_data'), true);
        $email = isset_and_not_empty($search_data, 'email');
        if ($email) {
            $user = $user->columnLike('email', $email);
        }

        $enable = isset_and_not_empty($search_data, 'enable');
        if ($enable) {
            $user = $user->enableSearch($enable);
        }

        $is_admin = isset_and_not_empty($search_data, 'is_admin');
        if ($is_admin) {
            $user = $user->isAdminSearch($is_admin);
        }

        return new UserCollection($user->paginate($per_page));
    }


    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function store(Request $request, User $user, UserValidate $validate)
    {
        $insert_data = $request->all();
        $insert_data['head_image'] = $insert_data['head_image']['attachment_id'];

        $rest_validate = $validate->storeValidate($insert_data);

        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);


        $res = $user->storeUser($insert_data);
        if ($res['status'] === true) return $this->message($res['message']);
        return $this->failed($res['message']);

    }

    public function update(User $user, Request $request, UserValidate $validate)
    {
        $update_data = $request->only('id', 'name', 'head_image', 'is_admin');

        $rest_validate = $validate->updateValidate($update_data);

        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);


        $update_data['head_image'] = $update_data['head_image']['attachment_id'];
        $res = $user->updateUser($update_data);

        if ($res['status'] === true) {
            admin_log_record(Auth::id(), 'U', 'users', '更新用户', $update_data);
            return $this->message($res['message']);
        }
        return $this->failed($res['message']);
    }

    public function getUserRoles(User $user)
    {
        $roles = $user->roles()->get();
        $return = [];
        $roles->each(function ($per) use (&$return) {
            $return[] = strval($per->id);
        });

        return $this->success($return);
    }

    public function giveUserRoles(Request $request, User $user)
    {
        $roles = $request->post('role');
        $user->syncRoles($roles);
        return $this->message('角色分配成功');
    }

    public function destroy(User $user)
    {
        if (!$user) return $this->failed('找不到用户', 404);
        $rest_destroy = $user->destroyUser();
        if ($rest_destroy['status'] === true) return $this->message($rest_destroy['message']);
        return $this->failed($rest_destroy['message'], 500);
    }
}
