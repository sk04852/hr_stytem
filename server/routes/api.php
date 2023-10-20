
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Middleware\VerifyPassportToken;
use App\Http\Middleware\XssSanitization;

Route::get('/dashboard', 'GlobalController@dashboard');
Route::post('/global-search', 'GlobalController@globalSearch');

Route::post('/extract-keywords', function(Request $request){
    $posted_field = "olen 33-aastane cnc! operaator. pärit Tallinnast Oman C autojuhiluba";
    $response = extract_keywords($posted_field);
    return response($response);
});

Route::group(['prefix' => 'auth', 'middleware' => ['json.response', 'XssSanitizer']], function ($router) {
    // public routes
    Route::post('/login', 'Auth\ApiAuthController@login')->name('auth.login')->withoutMiddleware([VerifyPassportToken::class]);
    Route::post('/register','Auth\ApiAuthController@register')->name('auth.register')->withoutMiddleware([VerifyPassportToken::class]);
});

Route::group(['prefix' => 'auth', 'middleware' => ['json.response', 'auth:api', 'XssSanitizer']], function ($router) {
    // our routes to be protected will go in here
    Route::post('/logout', 'Auth\ApiAuthController@logout')->name('auth.logout');
    Route::get('/profile', 'Auth\ApiAuthController@profile')->name('auth.profile');
    Route::post('/update-profile', 'Auth\ApiAuthController@updateProfile')->name('auth.updateProfile');
    Route::post('/change-password', 'Auth\ApiAuthController@changePassword')->name('auth.changePassword');
});

Route::group(['prefix' => 'users', 'middleware' => ['json.response', 'auth:api', 'XssSanitizer']], function ($router) {
    Route::get('roles', 'Users\RolesController@index');
    Route::post('roles', 'Users\RolesController@store');
    Route::get('roles/{id}/{permissions?}', 'Users\RolesController@show');
    Route::post('roles/update', 'Users\RolesController@update');
    Route::post('roles/delete', 'Users\RolesController@delete');
    Route::post('roles/assign-permissions', 'Users\RolesController@assignPermissions');
    Route::get('permissions', 'Users\RolesController@permissions');
    Route::post('permissions', 'Users\UsersController@assignUserPermissions');

//    Route::get('/', 'Users\UsersController@index')->middleware('permission:Can-View-Users');
    Route::get('/', 'Users\UsersController@index');
    Route::post('/', 'Users\UsersController@store')->middleware('permission:Can-Add-User');
    Route::get('link-zoom-account/{id}', 'Users\UsersController@linkZoomAccount');
    Route::get('get-zoom-personal-link', 'Users\UsersController@getZoomPersonalLink');
    Route::get('/{id}', 'Users\UsersController@show');
    Route::post('/update', 'Users\UsersController@update')->middleware('permission:Can-Update-User');
    Route::post('/partial-update', 'Users\UsersController@partialUpdate')->middleware('permission:Can-Update-User');
    Route::post('/delete', 'Users\UsersController@destroy')->middleware('permission:Can-Delete-User');
    Route::post('/change-role', 'Users\UsersController@changeRole');

    Route::get('notifications/all/{offset?}/{limit?}', 'Users\UsersController@notifications');
    Route::get('notifications/unread-count', 'Users\UsersController@unreadNotificationsCount');
    Route::post('notifications/mark-read', 'Users\UsersController@markNotificationRead');
    Route::get('notifications/mark-read/all', 'Users\UsersController@markAllkNotificationRead');    
});


Route::group(['prefix' => 'languages', 'middleware' => ['json.response', 'auth:api', 'XssSanitizer']], function ($router) {
    Route::get('/', 'Languages\LanguagesController@index')->name('languages.index');
});

Route::group(['prefix' => 'timezones', 'middleware' => ['json.response', 'auth:api', 'XssSanitizer']], function ($router) {
    Route::get('/', 'Timezones\TimezonesController@index')->name('timezones.index');
});

Route::group(['prefix' => 'education-degrees', 'middleware' => ['json.response', 'auth:api', 'XssSanitizer']], function ($router) {
    Route::get('/', 'EducationDegrees\EducationDegreesController@index')->name('education_degrees.index');
});

Route::group(['prefix' => 'education-levels', 'middleware' => ['json.response', 'auth:api', 'XssSanitizer']], function ($router) {
    Route::get('/', 'EducationDegrees\EducationLevelsController@index')->name('education_levels.index');
});

Route::group(['prefix' => 'nationalities', 'middleware' => ['json.response', 'auth:api', 'XssSanitizer']], function ($router) {
    Route::get('/', 'Nationalities\NationalitiesController@index')->name('nationalities.index');
});

Route::group(['prefix' => 'mail', 'middleware' => ['json.response', 'auth:api', 'XssSanitizer']], function ($router) {
    Route::get('/wildcards', 'Mail\WildcardsController@index')->name('mail.wildcards.index');
    Route::get('/templates', 'Mail\TemplatesController@index')->name('mail.templates.index');
    Route::post('/templates', 'Mail\TemplatesController@store')->name('mail.templates.store')->withoutMiddleware(['XssSanitizer']);
    Route::get('/templates/{id}/{to}/{cc_id}', 'Mail\TemplatesController@show')->name('mail.templates.show');
    Route::post('/templates/update', 'Mail\TemplatesController@update')->name('mail.templates.update')->withoutMiddleware(['XssSanitizer']);
    Route::post('/templates/delete', 'Mail\TemplatesController@destroy')->name('mail.templates.delete')->withoutMiddleware(['XssSanitizer']);
    Route::post('/send-mail', 'Mail\SendMail@sendMail')->name('mail.templates.send-mail')->withoutMiddleware(['XssSanitizer']);
    Route::post('/send-company-mail', 'Mail\SendMail@sendCompanyMail')->name('mail.templates.send-company-mail')->withoutMiddleware(['XssSanitizer']);
});


