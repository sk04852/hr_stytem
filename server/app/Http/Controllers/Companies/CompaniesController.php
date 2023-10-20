<?php

namespace App\Http\Controllers\Companies;

use App\Events\Timeline\Company\CompanyCreated;
use App\Events\Timeline\Company\CompanyUpdated;
use App\Exceptions\InvalidLocale;
use App\Http\Controllers\Companies\Models\Company;
use App\Http\Controllers\Companies\Models\CompanyContact;
use App\Http\Controllers\Companies\Models\CompanyFile;
use App\Http\Controllers\Companies\Models\CompanyIndustry;
use App\Http\Controllers\Companies\Models\CompanyLocation;
use App\Http\Controllers\Companies\Models\CompanyPr;
use App\Http\Controllers\Companies\Models\Rik;
use App\Http\Controllers\Companies\Requests\CompanySuggestionRequest;
use App\Http\Controllers\Companies\Requests\CreateCompanyContactRequest;
use App\Http\Controllers\Companies\Requests\CreateCompanyRequest;
use App\Http\Controllers\Companies\Requests\DeleteFileRequest;
use App\Http\Controllers\Companies\Requests\DeleteCompanyRequest;
use App\Http\Controllers\Companies\Requests\GetCompanyDataRequest;
use App\Http\Controllers\Companies\Requests\UpdateCompanyRequest;
use App\Http\Controllers\Companies\Services\CompaniesService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Timelines\Models\Timeline;
use App\Http\Controllers\Timelines\Traits\TimelineTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Companies\Filters\SearchFilter;


class CompaniesController extends Controller
{
    use TimelineTrait;

    const MODULE_NAME = 'Companies';
    const COMPANY_CREATED = 'New Company created successfully';
    const COMPANY_NOT_CREATED = 'Error in creating a Company';
    const COLLECTION_NAME = 'Companies';
    const COMPANY_UPDATED = 'Company updated successfully';
    const COMPANY_NOT_UPDATED = 'Error in updating Company';
    const COMPANY_DELETED = 'Company deleted successfully';
    const COMPANY_NOT_DELETED = 'Error in deleting Company';
    const COMPANY_FILE_DELETED = 'Company file deleted successfully';
    const COMPANY_FILE_NOT_DELETED = 'Error in deleting Company file';
    const COMPANY_ALREADY_MARKED = 'Company already marked';
    const COMPANY_NOT_FOUND = 'Company not found';
    const CANDIDATE_FILES_UPLOAD_ERROR = 'Company upload files not found';
    const CANDIDATE_FILES_INVALID_FORMAT = 'Company invalid upload files. only (docx, xls, csv, zip, pdf, jpg, png) allowed';
    const CANDIDATE_FILES_UPLOAD_DISK = 'candidatecv';
    const CANDIDATE_FILES_UPLOAD_PATH = 'uploads/companies/files';
    const COMPANY_CONTACT_CREATED = 'Company Contact is added successfully';
    const COMPANY_CONTACT_UPDATED = 'Company Contact is updated successfully';

