<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ActivityLog\Models\LogActivity;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Companies\Models\Company;
use App\Http\Controllers\Modules\Enums\LogTypeEnum;
use App\Http\Controllers\Modules\Model\Module;
use Illuminate\Support\Facades\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    const RECORD_NOT_FOUND = 'Record not found';
    const NO_RECORD = 'No Record';
    const RECORD_CREATED = 'New record has been created successfuly';
    const RECORD_NOT_CREATED = 'Record not created';
    const RECORD_UPDATED = 'Record has been updated successfuly';
    const RECORD_NOT_UPDATED = 'Record not updated';
    const MASS_RECORDS_UPDATED = 'Records has been updated successfuly';
    const RECORD_DELETED = 'Record has been deleted successfuly';
    const RECORD_NOT_DELETED = 'Record not deleted';
    const MASS_RECORDS_DELETED = 'Records has been deleted successfuly';
    const STATUS_UPDATED = 'Status has been Updated successfuly';
    const UNAUTHORIZED  = 'You are not authorized';

    protected $model = null;
    private $perPage = 10;
    private $sort = 'DESC';
    private $sortBy = 'id';
    private $log_;
    private $logger_;

    protected $created = 'Created';
    protected $updated = 'Updated';
    protected $deleted = 'Deleted';
    protected $cacheKey = 'Records';

    public function __construct(Model $model)
    {
        $this->model = $model;
        $loggerBuilder = app()->make('App\Http\Controllers\Log\LoggerBuilder');
        $this->logger_ = $loggerBuilder->getLogger();
        $this->userId();
        $this->companyId();
    }

    protected function serverError(Exception $exception, int $code = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        // if (app()->bound('sentry')) {
        //     app('sentry')->captureException($exception);
        // }
        // $this->addLog('Error', $exception->getMessage());

        return response()->json(
            [
                'error' => $exception->getMessage(),
                'ex' => $exception,
            ],
            $code
        );
    }

    protected function serverSQLError(Exception $exception, int $code = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        $this->addLog('Error', $exception->getMessage());
        return response()->json(
            [
                'error_state' => $exception->errorInfo[0],
                'error_code' => $exception->errorInfo[1],
                'error' => $this->getDatabaseErrorCodeInfo($exception)
            ],
            $code
        );
    }

    protected function getDatabaseErrorCodeInfo(Exception $exception)
    {
        $message = $exception->errorInfo[2];
        switch ($exception->errorInfo[1]) {
            case 1451:
                $message = 'Unable to delete record, related data to this record needs to be removed first';
                break;
            case 1452:
                $message = 'Cannot add or update a child row: a foreign key constraint fails';
                break;
        }
        return $message;
    }

    protected function created(array $data, int $code = Response::HTTP_OK)
    {
        return response()->json($data, $code);
    }

    protected function noRecord(array $data, int $code = Response::HTTP_NOT_FOUND)
    {
        return response()->json($data, $code);
    }

    protected function deleted(array $data, int $code = Response::HTTP_OK)
    {
        return response()->json($data, $code);
    }

    protected function failed(array $data, int $code = Response::HTTP_EXPECTATION_FAILED)
    {
        return response()->json($data, $code);
    }

    protected function notAllowed(array $data, int $code = Response::HTTP_UNAUTHORIZED)
    {
        return response()->json($data, $code);
    }

    protected function user()
    {
        return auth()->user();
    }

    protected function findOneById(int $id)
    {
        return $this->model->where('id', $id)->firstOrFail();
    }

    protected function findManyByIds(array $ids)
    {
        return $this->model->whereIn('id', $ids)->get();
    }

    protected function retrieveRecords(string $condition = '', $args = [], $sort = 'ASC', $sortBy = 'id')
    {
        return $this->model->orderBy($sortBy, $sort)->get();
    }

    protected function userId()
    {
        global $globals;
        $userId = ($this->user() ==  null) ? -1 : $this->user()->id;
        $globals['user_id'] = $userId;
        return (int) $userId;
    }

    protected function companyId()
    {
        global $app;
        $companyId = ($this->user() === null || empty($this->user()->company_id)) ? -1 : $this->user()->company_id;
        $app['company_id'] = $companyId;
        return (int) $companyId;
    }

    public function getLastCreatedEntity()
    {
        return $this->model;
    }

    public function getPerPage()
    {
        return request()->has('per_page') ? request()->get('per_page') : $this->perPage;
    }

    public function setPerPage(int $perPage)
    {
        $this->perPage = $perPage;
    }

    public function getSort()
    {
        return request()->has('sort') ? request()->get('sort') : $this->sort;
    }

    public function setSort(string $sort)
    {
        $this->sort = $sort;
    }

    public function getSortBy()
    {
        return request()->has('sort_by') ? request()->get('sort_by') : $this->sortBy;
    }

    public function setSortBy(string $sortBy)
    {
        $this->sortBy = $sortBy;
    }

    protected function type()
    {
        return 'User'; // Changed
    }

    protected function logActions($log_)
    {
        return $this->log_ = $log_;
    }

    protected function myData($user)
    {
        $id = NULL;
        switch ($user->type) {
            case 'Company':
                $model = Company::where('primary_email', '=', $user->email)->first();
                $id = $model->id;
                break;
        }
        return $id;
    }

    public function getModuleName($module_id)
    {
        $module = Module::find($module_id, ['name']);
        if ($module) {
            return $module->name;
        }
        return '--';
    }

    protected function addLog($type, $exceptionMessage = NULL, $oldData = NULL, $newData = NULL, $action = NULL, $module_id = NULL)
    {
        $log = [];
        $log['type'] = $type;
        $log['relation_id'] = isset($oldData) ? $oldData['id'] : null;
        $log['old_data'] = json_encode($oldData);
        $log['new_data'] = json_encode($newData);
        $log['action'] = $action;
        $log['message'] = $type == LogTypeEnum::Error ? $exceptionMessage : auth()->user()->first_name . ' ' . auth()->user()->last_name . ' ' . $action . ' ' . $this->getModuleName($module_id) . ' Record';
        $log['url'] = Request::fullUrl();
        $log['method'] = Request::method();
        $log['ip'] = Request::ip();
        $log['module_id'] = $module_id;
        $log['created_by_type'] = $type == LogTypeEnum::Error ? 'System' : 'User';
        $log['created_by'] = $type == LogTypeEnum::Error ? '0' : $this->userId();
        // $this->logger_->addLog($log);
        LogActivity::create($log);
    }

    public function isCanBeAccessByUser($companyId)
    {

        return $companyId == $this->companyId();
    }
}
