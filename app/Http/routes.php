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

Route::get('/print_course/{semester}', ['as' => 'get.print_course', 'uses' => 'BhuLoginController@printCourse']);
Route::get('/results/{sessionId?}/{semesterId?}',['as' => 'get.results', 'uses' => 'BhuLoginController@getResults']);
//Route::get('/find_results',['as' => 'post.find_results', 'uses' => 'BhuLoginController@findResults']);

// Admin Routes
Route::get('/admin/login', ['as' => 'admin.get_login', 'uses' => 'BhuAdminController@getLogin']);
Route::get('/admin/dashboard', ['as' => 'admin.dashboard', 'uses' => 'BhuAdminController@getDashboard']);

// Lecturer Section
Route::get('/admin/manage_course_results', ['as' => 'admin.lecturer_manage_results', 'uses' => 'BhuAdminController@lecturerManageCourseResults']);
Route::get('/admin/manage_course_result/{courseId}/{sessionId?}/{semesterId?}/{isAltEntry?}', ['as' => 'admin.lecturer_manage_result', 'uses' => 'BhuAdminController@lecturerManageCourseResult']);
Route::get('/admin/finalize_course_result/{courseId}/{sessionId?}/{semesterId?}', ['as' => 'admin.lecturer_finalize_result', 'uses' => 'BhuAdminController@lecturerFinalizeCourseResult']);
Route::get('/admin/finalize_course_result/{courseId}/{sessionId?}/{semesterId?}/{export}', ['as' => 'admin.lecturer_export_result', 'uses' => 'BhuAdminController@lecturerExportCourseResult']);
Route::get('/admin/manage_download_csv_sample', ['as' => 'admin.manage_download_csv_sample', 'uses' => 'BhuAdminController@downloadSampleCsv']);
Route::get('/admin/manage_download_single_csv_sample', ['as' => 'admin.manage_download_single_csv_sample', 'uses' => 'BhuAdminController@downloadSingleSampleCsv']);

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
Route::get('/admin/department_lecturer', ['as' => 'admin.department_lecturers', 'uses' => 'BhuAdminController@hodDepartmentLecturers']);


Route::post('/admin/manage_find_courses', ['as' => 'admin.post_hod_manage_find_courses', 'uses' => 'BhuAdminController@postHodManageFindCourses']);
Route::post('/admin/manage_edit_course', ['as' => 'admin.post_hod_manage_edit_course', 'uses' => 'BhuAdminController@postHodEditCourse']);
Route::post('/admin/manage_assign_course', ['as' => 'admin.post_hod_manage_assign_course', 'uses' => 'BhuAdminController@postHodManageAssignCourse']);
Route::post('/admin/manage_hod_course_result', ['as' => 'admin.post_hod_manage_course_result', 'uses' => 'BhuAdminController@postHodManageCourseResult']);
// End HOD Section

// Reports Section
Route::get('/admin/manage_reports',['as' => 'admin.get_reports', 'uses' => 'BhuAdminReportController@manageAdminReports']);
Route::get('/admin/manage_detailed_reports',['as' => 'admin.get_detailed_reports', 'uses' => 'BhuAdminReportController@manageAdminDetailedReports']);
Route::get('/admin/manage_find_submission',['as' => 'admin.post_find_submission', 'uses' => 'BhuAdminReportController@manageAdminFindResultSubmission']);
Route::get('/admin/manage_find_detailed_report',['as' => 'admin.post_find_detailed_report', 'uses' => 'BhuAdminReportController@manageAdminFindDetailedReport']);
Route::get('/admin/manage_course_result_hod/{courseId}/{sessionId}/{semesterId}', ['as' => 'admin.report_manage_result', 'uses' => 'BhuAdminReportController@manageAdminCourseResults']);
Route::get('/admin/manage_approve_course_result/{courseId}/{sessionId}/{semesterId}', ['as' => 'admin.report_approve_manage_result', 'uses' => 'BhuAdminReportController@manageAdminApproveCourseResults']);
Route::get('/admin/manage_detailed_results/{deptId}/{sessionId}/{semesterId}/{levelId}', ['as' => 'admin.report_detailed_results', 'uses' => 'BhuAdminReportController@manageAdminDetailedCourseResults']);
Route::get('/admin/manage_results_summary/{deptId}/{sessionId}/{semesterId}/{levelId}', ['as' => 'admin.report_results_summary', 'uses' => 'BhuAdminReportController@manageAdminSummaryCourseResults']);
Route::get('/admin/manage_results_remark/{deptId}/{sessionId}/{semesterId}/{levelId}', ['as' => 'admin.report_results_remark', 'uses' => 'BhuAdminReportController@manageAdminRemarkCourseResults']);
Route::get('/admin/manage_finalize_level_result/{deptId}/{sessionId}/{semesterId}/{levelId}', ['as' => 'admin.report_finalize_level_result', 'uses' => 'BhuAdminReportController@manageFinalizeLevelResult']);

// End Reports Section

// Transcript Section
Route::get('/admin/new_transcript',['as' => 'admin.get_new_transcript', 'uses' => 'BhuAdminController@getNewTranscript']);
Route::post('/admin/new_transcript',['as' => 'admin.post_new_transcript', 'uses' => 'BhuAdminController@postNewTranscript']);
// End Transcript Section

Route::get('/admin/change_password', ['as' => 'admin.change_password', 'uses' => 'BhuAdminController@getChangePassword']);
Route::post('/admin/change_password', ['as' => 'admin.post_change_password', 'uses' => 'BhuAdminController@postChangePassword']);


Route::get('/admin/logout', ['as' => 'admin.logout', 'uses' => 'BhuAdminController@getLogout']);

Route::post('/admin/login', ['as' => 'admin.post_login', 'uses' => 'BhuAdminController@postLogin']);

// POST routes
Route::post('/register', ['as' => 'post.register', 'uses' => 'BhuLoginController@postRegister']);

//Route::get('/demo',['as' => 'get.demo', 'uses' => 'BhuAdminController@getDemo']);
Route::get('/biodata_update',['as' => 'get.demo', 'uses' => 'BhuAdminController@getBiodataUpdate']);