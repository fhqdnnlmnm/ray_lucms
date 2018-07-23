<?php


/**       ==========================          基本APi           ====================   */
Route::namespace('Api')->group(function () {

    Route::post('login', 'LoginController@login');
    Route::post('logout', 'LoginController@logout');
    Route::post('refreshtoken', 'LoginController@refreshToken');

    // 多表登录测试
    /*
    Route::post('admin_user/login', 'LoginController@adminUserLogin');
    Route::get('admin_user', 'AdminUsersController@index');
    Route::post('admin_user/logout', 'LoginController@adminUserLogout');
    */


    Route::post('common_switch_enable', 'CommonController@switchEnable');
    Route::get('common_get_table_status/{table_name}/{column_name?}', 'CommonController@getTableStatus');
});

/**       ==========================          后台APi           ====================   */
Route::namespace('Admin')->group(function () {

    Route::get('admin/users', 'UserController@usersList')->name('users.list');
    Route::get('admin/users/{user}', 'UserController@show')->name('users.show');
    Route::post('admin/users', 'UserController@store')->name('users.store');
    Route::patch('admin/users/{user}', 'UserController@update')->name('users.update');
    Route::get('admin/users/{user}/roles', 'UserController@getUserRoles')->name('users.get_user_roles');
    Route::post('admin/give/{user}/roles', 'UserController@giveUserRoles')->name('users.give_user_roles');
    Route::delete('admin/users/{user}', 'UserController@destroy')->name('users.destroy');

    Route::get('admin/permissions', 'PermissionsController@permissionList')->name('permissions.list');
    Route::post('admin/permissions', 'PermissionsController@addEditPermission')->name('permissions.add_edit');
    Route::get('admin/all_permissions', 'PermissionsController@allPermissions')->name('permissions.all');
    Route::delete('admin/permissions/{permission}', 'PermissionsController@destroy')->name('permissions.destroy');

    Route::get('admin/roles', 'RolesController@roleList')->name('roles.list');
    Route::post('admin/roles', 'RolesController@addEditRole')->name('roles.add_edit');
    Route::get('admin/roles/{role}/permissions', 'RolesController@getRolePermissions')->name('roles.get_role_permissions');
    Route::post('admin/give/{role}/permissions', 'RolesController@giveRolePermissions')->name('roles.give_role_permissions');
    Route::get('admin/all_roles', 'RolesController@allRoles')->name('roles.all');
    Route::delete('admin/roles/{role}', 'RolesController@destroy')->name('roles.destroy');


    Route::get('admin/attachments', 'AttachmentsController@attachmentList')->name('attachments.list');
    Route::delete('admin/attachments/{attachment}', 'AttachmentsController@destroy')->name('attachments.destroy');

    Route::get('admin/advertisement_positions', 'AdvertisementPositionsController@advertisementPositionList')->name('advertisement_positions.list');
    Route::post('admin/advertisement_positions', 'AdvertisementPositionsController@addEditAdvertisementPosition')->name('advertisement_positions.add_edit');
    Route::delete('admin/advertisement_positions/{advertisement_position}', 'AdvertisementPositionsController@destroy')->name('advertisement_positions.destroy');
    Route::get('admin/advertisement_positions/all', 'AdvertisementPositionsController@allAdvertisementPositions')->name('advertisement_positions.all');


    Route::get('admin/advertisements', 'AdvertisementsController@advertisementList')->name('advertisements.list');
    Route::get('admin/advertisements/{advertisement}', 'AdvertisementsController@show')->name('advertisements.show');
    Route::post('admin/advertisements', 'AdvertisementsController@store')->name('advertisements.store');
    Route::patch('admin/advertisements/{advertisement}', 'AdvertisementsController@update')->name('advertisements.update');
    Route::delete('admin/advertisements/{advertisement}', 'AdvertisementsController@destroy')->name('advertisements.destroy');


    Route::get('admin/categories', 'CategoriesController@categoryList')->name('categories.list');
    Route::post('admin/categories', 'CategoriesController@addEditCategory')->name('categories.add_edit');
    Route::get('admin/categories/all', 'CategoriesController@allCategories')->name('categories.all');


    Route::get('admin/tags', 'TagsController@tagList')->name('tags.list');
    Route::post('admin/tags', 'TagsController@addEditTag')->name('tags.add_edit');


    Route::get('admin/articles', 'ArticlesController@articleList')->name('articles.list');
    Route::get('admin/articles/{article}', 'ArticlesController@show')->name('articles.show');
    Route::post('admin/articles', 'ArticlesController@store')->name('articles.store');
    Route::patch('admin/articles/{article}', 'ArticlesController@update')->name('articles.update');
    Route::delete('admin/articles/{article}', 'ArticlesController@destroy')->name('articles.destroy');


    Route::get('admin/logs', 'LogsController@logList')->name('logs.list');


    Route::get('admin/ip_filters', 'IpFiltersController@ipFilterList')->name('ip_filters.list');
    Route::post('admin/ip_filters', 'IpFiltersController@addEditIpFilter')->name('ip_filters.add_edit');
    Route::delete('admin/ip_filters/{ip_filter}', 'IpFiltersController@destroy')->name('ip_filters.destroy');
});


/**       ==========================          文件上传           ====================   */
Route::post('upload/avatar', 'Api\UploadController@uploadAvatar')->name('uploads.avatar');
Route::post('upload/tinymce', 'Api\UploadController@tinymceUpload')->name('uploads.tinymce');
Route::post('upload/advertisement', 'Api\UploadController@advertisementUpload')->name('uploads.advertisement');
Route::post('upload/other', 'Api\UploadController@otherUpload')->name('uploads.other');
