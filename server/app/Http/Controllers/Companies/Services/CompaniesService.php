<?php

/**
 * CompaniesService
 *
 * @copyright Copyright © 2022 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

namespace App\Http\Controllers\Companies\Services;

use App\Events\Timeline\Company\CompanyUpdated;
use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\Companies\Models\CompanyContact;
use App\Http\Controllers\Companies\Models\CompanyPr;
use App\Http\Controllers\Timelines\Models\Timeline;
use App\Http\Controllers\Timelines\Traits\TimelineTrait;
use Illuminate\Support\Facades\DB;

class CompaniesService
{
    use TimelineTrait;

    public function getAllCompanyJobsApplicants(CompanyPr $company_pr_object)
    {

        $candidates = DB::table('candidatecv_jobs AS cj')
            ->select(DB::raw('cj.job_pr_id, c.id AS candidatecv_id, c.first_name, c.last_name, c.phone, j.title, j.offer_name, a.name as action_name,  cj.created_at AS applied_date'))
            //            ->join('candidatecv AS c', 'cj.candidatecv_id', '=', 'c.id')
            ->join('candidatecv AS c', function ($join) use ($company_pr_object) {
                $join->on('cj.candidatecv_id', '=', 'c.id')
                    ->whereNull('c.deleted_at');
            })
            ->join('jobs_pr AS jp', function ($join) use ($company_pr_object) {
                $join->on('cj.job_pr_id', '=', 'jp.id')
                    ->where('jp.company_pr_id', '=', $company_pr_object->id)
                    ->whereNull('jp.deleted_at');
            })
            ->join('jobs AS j', 'jp.id', '=', 'j.job_pr_id')
            ->join('actions AS a', 'cj.action_id', '=', 'a.id')
            ->whereNull('cj.deleted_at')
            ->get();

        return $candidates;
    }

    public function addCompanyContact(CompanyPr $company_pr_object, $contact_data)
    {
        $response = [];
        $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
        $available_locale = config('translatable.locales');

        $contacts_arranged_data = [];
        $validated_data_locale = array_keys($contact_data);

        foreach ($validated_data_locale as $local_key => $locale) {
            $contacts_arranged_data[] = [
                'company_pr_id' => $company_pr_object->id,
                'locale' => $locale,
                'name' => $contact_data[$locale]['contacts']['name'],
                'email' => $contact_data[$locale]['contacts']['email'],
                'phone' => $contact_data[$locale]['contacts']['phone'],
                'position' => (isset($contact_data[$locale]['contacts']['position'])) ? $contact_data[$locale]['contacts']['position'] : null
            ];
        }

        if (is_array($contacts_arranged_data) && !empty($contacts_arranged_data)) {
            $parent_contact = null;
            foreach ($contacts_arranged_data as $key => $single_user) {
                if ($parent_contact == null) {
                    $parent_contact = CompanyContact::create($single_user);
                    $this->recordOneToManyNew('companyContacts', $parent_contact->name, $timeline_data);
                    array_push($response, $parent_contact);
                } else {
                    $child_contact = $parent_contact->childCompanyContacts()->create($single_user);
                    $this->recordOneToManyNew('companyContacts', $child_contact->name, $timeline_data);
                    array_push($response, $child_contact);
                }

                $parent_contact = null;
            }
        }
        CompanyUpdated::dispatch($company_pr_object, 'Company Updated', $timeline_data['old_values'], $timeline_data['new_values']);

        return $response;
    }
}