Route::group(['prefix' => 'companies', 'middleware' => ['json.response', 'auth:api', 'XssSanitizer']], function (){
    Route::get('/industries', 'Companies\IndustriesController@index');
    Route::post('/industries/suggestions', 'Companies\IndustriesController@suggestions');
    Route::get('/', 'Companies\CompaniesController@index');
    Route::get('/getCompaniesCandidates/{name}', 'Companies\CompaniesController@getCompaniesCandidates');
    Route::post('/', 'Companies\CompaniesController@store');
    Route::get('/timeline/{id}', 'Companies\CompaniesController@timeline');
    Route::get('/{id}/jobs', 'Companies\CompaniesController@getJobs');
    Route::get('/{id}/candidates', 'Companies\CompaniesController@getCandidates');
    Route::get('/{id}/contacts', 'Companies\CompaniesController@getCompanyContacts');
    Route::post('/{id}/contact', 'Companies\CompaniesController@addCompanyContact');
    Route::put('/{id}/contact', 'Companies\CompaniesController@updateCompanyContact');
    Route::get('/{id}/{full?}', 'Companies\CompaniesController@show');
    Route::post('/update', 'Companies\CompaniesController@update');
    Route::post('/delete', 'Companies\CompaniesController@destroy');
    Route::post('/file/delete', 'Companies\CompaniesController@deleteFile');
    Route::post('/fetch-company-data', 'Companies\CompaniesController@fetchCompanyData');
    Route::post('/fetch-company-detail-data', 'Companies\CompaniesController@fetchCompanyDetailData');
    Route::post('/suggestion', 'Companies\CompaniesController@suggestions');

});

Route::group(['prefix' => 'hr-tasks', 'middleware' => ['json.response', 'auth:api', 'XssSanitizer']], function (){
    Route::get('/', 'HrTasks\HrTasksController@index');
    Route::post('/', 'HrTasks\HrTasksController@store');
    Route::get('/get-assigned-tasks', 'HrTasks\HrTasksController@getAssignedTasks');
    Route::get('/get-created-tasks', 'HrTasks\HrTasksController@getCreatedTasks');
    Route::post('/delete', 'HrTasks\HrTasksController@delete');
    Route::post('/mark-complete', 'HrTasks\HrTasksController@markComplete');
    Route::post('/mention', 'HrTasks\HrTasksController@getMention');

//    Route::get('/{id}/{full?}', 'Companies\CompaniesController@show');
});

Route::group(['prefix' => 'jobs', 'middleware' => ['json.response', 'auth:api', 'XssSanitizer']], function (){
    Route::get('/', 'Jobs\JobsController@index');
    Route::post('/', 'Jobs\JobsController@store');
    Route::get('/active', 'Jobs\JobsController@getActiveJobs');
    Route::post('/show/{id}', 'Jobs\JobsController@show');
    Route::get('/timeline/{id}', 'Jobs\JobsController@timeline');
    Route::post('/update', 'Jobs\JobsController@update');
    Route::post('/delete', 'Jobs\JobsController@destroy');
    Route::post('/video/delete', 'Jobs\JobsController@deleteVideo');
    Route::get('/find-best-match/{job_id}', 'Jobs\JobsController@findBestMatch');
    Route::get('/{job_pr_id}/candidates/{action_id?}', 'Jobs\JobsController@getCandidates');
    Route::get('/{job_pr_id}/duplicate', 'Jobs\JobsController@duplicateJob');
    Route::get('/file/delete/{id}', 'Jobs\JobsController@fileDelete');
    
//
//

//    Route::post('jobs/delete', 'Jobs\JobsController@destroy');
//    Route::get('jobs/find-best-match/{job_id}', 'Jobs\JobsController@findBestMatch');
//    Route::get('jobs/file/delete/{id}', 'Jobs\JobsController@fileDelete');
});

Route::group(['prefix' => 'zoom', 'middleware' => ['json.response', 'auth:api', 'XssSanitizer']], function (){
    Route::get('/', 'Zoom\ZoomController@index');
    Route::get('/users', 'Zoom\ZoomController@allUsers');
    Route::get('/users/{id}', 'Zoom\ZoomController@getUser');
    Route::get('/users/{id}/update', 'Zoom\ZoomController@updateUser');
    Route::get('/users/{id}/update-user-personal-link', 'Zoom\ZoomController@updateUserPersonalLink');
});

// Google api calender
Route::prefix('/google-calendar')->group(function () {
    Route::post('/fetch_access_token', 'GoogleApi\CalendarController@fetchAccessToken');
    Route::get ('/get-calendars', 'GoogleApi\CalendarController@getCalendars');
    Route::post ('/create-calendar', 'GoogleApi\CalendarController@createCalendar');
    Route::post ('/delete-calendar', 'GoogleApi\CalendarController@deleteCalendar');
    Route::post  ('/share-calendar', 'GoogleApi\CalendarController@shareCalendar');
    Route::get ('/sync-calendar', 'GoogleApi\CalendarController@syncUserCalendar');
    Route::get  ('/all-events', 'GoogleApi\CalendarController@allEvents');
    Route::post  ('/all-user-events', 'GoogleApi\CalendarController@allUserEvents');
    Route::post  ('/create-event', 'GoogleApi\CalendarController@createNewEvent');
    Route::post  ('/update-event', 'GoogleApi\CalendarController@updateEvent');
    Route::get  ('/delete-event/{event_id}', 'GoogleApi\CalendarController@deleteEvent');
    Route::post  ('/shared-with', 'GoogleApi\CalendarController@sharedCalendarWith');
    Route::post  ('/remove-shared-with', 'GoogleApi\CalendarController@removeSharedCalendarWith');
    Route::get  ('/logout', 'GoogleApi\CalendarController@logout');
});


// Timelines
Route::prefix('/timelines')->group(function () {
    Route::post('/comment/store', 'Timelines\TimelinesController@storeComment');
});

//Route::group(['prefix' => 'auth'], function ($router) {
//    // Client Frontend Apis
//    Route::post('client-login', 'Clients\ClientsController@login');
//    Route::post('login', 'AuthController@login');
//    Route::post('/update', 'Users\UsersController@update');
//    // Route::get('extended_profile/{id}', 'Users\UsersController@getExtendedProfile');
//    Route::post('temp_login', 'AuthController@temporaryLogin');
//    Route::post('logout', 'AuthController@logout');
//    Route::post('2fa/validate', 'AuthController@validateToken');
//    Route::post('register', 'Users\UsersController@register');
//    Route::get('refresh', '\App\Http\Controllers\AuthController@refresh');
//    Route::post('forgot_password', 'Users\UsersController@forgotPassword');
//    Route::post('reset_password', 'Users\UsersController@resetPassword');
//    Route::post('password', 'Users\UsersController@changePassword');
//    Route::get('app_data', 'Application\ApplicationController@appData');
//    Route::get('get_token_by_id/{id}', 'AuthController@getTokenById');
//    Route::get('verify/{token}', 'Users\UsersController@verifyEmail')->name('verify.email');
//});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('me', 'Users\UsersController@getExtendedProfile');


