<?php

// GET Routes
Route::get('/', 'BhuLoginController@index');

// Authentication Routes...
Route::get('login', ['as' => 'getLogin', 'uses' => 'Auth\AuthController@getLogin']);
Route::post('login', 'Auth\AuthController@postLogin');
Route::post('logout', 'Auth\AuthController@getLogout');

Route::get('/home', ['as' => 'dashboard', 'uses' => 'HomeController@index']);
Route::get('/register', ['as' => 'get.register', 'uses' => 'BhuLoginController@getRegister']);
Route::get('/drop_course/{courseId}', ['as' => 'get.drop_course', 'uses' => 'BhuLoginController@dropCourse']);

Route::get('/print_course', ['as' => 'get.print_course', 'uses' => 'BhuLoginController@printCourse']);

// Admin Routes
Route::get('/admin/login', ['as' => 'admin.get_login', 'uses' => 'BhuAdminController@getLogin']);
Route::get('/admin/dashboard', ['as' => 'admin.dashboard', 'uses' => 'BhuAdminController@getDashboard']);

// Lecturer Section
Route::get('/admin/manage_course_results', ['as' => 'admin.lecturer_manage_results', 'uses' => 'BhuAdminController@lecturerManageCourseResults']);
Route::get('/admin/manage_course_result/{courseId}', ['as' => 'admin.lecturer_manage_result', 'uses' => 'BhuAdminController@lecturerManageCourseResult']);
Route::get('/admin/finalize_course_result/{courseId}', ['as' => 'admin.lecturer_finalize_result', 'uses' => 'BhuAdminController@lecturerFinalizeCourseResult']);
Route::get('/admin/finalize_course_result/{courseId}/{export}', ['as' => 'admin.lecturer_export_result', 'uses' => 'BhuAdminController@lecturerExportCourseResult']);
Route::get('/admin/manage_download_csv_sample', ['as' => 'admin.manage_download_csv_sample', 'uses' => 'BhuAdminController@downloadSampleCsv']);

Route::post('/admin/manage_course_result', ['as' => 'admin.post_lecturer_manage_result', 'uses' => 'BhuAdminController@postLecturerManageCourseResult']);
Route::post('/admin/manage_course_result_upload', ['as' => 'admin.post_lecturer_manage_result_upload', 'uses' => 'BhuAdminController@postLecturerManageCourseResultUpload']);
// End Lecturer Section

// HOD Section
Route::get('/admin/manage_courses', ['as' => 'admin.hod_manage_courses', 'uses' => 'BhuAdminController@hodManageCourses']);
Route::get('/admin/manage_add_course', ['as' => 'admin.hod_manage_add_course', 'uses' => 'BhuAdminController@getHodAddCourse']);
Route::get('/admin/manage_add_course/core/{courseId}/{semesterId}/{levelId}', ['as' => 'admin.hod_manage_add_core_course', 'uses' => 'BhuAdminController@hodManageAddCoreElectiveCourse']);
Route::get('/admin/manage_add_course/elective/{courseId}/{semesterId}/{levelId}', ['as' => 'admin.hod_manage_add_elective_course', 'uses' => 'BhuAdminController@hodManageAddCoreElectiveCourse']);
Route::get('/admin/manage_edit_course/{courseId}/{semesterId}/{levelId}', ['as' => 'admin.hod_manage_edit_course', 'uses' => 'BhuAdminController@getHodEditCourse']);
Route::get('/admin/manage_delete_course/{courseId}', ['as' => 'admin.hod_manage_delete_course', 'uses' => 'BhuAdminController@getHodDeleteCourse']);
Route::get('/admin/manage_lecturers', ['as' => 'admin.hod_manage_lecturers', 'uses' => 'BhuAdminController@hodManageLecturers']);
Route::get('/admin/manage_unaasign_course/{userId}/{courseId}/{sessionId}', ['as' => 'admin.hod_manage_unaasign_course', 'uses' => 'BhuAdminController@hodManageUnassignCourse']);
Route::get('/admin/manage_assign_course', ['as' => 'admin.get_hod_manage_assign_course', 'uses' => 'BhuAdminController@getHodManageAssignCourse']);
Route::get('/admin/manage_result_adjustments', ['as' => 'admin.hod_manage_result_adjustments', 'uses' => 'BhuAdminController@hodManageResultAdjustments']);
Route::get('/admin/manage_result_adjustment/{courseId}/{userId}/{semesterId}', ['as' => 'admin.hod_manage_result_adjustment', 'uses' => 'BhuAdminController@hodManageCourseResult']);


Route::post('/admin/manage_find_courses', ['as' => 'admin.post_hod_manage_find_courses', 'uses' => 'BhuAdminController@postHodManageFindCourses']);
Route::post('/admin/manage_edit_course', ['as' => 'admin.post_hod_manage_edit_course', 'uses' => 'BhuAdminController@postHodEditCourse']);
Route::post('/admin/manage_assign_course', ['as' => 'admin.post_hod_manage_assign_course', 'uses' => 'BhuAdminController@postHodManageAssignCourse']);
Route::post('/admin/manage_hod_course_result', ['as' => 'admin.post_hod_manage_course_result', 'uses' => 'BhuAdminController@postHodManageCourseResult']);
// End HOD Section

Route::get('/admin/logout', ['as' => 'admin.logout', 'uses' => 'BhuAdminController@getLogout']);

Route::post('/admin/login', ['as' => 'admin.post_login', 'uses' => 'BhuAdminController@postLogin']);

// POST routes
Route::post('/register', ['as' => 'post.register', 'uses' => 'BhuLoginController@postRegister']);

//Route::get('/demo',['as' => 'get.demo', 'uses' => 'BhuAdminController@getDemo']);