<?php

use App\Http\Controllers\Clients\Models\Client;
use App\Http\Controllers\Invoices\Models\Invoice;
use App\Http\Controllers\Invoices\Models\InvoiceProduct;
use Illuminate\Support\Facades\Route;
use Elasticsearch\ClientBuilder;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/hi', function (){
//     $client = ClientBuilder::create()
//     ->setHosts(['es01:9200'])
//     ->build();

//     // Info API
//     $response = $client->info();
//     return response($response);
// });

Route::domain('company.' . env('SESSION_DOMAIN'))->group(function () {
    Route::get('/create-job/{company_pr_id}/{token}', 'SubDomains\Company\Job\JobController@createJob')
        ->middleware([
            //            \Illuminate\Routing\Middleware\ValidateSignature::class,
            \App\Http\Middleware\VerifyUrl::class
        ])
        ->name('company.job.create');
    Route::post('/store-job/{token}', 'SubDomains\Company\Job\JobController@store')
        ->middleware([
            //            \Illuminate\Routing\Middleware\ValidateSignature::class,
            \App\Http\Middleware\VerifyUrl::class
        ])
        ->name('company.job.store');
    Route::get('/verify-job/{job_pr_id}/{token}', 'SubDomains\Company\Job\JobController@verifyJob')
        ->middleware([
            //            \Illuminate\Routing\Middleware\ValidateSignature::class,
            \App\Http\Middleware\VerifyUrl::class
        ])
        ->name('company.job.verify');
    Route::post('/verified-job/{job_pr_id}/{token}', 'SubDomains\Company\Job\JobController@verifiedJob')
        ->middleware([
            //            \Illuminate\Routing\Middleware\ValidateSignature::class,
            \App\Http\Middleware\VerifyUrl::class
        ])
        ->name('company.job.verified');
});


Route::domain('candidate.' . env('SESSION_DOMAIN'))->group(function () {
    Route::get('/profile/{candidatecv_id}/{token}', 'SubDomains\Candidate\CV\CandidateCvController@profile')
        ->middleware([
            //            \Illuminate\Routing\Middleware\ValidateSignature::class,
            \App\Http\Middleware\VerifyUrl::class
        ])
        ->name('candidate.cv.profile');
    Route::post('/profile/{candidatecv_id}/confirm/{token}', 'SubDomains\Candidate\CV\CandidateCvController@confirm')
        ->middleware([
            //            \Illuminate\Routing\Middleware\ValidateSignature::class,
            \App\Http\Middleware\VerifyUrl::class
        ])
        ->name('candidate.cv.profile.confirm');
});



Route::view('create_event', 'createEvent');
Route::view('upload', 'uploadToDrive');

Route::get('companies/file/download/{id}', 'Companies\CompaniesController@downloadFile');
Route::get('candidate/cv/download/{id}', 'CandidateCV\CandidateCVController@downloadPdf');
Route::get('candidate/agreement/download/{id}', 'CandidateCV\CandidateCVAgreementsController@downloadAgreement');
Route::get('jobs/file/download/{id}', 'Jobs\JobsController@downloadFile')->name('jobs.file.download');
Route::get('google/calendar/oauth', 'GoogleApi\CalendarController@oauth')->name('google.calendar.oauthCallback');
Route::get('google/calendar/webhook', 'GoogleApi\CalendarController@webhook')->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)->name('google.calendar.webhook');
Route::get('agrello/oauth', 'Agrello\AgrelloController@oauth')->name('agrello.oauthCallback');
Route::post('agrello/webhook', 'Agrello\AgrelloController@webhook')->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)->name('agrello.webhook');
//Route::webhooks('webhook-receiving-url');
//Route::get('/auth', 'Agrello\AgrelloController@oauth')->name('agrello.oauthCallback');
Route::get('/upload-files', 'Agrello\AgrelloController@uploadFiles')->name('upload.files');



Route::get('/', function () {
    return view('welcome');
});

Route::get('/import', function () {
    $csv = array_map('str_getcsv', file('import.csv'));

    $Groups = [];
    $Categories = [];
    array_shift($csv);

    foreach ($csv as $k => $v) {
        $Groups[] = (empty($v[1])) ? 'OTHER' : $v[1];
    }

    $GroupsModel = new App\Http\Controllers\Temp\Group();
    $CategoriesModel = new App\Http\Controllers\Temp\Category();

    foreach (array_unique($Groups) as $group) {
        $GroupsModels[] = [
            'CompanyId' => 9,
            'ShopId' => 19,
            'DefaultVat' => 1,
            'ShortName' => $group,
            'Description' => $group,
            'Code' => $group,
            'CreatedAt' => new \DateTime(),
            'UpdatedAt' => new \DateTime()
        ];
    }
    $GroupsModel->insert($GroupsModels);
    $CategoriesModels = [];
    foreach ($csv as $entry) {
        $Cat = $entry[2];
        $exists = $CategoriesModel->where('Code', $Cat)->first();
        if (!$exists) {
            $Group = (empty($v[1])) ? 'OTHER' : $v[1];
            $Group = $GroupsModel->where('Code', $Group)->first();
            $Category = new App\Http\Controllers\Temp\Category();
            $Category->Code = $Cat;
            $Category->CompanyId = 9;
            $Category->ShopId = 19;
            $Category->DefaultVAT = 1;
            $Category->Name = $Cat;
            $Category->GroupId = $Group->id;
            $Category->CreatedAt = new \DateTime();
            $Category->UpdatedAt = new \DateTime();
            $Category->save();
        }
    }

    foreach ($csv as $k => $entry) {
        $Cat = $entry[2];
        $Group = (empty($entry[1])) ? 'OTHER' : $entry[1];
        $Group = $GroupsModel->where('Code', $Group)->first();
        $Category = $CategoriesModel->where('Name', $Cat)->first();
        $VatId = ($entry[7] == 5) ? 1 : 2;
        $VatPrice = ($entry[7] == 5) ? 5.0 : 19.0;
        $Product = new App\Http\Controllers\Temp\Product();
        $Product->Code = str_replace(' ', '-', $entry[2] . ' ' . $entry[3] . ' ' . $entry[4]);
        $Product->ShopId = 19;
        $Product->Name = $entry[2] . ' ' . $entry[3] . ' ' . $entry[4];
        $Product->GroupId = $Group->id;
        $Product->CategoryId1 = $Category->id;
        $Product->Price = $entry[6];
        $Product->VATId = $VatId;
        $Product->ProductJson = '{"Recipies":[],"PricingPolicies":[]}';
        $Product->ProductPrinters = '[]';
        $Product->PriceList = '[{"Id":1,"Name":"Retail","Currency":"EUR","IsDefault":true,"Price":' . $Product->Price . ',"VAT":' . $VatPrice . '},{"Id":2,"Name":"Take Away","Currency":"EUR","IsDefault":false,"Price":0.0,"VAT":0.0},{"Id":3,"Name":"Staff","Currency":"EUR","IsDefault":false,"Price":0.0,"VAT":0.00},{"Id":4,"Name":"Wholesale","Currency":"EUR","IsDefault":false,"Price":0.0,"VAT":0.00}]';
        $Product->CreatedAt = new \DateTime();
        $Product->UpdatedAt = new \DateTime();
        $Product->save();
    }
});