Route::post('create_class', 'Classes\ClassesController@store');

Route::get('fetch_subjects/{class_id}', 'Subjects\SubjectsController@fetchSubjectsUsingClassID');
Route::get('fetch_topics/{subject_id}', 'Topics\TopicsController@fetchTopicsUsingSubjectID');
Route::get('get_student_dashboard', 'Users\UsersController@getStudentDashboardData');



Route::group(['prefix' => 'open'], function ($router) {
    Route::get('classes', 'Classes\ClassesController@index');
    Route::get('subjects', 'Subjects\SubjectsController@index');
    Route::get('topics', 'Topics\TopicsController@index');
    Route::get('generate-pdf/{invoiceId}', 'Invoices\InvoicesController@generatePDF');
    Route::get('oauth', 'Calendar\gCalendarController@oauth')->name('oauthCallback');
    Route::get('drive/oauth', 'GoogleDrive\GoogleDriveController@oauth')->name('drive_oauthCallback');
    Route::get('google/sync/event/{id}', 'Calendar\gCalendarController@syncEvents');
    Route::get('google/sync/calendar', 'Calendar\gCalendarController@syncCalendars');
    Route::get('google/logout', 'Calendar\gCalendarController@logout');
    Route::get('drive/logout', 'GoogleDrive\GoogleDriveController@logout');
    Route::get('google/calendars', 'Calendar\gCalendarController@showCalendars');
    Route::get('drive/{query}', 'GoogleDrive\GoogleDriveController@search');
    Route::get('drive/delete/{id}', 'GoogleDrive\GoogleDriveController@delete');
    Route::post('drive/upload', 'GoogleDrive\GoogleDriveController@doUpload');
    Route::resource('google', 'Calendar\gCalendarController');
    Route::resource('drive', 'GoogleDrive\GoogleDriveController');
    Route::get('/scheduler', 'CalendarEvents\CalendarEventsController@scheduler');
    Route::post('chatbot/conversation', 'Chatbots\ChatbotsController@detect_intent_texts');
    Route::get('chatbot/start', 'Chatbots\ChatbotsController@start');
    Route::post('chatbot/end', 'Chatbots\ChatbotsController@end');
    Route::post('attendance/{code}/{type}', 'EmsAttendance\AttendanceController@markAttendanceUsingEmployeeCode');
    Route::get('test', function() {
    });
    Route::get('gettestreport', 'Reports\ReportsController@getTestReport');
    // open routes for reports
    Route::get('reports/transactions_report', 'Reports\ReportsController@tradingPlateFormAccount');
});


//Route::group(['prefix' => 'users'], function ($router) {
//    Route::get('/', 'Users\UsersController@listPeople');
//    Route::post('/', 'Users\UsersController@register');
//    // Route::put('/', 'Users\UsersController@update');
//    Route::post('/update', 'Users\UsersController@update');
//    Route::get('profile/{id}', 'Users\UsersController@profile');
//    Route::get('login_history', 'Users\UsersController@loginHistory');
//    Route::get('extended_profile/{id}', 'Users\UsersController@getExtendedProfile');
//    Route::get('{id}/groups', 'Users\UsersController@getUserGroups');
//    Route::delete('delete/{id}', 'Users\UsersController@destroy');
//    Route::post('change_password/{id}', 'Users\UsersController@changePassword');
//    Route::post('add_to_group', 'Users\UsersController@addUserToGroup');
//    Route::post('remove_from_group', 'Users\UsersController@removeUserFromGroup');
//
//    Route::post('add_role_to_group', 'Users\UsersController@FUNCtION_IS_READY_NEED_TO_ATTACH');
//    Route::post('remove_role_from_group', 'Users\UsersController@FUNCtION_IS_READY_NEED_TO_ATTACH');
//    Route::post('add_user_to_group', 'Users\UsersController@FUNCtION_IS_READY_NEED_TO_ATTACH');
//    Route::post('remove_user_from_group', 'Users\UsersController@FUNCtION_IS_READY_NEED_TO_ATTACH');
//    Route::post('is_user_allowed_to', 'Users\UsersController@FUNCtION_IS_READY_NEED_TO_ATTACH');
//    Route::post('get_group_permissions', 'Users\UsersController@FUNCtION_IS_READY_NEED_TO_ATTACH');
//    Route::post('get_groups_permissions', 'Users\UsersController@FUNCtION_IS_READY_NEED_TO_ATTACH');
//    Route::post('get_role_permissions', 'Users\UsersController@FUNCtION_IS_READY_NEED_TO_ATTACH');
//});

// 2FA ROUTES
//$router->post('generate_2fa_qrcode', 'Users\UsersController@generate2FAQrCode');
//$router->post('generate_2fa_email_code', 'Users\UsersController@generate2FAEmailCode');
//$router->post('enable_google_2fa', 'Users\UsersController@enableGoogle2FA');
//$router->post('enable_email_2fa', 'Users\UsersController@enableEmail2FA');
//$router->post('disable_email_2fa', 'Users\UsersController@disableEmail2FA');
//$router->post('disable_google_2fa', 'Users\UsersController@disableGoogle2FA');
//END 2FA ROUTES

Route::group(['prefix' => 'documents'], function ($router) {
    Route::resource('/', 'Documents\DocumentsController');
    Route::post('update', 'Documents\DocumentsController@update');
    Route::post('upload', 'Medias\MediasController@upload');
    Route::delete('delete/{id}', 'Documents\DocumentsController@destroy');
    Route::post('/mass_delete', 'Documents\DocumentsController@performMassDelete');
});

Route::group(['prefix' => 'media'], function ($router) {
    Route::post('upload', 'Media\MediasController@upload');
    Route::delete('delete/{id}', 'Medias\MediasController@destroyMedia');
    Route::post('directories', 'Medias\MediasController@storeDirectory');
    Route::get('directories', 'Medias\MediasController@listDirectories');
    Route::delete('directories/delete/{id}', 'Medias\MediasController@destroyDirectory');
    Route::get('entity/{module_id}/{relation_id}', 'Medias\MediasController@getMediaForEntity');
    Route::get('list', 'Medias\MediasController@getMedia');
    Route::get('serve/{filename}', 'Medias\MediasController@serve');
});