    public function __construct(CompanyPr $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $templates = QueryBuilder::for($this->model)
                ->select([
                    'companies_pr.id AS id', 
                    'companies_pr.invoicing_info AS invoicing_info', 
                    'companies_pr.vat AS vat', 'c.name AS name'
                    ])
                ->join('companies AS c', function($join){
                    $join->on('companies_pr.id', '=', 'c.company_pr_id')->where('c.locale', app()->getLocale())->whereNull('c.deleted_at');
                })
                // ->join('company_locations AS cl', function($join){
                //     $join->on('companies_pr.id', '=', 'cl.company_pr_id')->where('c.locale', app()->getLocale())->whereNull('c.deleted_at');
                // })
                ->with([
                    'industries' => function ($query) {
                        $query->orderBy('name', 'asc');
                    },
                    // 'company' => function ($query) {
                    //     $query->locale();
                    // },
                    'companyContacts' => function ($query) {
                        $query->locale();
                    },
                    'companylocations' => function ($query) {
                        $query->orderBy('location', 'asc');
                        $query->locale();
                    }
                ])
                ->allowedFilters([
                    'invoicing_info',
                    AllowedFilter::exact('industry_id', 'industries.id'),
                    // AllowedFilter::partial('name', 'company.name'),
                    AllowedFilter::partial('name', 'c.name'),
                    AllowedFilter::partial('location', 'companylocations.location'),
                    AllowedFilter::custom('search', new SearchFilter)
                ])
                ->defaultSort('-id')
                ->allowedSorts('name')
                ->paginate($this->getPerPage())
                ->appends(request()->query());

            if ($templates->isNotEmpty()) {
                return $this->created(['data' => $templates]);
            }

            return $this->noRecord(['message' => CompaniesController::RECORD_NOT_FOUND, 'data' => []], 200);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function getCompaniesCandidates(Request $request)
    {
        try {
            $data = DB::table('timeline')->join('job', 'job.id', '=', 'timeline.job_ID')->join('candidatecv', 'candidatecv.id', '=', 'timeline.candidate_ID')->select('job.type_of_job', 'job.status', 'timeline.id', 'timeline.action_ID', 'timeline.candidate_ID', 'timeline.userpr_ID', 'timeline.action_name', 'timeline.date_added', 'timeline.created_at', 'timeline.updated_at', 'timeline.job_ID', 'timeline.action_name', 'timeline.comments', 'candidatecv.first_name', 'candidatecv.last_name', 'candidatecv.gender', 'candidatecv.phone', 'candidatecv.location', 'job.company')->where('job.company', $request->name)->latest("updated_at")->groupBy('id')->get();
            if ($data->isNotEmpty()) {
                return response()->json($data);
            }
            return $this->noRecord(['message' => CompaniesController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function store(CreateCompanyRequest $request)
    {
        try {
            $response = [];
            DB::transaction(function () use ($request, &$response) {
                $validated_data = $request->all();

                $available_locale = config('translatable.locales');
                $new_company = $this->model::create($request->only(['invoicing_info', 'address', 'vat']));

                unset($validated_data['invoicing_info']);
                //                unset($validated_data['industry_id']);
                //                 unset($validated_data['address']);
                unset($validated_data['vat']);
                unset($validated_data['files']);

                $industry_names = $validated_data['industry_names'];
                unset($validated_data['industry_names']);

                $contacts_arranged_data = [];
                $locations_arranged_data = [];
                $validated_data_locale = array_keys($validated_data);
                foreach ($validated_data['en']['contacts'] as $key => $contact) {
                    foreach ($validated_data_locale as $local_key => $locale) {
                        $contacts_arranged_data[$key][] = [
                            'company_pr_id' => $new_company->id,
                            'locale' => $locale,
                            'name' => $validated_data[$locale]['contacts'][$key]['name'],
                            'email' => $validated_data[$locale]['contacts'][$key]['email'],
                            'phone' => $validated_data[$locale]['contacts'][$key]['phone'],
                            'position' => (isset($validated_data[$locale]['contacts'][$key]['position'])) ? $validated_data[$locale]['contacts'][$key]['position'] : null
                        ];
                    }
                }
                foreach ($validated_data['en']['location'] as $key => $location) {
                    foreach ($validated_data_locale as $locale) {
                        $locations_arranged_data[$key][] = [
                            'company_pr_id' => $new_company->id,
                            'locale' => $locale,
                            'location' => $validated_data[$locale]['location'][$key]
                        ];
                    }
                }


                if (is_array($validated_data) && !empty($validated_data)) {
                    $validated_data = collect($validated_data);
                    foreach ($validated_data as $key => $record) {
                        if (in_array($key, $available_locale)) {

                            $new_company->company()->create([
                                'locale' => $key,
                                'name' => $record['company_name']
                            ]);
                        }
                    }

                    if (is_array($contacts_arranged_data) && !empty($contacts_arranged_data)) {
                        $parent_contact = null;
                        foreach ($contacts_arranged_data as $key => $single_user) {
                            foreach ($single_user as $single_locale) {
                                if ($parent_contact == null) {
                                    $parent_contact = CompanyContact::create($single_locale);
                                } else {
                                    $parent_contact->childCompanyContacts()->create($single_locale);
                                }
                            }
                            $parent_contact = null;
                        }
                    }

                    if (is_array($locations_arranged_data) && !empty($locations_arranged_data)) {
                        $parent_location = null;
                        foreach ($locations_arranged_data as $key => $single_location) {
                            foreach ($single_location as $single_locale) {
                                if ($parent_location == null) {
                                    $parent_location = CompanyLocation::create($single_locale);
                                } else {
                                    $parent_location->childCompanyLocations()->create($single_locale);
                                }
                            }
                            $parent_location = null;
                        }
                    }
                }

                if (isset($industry_names) && is_array($industry_names) && !empty($industry_names)) {
                    foreach ($industry_names as $industry_name) {
                        $industry_object = CompanyIndustry::firstOrCreate([
                            'name' => $industry_name
                        ]);

                        $new_company->industries()->attach($industry_object);
                    }
                }

                if ($request->hasFile('files')) {
                    $allowedfileExtension = ['docx', 'xls', 'csv', 'zip', 'pdf', 'PDF', 'jpg', 'JPG', 'jpeg', 'png'];
                    $files = $request->file('files');
                    foreach ($files as $file) {
                        $extension = strtolower($file->getClientOriginalExtension());
                        $check = in_array($extension, $allowedfileExtension);

                        if ($check) {
                            $new_company->companyFiles()->create([
                                'file_name' => $file->getClientOriginalName(),
                                'path' => $file->store(CompaniesController::CANDIDATE_FILES_UPLOAD_PATH, CompaniesController::CANDIDATE_FILES_UPLOAD_DISK)
                            ]);
                        } else {
                            return $this->failed(['message' => CompaniesController::CANDIDATE_FILES_INVALID_FORMAT]);
                        }
                    }
                }

                CompanyCreated::dispatch($new_company, 'Company Created');
                $response['company'] = $new_company->toArray();
            });
            $response['message'] = CompaniesController::COMPANY_CREATED;
            return $this->created($response);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function show(Request $request, $id, $full = null)
    {
        try {
            $company = $this->model::where('id', $id)
                ->when((is_null($full)), function ($query) {
                    return $query->with([
                        'industries',
                        'company' => function ($query) {
                            $query->locale();
                        },
                        'companyContacts' => function ($query) {
                            $query->locale();
                        },
                        'companylocations' => function ($query) {
                            $query->locale();
                        },
                        'companyFiles'
                    ]);
                })
                ->when(($full === 'complete-profile'), function ($query) {
                    return $query->with([
                        'industries',
                        'company',
                        'companyContacts',
                        'companylocations',
                        'companyFiles'
                    ]);
                })
                ->first();

            if (is_null($company)) {
                return $this->noRecord(['message' => CompaniesController::COMPANY_NOT_FOUND]);
            } else {
                return $this->created(['company' => $company]);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function update(UpdateCompanyRequest $request)
    {
        try {
            $response = false;
            $validated_data = $request->all();
            DB::transaction(function () use ($request, &$response, $validated_data) {
                $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
                //                $timeline_relations_data = [
                //                    'industry_id' => ['relation' => CompanyIndustry::class, 'identity_column' => 'id', 'record_column_name' => 'name']
                //                ];
                $company_pr = $this->model::where(['id' => $validated_data['company_pr_id']])->first();
                if (is_null($company_pr)) {
                    $this->noRecord(['message' => CompaniesController::COMPANY_NOT_FOUND]);
                }

                $company_pr->invoicing_info = $validated_data['invoicing_info'];
                //                 $company_pr->address = (is_null($validated_data['address'])) ? null : $validated_data['address'];
                $company_pr->vat = (is_null($validated_data['vat'])) ? null : $validated_data['vat'];
                //                $company_pr->industry_id = (is_null($validated_data['industry_id'])) ? null : $validated_data['industry_id'];
                if ($company_pr->isDirty()) {
                    $this->recordChanges($company_pr, $timeline_data);
                }
                if (!$company_pr->save()) {
                    throw new \Exception(CompaniesController::COMPANY_NOT_UPDATED);
                }
                $posted_old_files = (isset($validated_data['old_files'])) ? collect($validated_data['old_files']) : collect([]);
                $industries = (isset($validated_data['industries'])) ? collect($validated_data['industries']) : collect([]);
                unset($validated_data['company_pr_id']);
                unset($validated_data['invoicing_info']);
                unset($validated_data['vat']);
                unset($validated_data['industries']);
                unset($validated_data['files']);
                unset($validated_data['old_files']);

                $contacts_arranged_data = [];
                $locations_arranged_data = [];
                $validated_data_locale = array_keys($validated_data);
                foreach ($validated_data['en']['contacts'] as $key => $contact) {
                    foreach ($validated_data_locale as $local_key => $locale) {
                        $contacts_arranged_data[$key][] = [
                            'id' => $validated_data[$locale]['contacts'][$key]['id'],
                            'company_pr_id' => $company_pr->id,
                            'locale' => $locale,
                            'name' => $validated_data[$locale]['contacts'][$key]['name'],
                            'email' => $validated_data[$locale]['contacts'][$key]['email'],
                            'phone' => $validated_data[$locale]['contacts'][$key]['phone'],
                            'position' => (isset($validated_data[$locale]['contacts'][$key]['position'])) ? $validated_data[$locale]['contacts'][$key]['position'] : null
                        ];
                    }
                }
                foreach ($validated_data['en']['location'] as $key => $location) {
                    foreach ($validated_data_locale as $locale) {
                        $locations_arranged_data[$key][] = [
                            'id' => $validated_data[$locale]['location'][$key]['id'],
                            'company_pr_id' => $company_pr->id,
                            'locale' => $locale,
                            'location' => $validated_data[$locale]['location'][$key]['location']
                        ];
                    }
                }

                $available_locale = config('translatable.locales');
                if (is_array($validated_data) && !empty($validated_data)) {
                    foreach ($validated_data as $locale => $record) {
                        if (in_array($locale, $available_locale)) {
                            //Update Company Translatable
                            $flag = Company::find($record['company_id']);
                            $flag->fill(['name' => $record['company_name']]);
                            if ($flag->isDirty()) {
                                $this->recordChanges($flag, $timeline_data);
                            }
                            if (!$flag->save()) {
                                throw new \Exception(CompaniesController::COMPANY_NOT_UPDATED);
                            }
                        } else {
                            throw new InvalidLocale('Invalid Locale');
                        }
                    }
                }

                //Update Contacts
                if (is_array($contacts_arranged_data) && !empty($contacts_arranged_data)) {
                    $old_contacts = $company_pr->companyContacts;
                    $latest_contact_ids = collect([]);
                    foreach ($contacts_arranged_data as $key => $group_contact) {
                        if (is_array($group_contact) && !empty($group_contact)) {
                            $parent_contact = null;
                            foreach ($group_contact as $inner_key => $single_contact) {
                                $temp_contact_id = $single_contact['id'];
                                unset($single_contact['id']);

                                //Add New Contacts
                                if ($temp_contact_id == null) {
                                    if ($parent_contact == null) {
                                        $parent_contact = CompanyContact::create($single_contact);
                                        $this->recordOneToManyNew('companyContacts', $parent_contact->name, $timeline_data);
                                        $latest_contact_ids->push($parent_contact->id);
                                    } else {
                                        $single_contact['contact_ID'] = $parent_contact->id;
                                        $temp_new_contact = CompanyContact::create($single_contact);
                                        $this->recordOneToManyNew('companyContacts', $temp_new_contact->name, $timeline_data);
                                        $latest_contact_ids->push($temp_new_contact->id);
                                    }
                                }

                                //Update old Contacts
                                if ($temp_contact_id != null) {
                                    $latest_contact_ids->push($temp_contact_id);
                                    $flag = CompanyContact::find($temp_contact_id);
                                    $flag->fill($single_contact);
                                    if ($flag->isDirty()) {
                                        $this->recordOneToManyUpdate($flag, 'companyContacts', $timeline_data);
                                    }
                                    $flag->save();
                                }
                            }
                        }
                    }

                    $delete_contacts = $old_contacts->whereNotIn('id', $latest_contact_ids)->all();
                    if (is_array($delete_contacts) && !empty($delete_contacts)) {
                        foreach ($delete_contacts as $delete_contact) {
                            $this->recordOneToManyDelete($delete_contact, 'companyContacts', 'name', $timeline_data);
                            $delete_contact->delete();
                        }
                    }
                }

                //Update Locations
                if (is_array($locations_arranged_data) && !empty($locations_arranged_data)) {
                    $old_locations = $company_pr->companylocations;
                    $latest_location_ids = collect([]);
                    foreach ($locations_arranged_data as $key => $group_location) {
                        if (is_array($group_location) && !empty($group_location)) {
                            $parent_location = null;
                            foreach ($group_location as $inner_key => $single_location) {
                                $temp_location_id = $single_location['id'];
                                unset($single_location['id']);

                                //Add New Contacts
                                if ($temp_location_id == null) {
                                    if ($parent_location == null) {
                                        $parent_location = CompanyLocation::create($single_location);
                                        $this->recordOneToManyNew('companylocations', $single_location['location'], $timeline_data);
                                        $latest_location_ids->push($parent_location->id);
                                    } else {
                                        $single_location['location_ID'] = $parent_location->id;
                                        $latest_location_ids->push(CompanyLocation::create($single_location)->id);
                                        $this->recordOneToManyNew('companylocations', $single_location['location'], $timeline_data);
                                    }
                                }

                                //Update old Contacts
                                if ($temp_location_id != null) {
                                    $latest_location_ids->push($temp_location_id);
                                    $flag = CompanyLocation::find($temp_location_id);
                                    $flag->fill($single_location);
                                    if ($flag->isDirty()) {
                                        $this->recordOneToManyUpdate($flag, 'companylocations', $timeline_data);
                                    }
                                    $flag->save();
                                }
                            }
                        }
                    }

                    $delete_locations = $old_locations->whereNotIn('id', $latest_location_ids)->all();
                    if (is_array($delete_locations) && !empty($delete_locations)) {
                        foreach ($delete_locations as $delete_location) {
                            $this->recordOneToManyDelete($delete_location, 'companylocations', 'location', $timeline_data);
                            $delete_location->delete();
                        }
                    }
                }

                if ($industries->isNotEmpty()) {
                    $old_industries = $company_pr->industries;

                    $delete_industries = $old_industries->whereNotIn('id', $industries->whereNotNull('id')->pluck('id'));
                    if ($delete_industries->isNotEmpty()) {
                        foreach ($delete_industries as $delete_industry) {
                            $company_pr->industries()->detach($delete_industry);
                            $this->recordOneToManyDelete($delete_industry, 'industries', 'name', $timeline_data);
                        }
                    }

                    $new_industries = $industries->whereNull('id');
                    if ($new_industries->isNotEmpty()) {
                        foreach ($new_industries as $new_industry) {
                            $temp_new_industry = CompanyIndustry::firstOrCreate(['name' => $new_industry['name']]);
                            $company_pr->industries()->attach($temp_new_industry);
                            $this->recordOneToManyNew('industries', $temp_new_industry->name, $timeline_data);
                        }
                    }

                    $new_industries = $industries->whereNotNull('id');
                    if ($new_industries->isNotEmpty()) {
                        foreach ($new_industries as $new_industry) {
                            $is_exist = $old_industries->where('id', '=', $new_industry['id'])->first();
                            if (is_null($is_exist)) {
                                $temp_industry = CompanyIndustry::find($new_industry['id']);
                                if (!is_null($temp_industry)) {
                                    $company_pr->industries()->attach($temp_industry->id);
                                    $this->recordOneToManyNew('industries', $temp_industry->name, $timeline_data);
                                }
                            }
                        }
                    }
                }

                $latest_file_ids = collect();
                $old_files = $company_pr->companyFiles;
                if ($request->hasFile('files')) {
                    $allowedfileExtension = ['docx', 'xls', 'csv', 'zip', 'pdf', 'PDF', 'jpg', 'JPG', 'jpeg', 'png'];
                    $files = $request->file('files');
                    foreach ($files as $file) {
                        $extension = strtolower($file->getClientOriginalExtension());
                        $check = in_array($extension, $allowedfileExtension);

                        if ($check) {
                            $latest_file_ids->push(CompanyFile::create([
                                'company_pr_id' => $company_pr->id,
                                'file_name' => $file->getClientOriginalName(),
                                'path' => $file->store(CompaniesController::CANDIDATE_FILES_UPLOAD_PATH, CompaniesController::CANDIDATE_FILES_UPLOAD_DISK)
                            ])->id);
                            $this->recordOneToManyNew('companyFiles', $file->getClientOriginalName(), $timeline_data);
                        } else {
                            return $this->failed(['message' => CompaniesController::CANDIDATE_FILES_INVALID_FORMAT]);
                        }
                    }
                }

                if ($posted_old_files->isNotEmpty()) {
                    $latest_file_ids = $latest_file_ids->push($posted_old_files->pluck('id')->all())->flatten()->all();
                    //                    dd($latest_file_ids);
                    $delete_files = $old_files->whereNotIn('id', $latest_file_ids)->all();
                    if (is_array($delete_files) && !empty($delete_files)) {
                        foreach ($delete_files as $delete_file) {
                            $this->recordOneToManyDelete($delete_file, 'companyFiles', 'file_name', $timeline_data);
                            $delete_file->delete();
                        }
                    }
                } else {
                    $delete_files = $old_files->all();
                    if (is_array($delete_files) && !empty($delete_files)) {
                        foreach ($delete_files as $delete_file) {
                            $this->recordOneToManyDelete($delete_file, 'companyFiles', 'file_name', $timeline_data);
                            $delete_file->delete();
                        }
                    }
                }

                CompanyUpdated::dispatch($company_pr, 'Company Updated', $timeline_data['old_values'], $timeline_data['new_values']);
                $response = true;
            });

            if ($response) {
                return $this->created(['message' => CompaniesController::COMPANY_UPDATED]);
            } else {
                return $this->failed(['message' => CompaniesController::COMPANY_NOT_UPDATED]);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteCompanyRequest $request)
    {
        $ids = $request['id'];
        try {
            DB::transaction(function () use ($request, $ids) {
                foreach ($ids as $id) {
                    $company_pr = $this->findOneById($id);
                    if ($company_pr->jobs->isNotEmpty() && !$company_pr->jobs()->delete()) {
                        throw new \Exception(CompaniesController::COMPANY_NOT_DELETED);
                    }
                    if ($company_pr->companyFiles->isNotEmpty() && !$company_pr->companyFiles()->delete()) {
                        throw new \Exception(CompaniesController::COMPANY_NOT_DELETED);
                    }
                    if ($company_pr->companylocations->isNotEmpty() && !$company_pr->companylocations()->delete()) {
                        throw new \Exception(CompaniesController::COMPANY_NOT_DELETED);
                    }
                    if ($company_pr->companyContacts->isNotEmpty() && !$company_pr->companyContacts()->delete()) {
                        throw new \Exception(CompaniesController::COMPANY_NOT_DELETED);
                    }
                    if ($company_pr->company->isNotEmpty() && !$company_pr->company()->delete()) {
                        throw new \Exception(CompaniesController::COMPANY_NOT_DELETED);
                    }
                    if (!$company_pr->delete()) {
                        throw new \Exception(CompaniesController::COMPANY_NOT_DELETED);
                    }
                }
            });
            return $this->created(['message' => CompaniesController::COMPANY_DELETED]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function deleteFile(DeleteFileRequest $request)
    {
        $ids = $request['id'];
        try {
            DB::transaction(function () use ($request, $ids) {
                $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
                if (is_array($ids) && !empty($ids)) {
                    foreach ($ids as $id) {
                        $flag = CompanyFile::with('companyPr')->find($id);
                        $company_pr = $flag->companyPr;
                        $this->recordOneToManyDelete($flag, 'companyFiles', 'file_name', $timeline_data);
                        $flag_check = $flag->delete();
                        if (!$flag_check) {
                            throw new \Exception(CompaniesController::COMPANY_FILE_NOT_DELETED);
                        }

                        CompanyUpdated::dispatch($company_pr, 'Company Updated', $timeline_data['old_values'], $timeline_data['new_values']);
                    }
                }
            });
            return $this->created(['message' => CompaniesController::COMPANY_FILE_DELETED]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    //    public function fetchCompanyData(GetCompanyDataRequest $request)
    //    {
    //        try {
    //            $company_name = $request['company_name'];
    //
    //            $local_rik_data = Rik::where('response->nimi', $company_name)->get();
    //            if ($local_rik_data->isNotEmpty()) {
    //                $local_rik_data->each(function ($item, $key) {
    //                    $item->response = json_decode($item->response);
    //                });
    //                return $this->created(['data' => $local_rik_data]);
    //            } else {
    //                $data = collect();
    //                $url = "https://ariregxmlv6.rik.ee";
    //                $xml_request = simplexml_load_file(storage_path('app/request.xml'));
    //
    //                $xml_request->Body->ettevotjaRekvisiidid_v1->keha->ariregister_kasutajanimi = env('RIK_USERNAME');
    //                $xml_request->Body->ettevotjaRekvisiidid_v1->keha->ariregister_parool = env('RIK_PASSWORD');
    //                $xml_request->Body->ettevotjaRekvisiidid_v1->keha->nimi = $company_name;
    //
    //                $response = Http::withBody($xml_request->asXML(), 'application/xml')->post($url);
    //                $decoded_response = json_decode($response);
    //
    //                /**
    //                 * keha => the body
    //                 * ettevotjad => entrepreneurs
    //                 */
    //                if(isset($decoded_response->keha)){
    //                    if (is_array($decoded_response->keha->ettevotjad->item) && !empty($decoded_response->keha->ettevotjad->item)) {
    //                        foreach ($decoded_response->keha->ettevotjad->item as $item) {
    //                            Rik::create(['response' => json_encode($item)]);
    //                            $data->push($item);
    //                        }
    //                    }
    //                }
    //
    //                if($data->isEmpty()){
    //                    return $this->noRecord(['message' => CompaniesController::RECORD_NOT_FOUND]);
    //                }
    //
    //                return $this->created(['data' => $data]);
    //            }
    //
    //        } catch (Exception $ex) {
    //            return $this->serverError($ex);
    //        }
    //    }



    public function fetchCompanyData(GetCompanyDataRequest $request)
    {
        try {
            $company_name = $request['company_name'];
            // https://avaandmed.ariregister.rik.ee/en/open-data-api/introduction-api-services
            $url = "https://ariregxmlv6.rik.ee";
            $xml_request1 = simplexml_load_file(storage_path('app/ariregister/request.xml'));
            $xml_request1->Body->ettevotjaRekvisiidid_v1->keha->ariregister_kasutajanimi = env('RIK_USERNAME');
            $xml_request1->Body->ettevotjaRekvisiidid_v1->keha->ariregister_parool = env('RIK_PASSWORD');
            $xml_request1->Body->ettevotjaRekvisiidid_v1->keha->nimi = "%".$company_name."%";
            $response = Http::withBody($xml_request1->asXML(), 'application/xml')->post($url);

            return $this->created(['data' => json_decode($response)]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function fetchCompanyDetailData(GetCompanyDataRequest $request)
    {
        try {
            $company_name = $request['company_name'];

            $url = "https://ariregxmlv6.rik.ee";
            $xml_request2 = simplexml_load_file(storage_path('app/ariregister/detailCompanyRequest.xml'));
            $xml_request2->Body->detailandmed_v2->keha->ariregister_kasutajanimi = env('RIK_USERNAME');
            $xml_request2->Body->detailandmed_v2->keha->ariregister_parool = env('RIK_PASSWORD');
            $xml_request2->Body->detailandmed_v2->keha->arinimi = $company_name;
            $response = Http::withBody($xml_request2->asXML(), 'application/xml')->post($url);

            return $this->created(['data' => json_decode($response)]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function getJobs(Request $request, $id)
    {
        try {
            $company_pr_object = $this->model::where('id', $id)->first();

            if ($company_pr_object != null) {
                return $this->created(['jobs' => $company_pr_object->jobs()->with('jobs')->get()]);
            } else {
                return $this->noRecord(['message' => CompaniesController::COMPANY_NOT_FOUND], 200);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function getCandidates(Request $request, CompaniesService $companies_service, $id)
    {
        try {
            $company_pr_object = $this->model::where('id', $id)->first();

            if ($company_pr_object != null) {

                $candidates = $companies_service->getAllCompanyJobsApplicants($company_pr_object);

                return $this->created(['candidate' => $candidates]);
            } else {
                return $this->noRecord(['message' => CompaniesController::COMPANY_NOT_FOUND], 200);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function getTimeline(Request $request, $id)
    {
        try {
            $company = $this->model::where('id', $id)->first();

            if ($company != null) {
                return $this->created(['timeline' => $company->timeline()->orderBy('id', 'DESC')->get()]);
            } else {
                return $this->noRecord(['message' => CompaniesController::COMPANY_NOT_FOUND], 200);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function downloadFile($id)
    {
        try {
            $file = CompanyFile::findOrFail($id);

            $pathinfo = pathinfo(Storage::url($file->getRawOriginal('path'), PATHINFO_EXTENSION));
            if (!is_null($file)) {
                return response()->download('storage/' . $file->getRawOriginal('path'), $file->file_name);
            } else {
                return $this->noRecord(['message' => CompaniesController::COMPANY_NOT_FOUND], 200);
            }
        } catch (ModelNotFoundException $ex){
            Log::error($ex);
            return $this->noRecord(['message' => 'File not found']);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function timeline(Request $request, $company_pr_id)
    {
        try {
            $company_pr_object = $this->model::find($company_pr_id);
            if (is_null($company_pr_object)) {
                throw new ModelNotFoundException(Controller::RECORD_NOT_FOUND);
            }

            return $this->created(['timeline' => $company_pr_object->timeline()->with('userPr.user', 'comments')->orderBy('created_at', 'DESC')->get()]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function suggestions(CompanySuggestionRequest $request)
    {
        try {
            $company_name = $request->input('company_name');

            $companies = Company::with('companyPr.companylocations')->where('name', 'Like', "%$company_name%")->whereNull('deleted_at')->get();

            if ($companies->isNotEmpty()) {
                return $companies;
            }

            return $this->noRecord(['message' => CompaniesController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function getCompanyContacts(Request $request, $id)
    {
        try {

            $company_pr = $this->model::with('companyContacts')->findOrFail($id);

            return $this->created(['data' => $company_pr->companyContacts]);
        } catch (ModelNotFoundException $ex) {
            return $this->noRecord(['message' => 'Company Not Found']);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }
    public function addCompanyContact(CreateCompanyContactRequest $request, CompaniesService $company_service, $id)
    {
        try {
            $company_pr_object = $this->model::findOrFail($id);

            $validated_data = $request->all();
            
            $response = $company_service->addCompanyContact($company_pr_object, $validated_data);
            if(is_array($response) && !empty($response)){
                return $this->created(['message' => self::COMPANY_CONTACT_CREATED]);
            }
        } catch (ModelNotFoundException $ex) {
            return $this->noRecord(['message' => 'Company Not Found']);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }
    public function updateCompanyContact(Request $request, $id)
    {
        try {
            return $this->created(['data' => ['updateCompanyContact']]);
        } catch (ModelNotFoundException $ex) {
            return $this->noRecord(['message' => 'Company Not Found']);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }
}
