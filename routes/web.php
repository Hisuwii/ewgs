<?php

$router->get('/admin', 'AdminAuthController@showLogin');
$router->get('/admin/register', 'AdminAuthController@showRegister');
$router->post('/admin/register', 'AdminAuthController@register');

$router->get('/admin/teacher', 'TeacherController@index');
$router->get('/admin/teacher/data', 'TeacherController@getData');
$router->get('/admin/teacher/logs', 'TeacherController@logs');
$router->get('/admin/teacher/logs/data', 'TeacherController@getLogsData');
$router->post('/admin/teacher/add', 'TeacherController@add');
$router->post('/admin/teacher/edit',   'TeacherController@edit');
$router->post('/admin/teacher/delete', 'TeacherController@delete');
$router->post('/admin/teacher/reset-password', 'TeacherController@resetPassword');
$router->post('/admin/teacher/toggle-status',    'TeacherController@toggleStatus');

$router->get('/admin/class', 'ClassController@index');
$router->get('/admin/class/data', 'ClassController@getData');
$router->post('/admin/class/add', 'ClassController@add');
$router->post('/admin/class/edit',   'ClassController@edit');
$router->post('/admin/class/delete', 'ClassController@delete');

$router->get('/admin/subject', 'SubjectController@index');
$router->get('/admin/subject/data', 'SubjectController@getData');
$router->post('/admin/subject/add', 'SubjectController@add');
$router->post('/admin/subject/edit',   'SubjectController@edit');
$router->post('/admin/subject/delete', 'SubjectController@delete');

$router->get('/admin/student',            'StudentController@index');
$router->get('/admin/student/data',       'StudentController@getData');
$router->post('/admin/student/add',       'StudentController@add');
$router->post('/admin/student/edit',      'StudentController@edit');
$router->post('/admin/student/delete',    'StudentController@delete');
$router->post('/admin/student/import',    'StudentController@importStudents');
$router->get('/admin/student/template',   'StudentController@downloadTemplate');

$router->get('/admin/assign/student',               'AssignmentController@studentClass');
$router->get('/admin/assign/student/enrolled',      'AssignmentController@enrolledStudents');
$router->get('/admin/assign/student/data',          'AssignmentController@getStudentClassLinks');
$router->get('/admin/assign/student/available',     'AssignmentController@getAvailableStudents');
$router->post('/admin/assign/student/link',         'AssignmentController@enrollStudent');
$router->post('/admin/assign/student/unlink',       'AssignmentController@removeStudent');

$router->post('/admin/AdminLogin', 'AdminAuthController@login');

$router->get('/admin/AdminLogin', 'AdminAuthController@logout');

$router->get('/admin/AdminDashboard', 'AdminDashboardController@index');
$router->get('/admin/AdminDashboard/ping', 'AdminDashboardController@ping');

// User (Teacher) Routes
$router->get('/', 'UserAuthController@index');
$router->post('/user/login', 'UserAuthController@login');
$router->get('/user/logout', 'UserAuthController@logout');
$router->post('/user/change-password', 'UserAuthController@changePassword');
$router->get('/user/dashboard', 'UserDashboardController@index');
$router->get('/user/dashboard/stats', 'UserDashboardController@stats');
$router->get('/user/dashboard/ping',  'UserDashboardController@ping');
$router->get('/user/my-classes', 'UserClassController@index');
$router->get('/user/my-classes/stats', 'UserClassController@stats');
$router->get('/user/add-grade', 'UserGradeController@index');
$router->get('/user/grade/add/{classId}', 'UserGradeController@gradeForm');
$router->get('/user/grade/subjects/{classId}',   'UserGradeController@getSubjects');
$router->get('/user/grade/structure/{subjectId}', 'UserGradeController@getGradingStructure');
$router->get('/user/grade/existing',              'UserGradeController@fetchExistingGrades');
$router->post('/user/grade/save',                 'UserGradeController@saveGrades');
$router->get('/user/manage-grades',       'UserGradeController@manageGrades');
$router->get('/user/manage-grades/stats', 'UserGradeController@manageGradesStats');
$router->get('/user/grade/manage/{classId}',       'UserGradeController@manageGradeForm');
$router->get('/user/grade/export-check/{classId}', 'UserGradeController@exportCheck');
$router->get('/user/grade/export-data/{classId}',  'UserGradeController@exportData');

// Reports
$router->get('/user/reports',                  'UserReportController@index');
$router->get('/user/reports/section-data',     'UserReportController@sectionData');
$router->get('/user/reports/student-data',     'UserReportController@studentData');
$router->get('/user/reports/dashboard-chart',  'UserReportController@dashboardChart');

// Home page
// $router->get('/', 'HomeController@index');

// Using controllers
// $router->get('/about', 'HomeController@about');
// $router->get('/contact', 'HomeController@contact');
// $router->post('/contact', 'HomeController@submitContact');

// Dynamic routes with parameters
// $router->get('/user/{id}', 'UserController@show');
// $router->get('/post/{slug}', 'PostController@show');