Route::group(['prefix' => 'people'], function ($router) {
    Route::get('lookup', 'PeopleLookup\PeopleLookupController@lookup');
});

// SMTP
Route::post('smtp_connections/send_test_email', 'SMTPConnections\SMTPConnectionsController@sendTestMail');
Route::post('smtp_connections/auth/{id}', 'SMTPConnections\SMTPConnectionsController@authenticate');
Route::post('smtp_connections/delete', 'SMTPConnections\SMTPConnectionsController@destroy');
Route::post('smtp_connections/update', 'SMTPConnections\SMTPConnectionsController@update');
Route::post('smtp_connections/mass_delete', 'SMTPConnections\SMTPConnectionsController@massDelete');
Route::post('smtp_connections/mass_update', 'SMTPConnections\SMTPConnectionsController@massUpdate');
Route::get('smtp_connections/search/{name}', 'SMTPConnections\SMTPConnectionsController@search');
Route::resource('smtp_connections', 'SMTPConnections\SMTPConnectionsController');

// Calendar Events
Route::delete('calendar_event', 'CalendarEvents\CalendarEventsController@destroy');
Route::post('calendar_event', 'CalendarEvents\CalendarEventsController@update');
Route::get('calendar_events/search/{name}', 'CalendarEvents\CalendarEventsController@search');
Route::post('calendar_events/mass_delete', 'CalendarEvents\CalendarEventsController@massDelete');
Route::post('calendar_events/mass_update', 'CalendarEvents\CalendarEventsController@massUpdate');
Route::resource('calendar_events', 'CalendarEvents\CalendarEventsController');

// Calendar Events user
Route::delete('eventusers', 'EventUsers\EventUsersController@destroy');
Route::resource('eventusers', 'EventUsers\EventUsersController');
Route::post('eventuser', 'EventUsers\EventUsersController@update');
Route::post('eventusers/mass_update', 'EventUsers\EventUsersController@massUpdate');
Route::post('eventusers/mass_delete', 'EventUsers\EventUsersController@massDelete');

//Calendar Module
Route::post('calendar/mass_delete', 'Calendar\CalendarController@doMassDelete');
Route::post('calendar/mass_update', 'Calendar\CalendarController@doMassUpdate');
Route::get('calendar/{title}', 'Calendar\CalendarController@searchByTitle');
Route::post('calendar/{id}', 'Calendar\CalendarController@update');
Route::post('calendar', 'Calendar\CalendarController@destroy');
Route::resource('calendars', 'Calendar\CalendarController');


//CalendarUser Module
Route::post('calendar_user/mass_delete', 'CalendarUser\CalendarUserController@doMassDelete');
Route::post('calendar_user/mass_update', 'CalendarUser\CalendarUserController@doMassUpdate');
Route::get('calendar_user/{user_id}', 'CalendarUser\CalendarUserController@searchByUserId');
Route::post('calendar_user', 'CalendarUser\CalendarUserController@update');
Route::post('delete_calendar_user', 'CalendarUser\CalendarUserController@destroy');
Route::resource('calendar_users', 'CalendarUser\CalendarUserController');

// Other Modules
Route::resource('field_options', 'Options\FieldOptionsController');
Route::post('field_option_types', 'Options\FieldOptionsController@createFieldOptionType');
Route::delete('field_option_types/{fieldOptionType}', 'Options\FieldOptionsController@deleteFieldOptionType');
Route::put('field_option_types/{id}', 'Options\FieldOptionsController@updateFieldOptionType');
Route::get('company_field_options', 'Options\FieldOptionsController@getFieldOptionsForCompany');

Route::resource('tasks', 'Tasks\TasksController');
Route::post('tasks/search/{data}', 'Tasks\TasksController@search');
Route::post('tasks/mass_delete', 'Tasks\TasksController@performMassDelete');
Route::post('tasks/mass_update', 'Tasks\TasksController@massUpdate');
Route::post('tasks/update_priority', 'Tasks\TasksController@performChangePriority');
Route::post('tasks/update_stage', 'Tasks\TasksController@performChangeStage');
Route::post('tasks/mass_update_priority', 'Tasks\TasksController@performMassChangePriority');
Route::post('tasks/mass_update_stage', 'Tasks\TasksController@performMassChangeStage');

// Products Start
Route::resource('vendor/products', 'VendorProducts\VendorProductsController');
Route::post('vendor/product/update', 'VendorProducts\VendorProductsController@update');
Route::post('vendor/products/search/{data}', 'VendorProducts\VendorProductsController@search');
Route::post('vendor/products/mass_delete', 'VendorProducts\VendorProductsController@performMassDelete');
Route::post('vendor/products/mass_update', 'VendorProducts\VendorProductsController@massUpdate');
Route::get('vendor/products/search/{name}', 'VendorProducts\VendorProductsController@search');
Route::post('vendor/product/status/{id}', 'VendorProducts\VendorProductsController@status');

//Vendor Groups
Route::post('vendor/groups/update', 'Vendors\VendorGroupsController@update');
Route::post('vendor/groups/{id}/delete', 'Vendors\VendorGroupsController@destroy');
Route::resource('vendor/groups', 'Vendors\VendorGroupsController');

//Vendor Categories
Route::post('vendor/categories/update', 'Vendors\VendorCategoriesController@update');
Route::post('vendor/categories/{id}/delete', 'Vendors\VendorCategoriesController@destroy');
Route::resource('vendor/categories', 'Vendors\VendorCategoriesController');


// Products End
Route::resource('comments', 'Comments\CommentsController');
Route::resource('emails', 'Emails\EmailsController');
Route::delete('comments', 'Comments\CommentsController@destroy');

//Email Templates
//Route::get('email_templates', 'EmailTemplates\EmailTemplatesController@index');
//Route::post('email_templates/store', 'EmailTemplates\EmailTemplatesController@store');
//Route::get('email_templates/search/{id}', 'EmailTemplates\EmailTemplatesController@show');
//Route::post('email_templates/update', 'EmailTemplates\EmailTemplatesController@update');
//Route::post('email_templates/mass_delete', 'EmailTemplates\EmailTemplatesController@performMassDelete');
//Route::delete('email_templates/delete/{id}', 'EmailTemplates\EmailTemplatesController@destroy');
//Route::post('email_templates/mass_update', 'EmailTemplates\EmailTemplatesController@performMassUpdate');
//Route::get('email_templates/search_template', 'EmailTemplates\EmailTemplatesController@performSearchByTemplateName'); // This Route get template_name from form input=template_name

