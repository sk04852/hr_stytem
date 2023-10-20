<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HrTasks\Models\HrTask;
use App\Http\Controllers\HrTasks\Models\HrTaskStatus;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GlobalController extends Controller
{
    public function __construct()
    {
    }

    public function globalSearch(Request $request)
    {
        $validated = $request->validate([
            'search_query' => 'required'
        ]);

        try {
            $candidatecv = DB::table('candidatecv AS c')
                ->where(function ($query) use ($request) {
                    $query->where('c.first_name', 'like', "%$request->search_query%");
                    $query->orWhere('c.last_name', 'like', "%$request->search_query%");
                })
                ->whereNull('c.deleted_at')
                ->select('c.id AS id', DB::raw("concat(c.first_name, ' ', c.last_name) AS name"), DB::raw("'candidatecv' as type"), DB::raw("'Candidate' as Title"), DB::raw("'/hr-candidate/view/' as path"));


            $companies = DB::table('companies AS cp')
                ->join('companies_pr AS pr', function ($query) {
                    $query->on('cp.company_pr_id', '=', 'pr.id')->whereNull('pr.deleted_at');
                })
                ->where('cp.name', 'Like', "%$request->search_query%")
                ->whereNull('cp.deleted_at')
                ->select('pr.id AS id', 'cp.name as name', DB::raw("'companies' as type"), DB::raw("'Company' as Title"), DB::raw("'/hr-companies/view/' as path"));

            $result = DB::table('jobs AS j')
                ->join('jobs_pr AS jp', function ($query) {
                    $query->on('j.job_pr_id', '=', 'jp.id')->whereNull('jp.deleted_at');
                })
                ->whereNull('j.deleted_at')
                ->where('j.offer_name', 'Like', "%$request->search_query%")
                ->union($candidatecv)
                ->union($companies)
                ->select('jp.id as id', 'j.offer_name as name', DB::raw("'jobs' as type"), DB::raw("'Job' as Title"), DB::raw("'/hr-jobs/view/' as path"))
                ->get();

            if($result->isNotEmpty()){
                return $this->created(['data' => $result]);
            }else{
                return $this->created(['data' => []]);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function dashboard (Request $request) {
        try {
            $user_pr = Auth::user();

            $data['statistics']['created_tasks_counts'] = HrTask::where('created_by', '=', $user_pr->id)->count();
            $data['statistics']['assigned_tasks_counts'] = $user_pr->hrTasks()->where('hr_task_status_id', '<>', HrTaskStatus::COMPLETED_STATUS)->count();

            return $this->created(['data' => $data]);

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }


}