//CalendarUser Module
Route::post('calendar_user/mass_delete', 'CalendarUser\CalendarUserController@doMassDelete');
Route::post('calendar_user/mass_update', 'CalendarUser\CalendarUserController@doMassUpdate');
Route::get('calendar_user/{user_id}', 'CalendarUser\CalendarUserController@searchByUserId');
Route::post('calendar_user', 'CalendarUser\CalendarUserController@update');
Route::post('delete_calendar_user', 'CalendarUser\CalendarUserController@destroy');
Route::resource('calendar_users', 'CalendarUser\CalendarUserController');

// Tikcet Module
Route::post('tickets/update', 'Tickets\TicketsController@update');
Route::post('tickets/mass_update', 'Tickets\TicketsController@massUpdate');
Route::post('tickets/mass_delete', 'Tickets\TicketsController@massDelete');
Route::post('tickets/mass_update_status', 'Tickets\TicketsController@massUpdateStatus');
Route::post('tickets/mass_update_priority', 'Tickets\TicketsController@massUpdatePriority');
Route::resource('tickets', 'Tickets\TicketsController');

Route::resource('transactions', 'Transactions\TransactionsController');
//Workflow Module
Route::resource('workflows', 'Workflows\WorkflowsController');
Route::post('workflow/update', 'Workflows\WorkflowsController@update');
Route::post('workflow/delete', 'Workflows\WorkflowsController@destroy');
Route::post('workflow/create_todo', 'Workflows\WorkflowTodosController@store');
Route::post('workflow/update_todo', 'Workflows\WorkflowTodosController@update');
Route::post('workflow/delete_todo', 'Workflows\WorkflowTodosController@destroy');
Route::get('workflow/show_todo', 'Workflows\WorkflowTodosController@index');
Route::get('workflow/show_todo/data/{todo}/{id}', 'Workflows\WorkflowTodosController@showTodoData');

Route::post('generic/{type}/update', 'Generics\GenericsController@update');

// Employee Time Off
Route::post('employees/timeoff', 'Timeoff\EmployeeTimeOffsController@store');
Route::get('employees/mytimeoff/requests', 'Timeoff\EmployeeTimeOffsController@myTimeoffRequests');
Route::post('employees/timeoff/update', 'Timeoff\EmployeeTimeOffsController@updateTimeOffRequest');
Route::get('employees/timeoff/balances', 'TimeOff\EmployeeTimeOffsController@timeOffBalances');
Route::get('admin/timeoff/requests', 'TimeOff\EmployeeTimeOffsController@adminTimeOffRequests');
Route::post('admin/timeoff/requests/update', 'Timeoff\EmployeeTimeOffsController@updateTimeOffRequest');

// Attendance Module For EMS
Route::post('attendance/update', 'EmsAttendance\AttendanceController@update');
Route::post('attendance/delete', 'EmsAttendance\AttendanceController@destroy');
Route::apiResource('/attendance', 'EmsAttendance\AttendanceController');
Route::get('attendance/profile/{id}', 'EmsAttendance\AttendanceController@profile');

// TimeOff types module for EMS
Route::get('timeoffs', 'TimeOff\TimeOffTypesController@index');
Route::post('timeoff/update', 'TimeOff\TimeOffsController@update');
Route::post('timeoff/delete', 'TimeOff\TimeOffsController@destroy');
Route::post('/timeoff', 'TimeOff\TimeOffTypesController@store');
Route::get('timeoff/profile/{id}', 'TimeOff\TimeOffsController@profile');
Route::post('timeoff/timeoff_request', 'TimeOff\TimeOffsController@timeoff_request');
Route::post('timeoff/Cancel_timeoff_request', 'TimeOff\TimeOffsController@Cancel_timeoff_request');
Route::post('timeoff/user_time_off_requests', 'TimeOff\TimeOffsController@user_time_off_requests');


//import Employee
Route::post('employees/import', 'EmsEmployee\ImportEmployeesController@store');


//Project
Route::get('projects', 'Project\ProjectsController@index');
Route::post('projects', 'Project\ProjectsController@store');
Route::post('projects/update', 'Project\ProjectsController@update');
Route::post('projects/delete', 'Project\ProjectsController@destroy');

//Holidays
Route::post('/holidays/update', 'Holidays\HolidaysController@update');
Route::resource('/holidays', 'Holidays\HolidaysController');

//Enable/Disable google_2fa Authenticator
//Route::group(['middleware' => 'auth:api'], function () {
//    Route::get('users', 'Users\UsersController@listUsers');
//    Route::post('users', 'Users\UsersController@register');
//});

//Policies Category
Route::get('policies_categories', 'PloiciesCategories\PoliciesCategoriesController@index');
Route::post('policies_categories', 'PloiciesCategories\PoliciesCategoriesController@store');
Route::post('policies_categorie', 'PloiciesCategories\PoliciesCategoriesController@update');

//Policies
Route::get('policies', 'Policies\PoliciesController@index');
Route::post('policies', 'Policies\PoliciesController@store');
Route::post('policy', 'Policies\PoliciesController@update');
Route::delete('policy', 'Policies\PoliciesController@destory');

//Employees Policies
Route::get('employees_policies', 'EmployeesPolicies\EmployeesPoliciesController@index');
Route::post('employees_policies', 'EmployeesPolicies\EmployeesPoliciesController@store');
Route::post('employee_policy', 'EmployeesPolicies\EmployeesPoliciesController@update');
Route::delete('employee_policy', 'EmployeesPolicies\EmployeesPoliciesController@destory');
Route::resource('timeoff_types', 'TimeOff\TimeOffTypesController');


//Holidays
Route::delete('heart_beat/delete', 'HeartBeats\HeartBeatsController@destroy');
Route::post('heart_beat/update', 'HeartBeats\HeartBeatsController@update');
Route::post('heart_beat/check', 'HeartBeats\HeartBeatsController@checkHeartBeat');
Route::resource('heart_beats', 'HeartBeats\HeartBeatsController');

//Companies
Route::post('company/change_password', 'Companies\CompaniesController@changePassword');
Route::post('update_company', 'Companies\CompaniesController@update');
Route::resource('companies', 'Companies\CompaniesController');

/* ===== Outlet Start =====*/
Route::post('update_outlet', 'Outlets\OutletsController@update');
Route::post('outlet/change_password', 'Outlets\OutletsController@changePassword');
Route::resource('outlets', 'Outlets\OutletsController');
/* ===== Outlet End =====*/

/* ===== Terminal Start =====*/
Route::post('update_terminal', 'Terminals\TerminalsController@update');
Route::resource('terminals', 'Terminals\TerminalsController');
/* ===== Terminal End =====*/

/* ===== Terminal Setting Start =====*/
Route::post('update_terminal_setting', 'Terminals\TerminalSettingsController@updateSetting');
/* ===== Terminal Setting End =====*/

/* ===== Terminal Printer Start =====*/
Route::post('terminal/update_printers', 'Terminals\TerminalPrintersSettingController@update');
Route::get('terminal/printers/{terminal_id}', 'Terminals\TerminalPrintersSettingController@index');
Route::get('terminal/printer/{id}', 'Terminals\TerminalPrintersSettingController@show');
Route::resource('terminal/printers', 'Terminals\TerminalPrintersSettingController');
/* ===== Terminal Printer End =====*/
/* ===== Sales Person Stock Start =====*/
Route::post('sales_person/stock/assign', 'SalesPersonStock\SalesPersonStockController@assignSalesPersonStocks');
Route::get('sales_person/stock/assign', 'SalesPersonStock\SalesPersonStockController@showAssignedSalesPersonStock');
Route::get('sales_persons', 'SalesPersonStock\SalesPersonStockController@showSalesPerson');
Route::post('sales_person/stock/return', 'SalesPersonStock\SalesPersonStockController@returnSalesPersonStocks');



// Import functiopnality
Route::post('import_parse', 'ImportData\ImportController@parseImport');
Route::post('import_process', 'ImportData\ImportController@processImport');


/* COPYING HERE, THESE SHOULD MOVE TO THERE OWN FILES */
//Bank Module
Route::post('bank/mass_delete', 'Bank\BankController@doMassDelete');
Route::post('bank/mass_update', 'Bank\BankController@doMassUpdate');
Route::get('bank/{bank_name}', 'Bank\BankController@searchByBankName');
Route::post('bank', 'Bank\BankController@update');
Route::post('delete_bank', 'Bank\BankController@destroy');
Route::apiResource('banks', 'Bank\BankController');
//BankAccount Module
Route::post('bank_account/mass_delete', 'BankAccount\BankAccountController@doMassDelete');
Route::post('bank_account/mass_update', 'BankAccount\BankAccountController@doMassUpdate');
Route::get('bank_account/{account_number}', 'BankAccount\BankAccountController@searchByAccountNumber');
Route::post('bank_account', 'BankAccount\BankAccountController@update');
Route::post('delete_bank_account', 'BankAccount\BankAccountController@destroy');
Route::resource('bank_accounts', 'BankAccount\BankAccountController');
//WireTransfer Module
Route::post('wire_transfer/mass_delete', 'WireTransfer\WireTransfersController@doMassDelete');
Route::post('wire_transfer/mass_update', 'WireTransfer\WireTransfersController@doMassUpdate');
Route::get('wire_transfer/{depositor_bank_name}', 'WireTransfer\WireTransfersController@searchByDepositorBankName');
Route::post('wire_transfer/update', 'WireTransfer\WireTransfersController@update');
Route::post('wire_transfer/delete', 'WireTransfer\WireTransfersController@destroy');
Route::apiResource('wire_transfers', 'WireTransfer\WireTransfersController');

Route::apiResource('trading_accounts', 'TradingAccounts\TradingAccountsController');
Route::apiResource('trading_platform_details', 'TradingAccounts\PlatformDetails\TradingAccountPlatformDetailsController');

Route::apiResource('trading_platforms', 'TradingPlatforms\TradingPlatformsController');
Route::post('trading_platform/update', 'TradingPlatforms\TradingPlatformsController@update');

//Dashboard Widgets
Route::get('lead_by_status', 'DashboardWidgets\DashboardWidgetsController@getLeadsByStatus');
Route::get('ticket_by_status', 'DashboardWidgets\DashboardWidgetsController@getTicketsByStatus');
Route::get('dashboard_items', 'DashboardWidgets\DashboardWidgetsController@getDashbaordItems');
Route::get('deposits_by_days', 'DashboardWidgets\DashboardWidgetsController@getDepositsByDays');
Route::get('monthly_deposits_widthdrawals', 'DashboardWidgets\DashboardWidgetsController@getMonthlyDepositsWidthdrawals');
Route::get('deposit_by_assignee', 'DashboardWidgets\DashboardWidgetsController@getDepositsByAssignee');
Route::get('daily_deposits_withdrawals', 'DashboardWidgets\DashboardWidgetsController@getDailyDepositsWithdrawals');


Route::get('search', 'Search\SearchController@index');

//Video Module
Route::get('video', 'Video\VideoController@index');
Route::post('video', 'Video\VideoController@store');
Route::post('video/delete', 'Video\VideoController@destroy');



Route::group(['prefix' => 'open'], function ($router) {

    //Actions
    Route::get('actions', 'Actions\ActionsController@index');
    Route::post('actions', 'Actions\ActionsController@store');
    Route::post('actions/update', 'Actions\ActionsController@update');
    Route::post('actions/delete', 'Actions\ActionsController@destroy');

    //CandidateCV
    Route::get('candidate_cv', 'CandidateCV\CandidateCVController@index');
    Route::post('candidate_cv', 'CandidateCV\CandidateCVController@store');
    Route::post('candidate_cv/show/{id}', 'CandidateCV\CandidateCVController@show');
    Route::get('candidate_cv/timeline/{id}', 'CandidateCV\CandidateCVController@timeline');
    Route::post('candidate_cv/update', 'CandidateCV\CandidateCVController@update');
    Route::post('candidate_cv/apply-to-job', 'CandidateCV\CandidateCVController@applyToJob');
//    Route::post('candidate_cv/update_action', 'CandidateCV\CandidateCVController@update_action');
    Route::post('candidate_cv/reject_candidate', 'CandidateCV\CandidateCVController@reject_candidate');
    Route::post('candidate_cv/delete', 'CandidateCV\CandidateCVController@destroy');
    Route::get('candidate_cv/getcountdata', 'CandidateCV\CandidateCVController@getcountdata');
    Route::get('candidate_cv/get-best-match/{candidatecv_id}', 'CandidateCV\CandidateCVController@findBestMatch');
    Route::post('candidate_cv/timeline_action_type', 'CandidateCV\CandidateCVController@timelineActionType');
    Route::post('candidate_cv/jobs-applied', 'CandidateCV\CandidateCVController@getJobsApplied');
    Route::post('candidate_cv/jobs-applied-previous-phase', 'CandidateCV\CandidateCVController@getJobsAppliedPreviousPhase');

    //Custom Candidate List
    Route::get('custom_candidate_list', 'CustomCandidate\CustomCandidateListController@index');
    Route::post('custom_candidate_list', 'CustomCandidate\CustomCandidateListController@store');
    Route::get('custom_candidate_list/show/{id}', 'CustomCandidate\CustomCandidateListController@show');
    Route::post('custom_candidate_list/update', 'CustomCandidate\CustomCandidateListController@update');
    Route::post('custom_candidate_list/delete', 'CustomCandidate\CustomCandidateListController@destroy');
    Route::post('custom_candidate_list/add-candidates', 'CustomCandidate\CustomCandidateListController@addCandidatesInList');
    Route::post('custom_candidate_list/add-multiple-candidates-multiple-lists', 'CustomCandidate\CustomCandidateListController@addMultipleCandidateMultipleList');
    Route::post('custom_candidate_list/remove-candidate', 'CustomCandidate\CustomCandidateListController@removeCandidateFromList');
    Route::post('custom_candidate_list/remove-multiple-candidates-multiple-lists', 'CustomCandidate\CustomCandidateListController@removeMultipleCandidateMultipleList');
    Route::post('custom_candidate/filter', 'CustomCandidate\CustomCandidateListController@filter');

    //Custom Candidate List Name
//    Route::post('custom_candidate_list_name/edit', 'CustomCandidate\CustomCandidateListController@edit_list');
    Route::post('custom_candidate_list_name/update', 'CustomCandidate\CustomCandidateListController@update_list');
//    Route::post('custom_candidate_list_name/delete', 'CustomCandidate\CustomCandidateListController@destroy_list');

    //Candidate Job History
    Route::get('candidate-job-history/{id}', 'CandidateCV\CandidateCVJobHistoryController@index');
    Route::post('candidate-job-history', 'CandidateCV\CandidateCVJobHistoryController@store');
    Route::post('candidate-job-history/update', 'CandidateCV\CandidateCVJobHistoryController@update');
    Route::post('candidate-job-history/delete', 'CandidateCV\CandidateCVJobHistoryController@destroy');

    //Candidate Education
    Route::get('candidate-education/{id}', 'CandidateCV\CandidateCVEducationController@index');
    Route::post('candidate-education', 'CandidateCV\CandidateCVEducationController@store');
    Route::post('candidate-education/update', 'CandidateCV\CandidateCVEducationController@update');
    Route::post('candidate-education/delete', 'CandidateCV\CandidateCVEducationController@destroy');

    //Additional Courses
    Route::get('candidate-additional-course/{id}', 'CandidateCV\CandidateCVAdditionalCoursesController@index');
    Route::post('candidate-additional-course', 'CandidateCV\CandidateCVAdditionalCoursesController@store');
    Route::post('candidate-additional-course/update', 'CandidateCV\CandidateCVAdditionalCoursesController@update');
    Route::post('candidate-additional-course/delete', 'CandidateCV\CandidateCVAdditionalCoursesController@destroy');

    //Candidate Skills
    Route::get('candidate-skills/{id}', 'CandidateCV\CandidateCVSkillController@index');
    Route::post('candidate-skills', 'CandidateCV\CandidateCVSkillController@store');
    Route::post('candidate-skills/update', 'CandidateCV\CandidateCVSkillController@update');
    Route::post('candidate-skills/delete', 'CandidateCV\CandidateCVSkillController@destroy');

    //Candidate Languages
    Route::get('candidate-languages/{id}', 'CandidateCV\CandidateCVLanguageController@index');
    Route::post('candidate-languages', 'CandidateCV\CandidateCVLanguageController@store');
    Route::post('candidate-languages/update', 'CandidateCV\CandidateCVLanguageController@update');
    Route::post('candidate-languages/delete', 'CandidateCV\CandidateCVLanguageController@destroy');

    //Candidate Files
    Route::get('candidate-files/{id}', 'CandidateCV\CandidateCVFilesController@index');
    Route::post('candidate-files', 'CandidateCV\CandidateCVFilesController@store');
//    Route::post('candidate-languages/update', 'CandidateCV\CandidateCVFilesController@update');
    Route::post('candidate-files/delete', 'CandidateCV\CandidateCVFilesController@destroy');


    //Candidate Agreements
    Route::get('candidate-agreements/{id}', 'CandidateCV\CandidateCVAgreementsController@index');
    Route::post('candidate-agreements', 'CandidateCV\CandidateCVAgreementsController@store');
//    Route::post('candidate-languages/update', 'CandidateCV\CandidateCVFilesController@update');
    Route::post('candidate-agreements/delete', 'CandidateCV\CandidateCVAgreementsController@destroy');

//    Download CV
//    Route::get('candidatecv-download-pdf/{id}', 'CandidateCV\CandidateCVController@downloadPdf');

    //Languages
    Route::get('languages', 'Languages\LanguagesController@index');
    Route::post('languages', 'Languages\LanguagesController@store');
    Route::post('languages/update', 'Languages\LanguagesController@update');
    Route::post('languages/delete', 'Languages\LanguagesController@destroy');

    //Tags
    Route::get('tags', 'Tags\TagsController@index');
    Route::post('tags', 'Tags\TagsController@store');
    Route::post('tags/update', 'Tags\TagsController@update');
    Route::post('tags/delete', 'Tags\TagsController@destroy');

    //Tasks
    Route::get('tasks', 'Tasks\TasksController@index');
    Route::post('tasks', 'Tasks\TasksController@store');
    Route::post('tasks/update', 'Tasks\TasksController@update');
    Route::post('tasks/delete', 'Tasks\TasksController@destroy');

    //CompanyProfiles
    Route::get('company_profiles', 'CompanyProfile\CompanyProfilesController@index');
    Route::post('company_profiles', 'CompanyProfile\CompanyProfilesController@store');
    Route::post('company_profiles/update', 'CompanyProfile\CompanyProfilesController@update');
    Route::post('company_profiles/delete', 'CompanyProfile\CompanyProfilesController@destroy');

    //Skills
    Route::get('skills', 'Skills\SkillsController@index');
    Route::post('skills', 'Skills\SkillsController@store');
    Route::post('skills/update', 'Skills\SkillsController@update');
    Route::post('skills/delete', 'Skills\SkillsController@destroy');

    //JobTags
    Route::get('job_tags', 'JobTags\JobTagsController@index');
    Route::post('job_tags', 'JobTags\JobTagsController@store');
    Route::post('job_tags/update', 'JobTags\JobTagsController@update');
    Route::post('job_tags/delete', 'JobTags\JobTagsController@destroy');

    //Jobs
    Route::get('jobs', 'Jobs\JobsController@index');
    Route::get('jobs/jobs_candidates/{id}', 'Jobs\JobsController@getJobsCandidates');
    Route::post('jobs', 'Jobs\JobsController@store');
    Route::post('jobs/update', 'Jobs\JobsController@update');
    Route::post('jobs/delete', 'Jobs\JobsController@destroy');
//    Route::get('jobs/find-best-match/{job_id}', 'Jobs\JobsController@findBestMatch');
    Route::get('jobs/file/delete/{id}', 'Jobs\JobsController@fileDelete');


    // JobsLang
    Route::get('jobs/desireLang', 'job_desired_lang\JobDesiredLangController@index');
    Route::post('jobs/desireLang', 'job_desired_lang\JobDesiredLangController@store');
    Route::post('jobs/desireLang/delete', 'job_desired_lang\JobDesiredLangController@destroy');
    Route::get('jobs/workLang', 'job_work_lang\JobworkLangController@index');
    Route::post('jobs/workLang', 'job_work_lang\JobworkLangController@store');
    Route::post('jobs/workLang/delete', 'job_work_lang\JobworkLangController@destroy');

    // JobShifts
    Route::get('jobs/shifts', 'job_shifts\JobshiftController@index');
    Route::post('jobs/shifts', 'job_shifts\JobshiftController@store');
    Route::post('jobs/shifts/delete', 'job_shifts\JobshiftController@destroy');

    // JobUpdate
    Route::get('jobupdate', 'JobsUpdate\JobUpdateController@index');
    Route::post('jobupdate', 'JobsUpdate\JobUpdateController@store');

    // JobActivity
    Route::get('jobsactivity','JobActivity\JobActivityController@index');
    Route::post('jobsactivity','JobActivity\JobActivityController@store');

    //Candidate Files
    Route::get('jobs-files/{id}', 'Jobs\JobsFilesController@index');
    Route::post('jobs-files', 'Jobs\JobsFilesController@store');
//    Route::post('candidate-languages/update', 'CandidateCV\CandidateCVFilesController@update');
    Route::post('jobs-files/delete', 'Jobs\JobsFilesController@destroy');

    //UserTags
    Route::get('user_tags', 'UserTags\UserTagsController@index');
    Route::post('user_tags', 'UserTags\UserTagsController@store');
    Route::post('user_tags/update', 'UserTags\UserTagsController@update');
    Route::post('user_tags/delete', 'UserTags\UserTagsController@destroy');

//    //UserRoles
//    Route::get('user_roles', 'UserRoles\UserRolesController@index');
//    Route::post('user_roles', 'UserRoles\UserRolesController@store');
//    Route::post('user_roles/update', 'UserRoles\UserRolesController@update');
//    Route::post('user_roles/delete', 'UserRoles\UserRolesController@destroy');


    //Permissions
    Route::get('permissions', 'Permissions\PermissionsController@index');
    Route::post('permissions', 'Permissions\PermissionsController@store');
    Route::post('permissions/update', 'Permissions\PermissionsController@update');
    Route::post('permissions/delete', 'Permissions\PermissionsController@destroy');

    //UserProfiles
    Route::get('user_profiles', 'UserProfiles\UserProfilesController@index');
    Route::post('user_profiles', 'UserProfiles\UserProfilesController@store');
    Route::post('user_profiles/update', 'UserProfiles\UserProfilesController@update');
    Route::post('user_profiles/delete', 'UserProfiles\UserProfilesController@destroy');
//    Route::post('timelines/adduser', 'Timelines\TimelinesController@adduser');


    //Timelines
//    Route::get('timelines', 'Timelines\TimelinesController@index');
//    Route::post('timelines', 'Timelines\TimelinesController@store');
//    Route::post('timelines/update', 'Timelines\TimelinesController@update');
//    Route::post('timelines/delete', 'Timelines\TimelinesController@destroy');
//    Route::get('timelines/getoffers/{id}', 'Timelines\TimelinesController@getoffers');
//    Route::get('timelines/getCandidateTimeline/{id}', 'Timelines\TimelinesController@getCandidateTimeline');
//    Route::get('timelines/getCompanyTimeline/{id}', 'Timelines\TimelinesController@getCompanyTimeline');
//    Route::get('timelines/getJobsTimeline/{id}', 'Timelines\TimelinesController@getJobsTimeline');
//    Route::post('timelines/updatecomment', 'Timelines\TimelinesController@updatecomment');



    //JobProfiles


    Route::get('job_profiles', 'JobProfiles\JobProfilesController@index');
    Route::post('job_profiles', 'JobProfiles\JobProfilesController@store');
    Route::post('job_profiles/update', 'JobProfiles\JobProfilesController@update');
    Route::post('job_profiles/delete', 'JobProfiles\JobProfilesController@destroy');

    //CandidateCVProfiles
    Route::get('candidate_cv_profiles', 'CandidateCVProfiles\CandidateCVProfilesController@index');
    Route::post('candidate_cv_profiles', 'CandidateCVProfiles\CandidateCVProfilesController@store');
    Route::post('candidate_cv_profiles/update', 'CandidateCVProfiles\CandidateCVProfilesController@update');
    Route::post('candidate_cv_profiles/delete', 'CandidateCVProfiles\CandidateCVProfilesController@destroy');

});


