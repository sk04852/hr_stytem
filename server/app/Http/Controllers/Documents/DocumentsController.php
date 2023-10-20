<?php

namespace App\Http\Controllers\Documents;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Medias\MediasController;
use App\Http\Controllers\Documents\Models\Document as ThisModel;
use App\Http\Controllers\Documents\Requests\CreateDocumentRequest as CreateRequest;
use App\Http\Controllers\Documents\Requests\UpdateDocumentRequest as UpdateRequest;
use App\Http\Controllers\Documents\Requests\MassDeleteDocumentRequest;
use App\Http\Controllers\Modules\Enums\LogAction;
use App\Http\Controllers\Modules\Enums\LogTypeEnum;
use App\Http\Controllers\Modules\Enums\ModuleEnum;
use App\Http\Services\UserService;
use App\Http\Services\EmployeesService;
use App\Http\Services\MediaService;
use Illuminate\Http\Request;

require_once app_path() . '/Helpers/WP/plugin.php';
require_once app_path() . '/Helpers/WP/class-wp-error.php';
require_once app_path() . '/Helpers/WP/functions.php';
require_once app_path() . '/Helpers/WP/media.php';


class DocumentsController extends Controller
{
    const MODULE_NAME = 'document';
    const COLLECTION_NAME = 'documents';
    private $mediaController_ = null;
    private $userService_ = null;
    private $employeesService_ = null;
    private $mediaService_ = null;

    public function __construct(
        MediasController $mediaController,
        ThisModel $model,
        EmployeesService $employeesService,
        MediaService $mediaService,
        UserService $userService
    ) {
        parent::__construct($model);
        $this->mediaController_ = $mediaController;
        $this->userService_ = $userService;
        $this->employeesService_ = $employeesService;
        $this->mediaService_ = $mediaService;
    }

    public function index(Request $request)
    {
        try {
            $filters = [];

            if ($request->has('module')) {
                $filters['module'] = $request->get('module');
            }

            if ($request->has('relation_id')) {
                $filters['relation_id'] = $request->get('relation_id');
            }

            if ($request->has('created_by')) {
                $filters['created_by'] = $request->get('created_by');
            }

            if ($this->userService_->belongsToGroup($this->userId(), 4)) {
                $filters['company_id'] = $this->companyId();
            }

            if ($request->has('assigned_to')) {
                $filters['assigned_to'] = $request->get('assigned_to');
            }

            if ($request->has('document_no')) {
                $filters['document_no'] = $request->get('document_no');
            }

            if ($request->has('status')) {
                $filters['status'] = $request->get('status');
            }

            if ($request->has('issue_date')) {
                $filters['issue_date'] = $request->get('issue_date');
            }

            $records = $this->model
                ->documentFilters($filters)
                ->orderBy($this->getSortBy(), $this->getSort())
                ->paginate($this->getPerPage());

            $employeeRelationships = [];

            foreach ($records as $record) {
                if ($record->relation_type === 'Employee' && $record->relation_id !== NULL) {
                    $employeeRelationships[$record->id] = $record->relation_id;
                }
            }

            $employees  = [];

            if (count($employeeRelationships) > 0) {
                $employees = $this->employeesService_->getEmployeesByIds(array_unique(array_values($employeeRelationships)));
                $employees = $this->employeesService_->employeesToDigest($employees);
            }

            $employeesMap = [];
            if (count($employees)) {
                foreach ($employees as $employee) {
                    $employeesMap[$employee['id']] = $employee;
                }
            }

            if (count($employeesMap)) {
                foreach ($records as $record) {
                    if ($record->relation_type === 'Employee' && $record->relation_id !== NULL) {
                        $record->belongs_to = $employeesMap[$record->relation_id];
                    }
                }
            }

            return $this->created([DocumentsController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function create()
    {
        return;
    }

    public function store(CreateRequest $request)
    {
        try {
            $mediaIds = $request->media_id;
            $this->model->fill($this->model->getOnlyFillables($request->all()));
            $this->model->created_by = $this->userId();
            $this->model->assigned_to = $request->get('assigned_to', $this->userId());
            $this->model->company_id = $this->companyId();
            $this->model->relation_type = ($request->has('relation_type')) ? ucwords($request->get('relation_type')) : '';
            if ($this->model->save()) {
                $this->addLog(LogTypeEnum::Info, null, $this->model, $this->model, LogAction::Created, ModuleEnum::Documents);
                $this->mediaService_->createMediaRelationship(ModuleEnum::Documents, $this->model->id, $mediaIds);
                return $this->created([DocumentsController::MODULE_NAME => $this->model, 'message' => DocumentsController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->findOneById($id);
            return $this->created([DocumentsController::MODULE_NAME => $record]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function edit($id)
    {
        return;
    }

    public function update(UpdateRequest $request)
    {
        try {
            $record = $this->findOneById($request->id);
            $record->fill($request->all());
            if ($record->save()) {
                return $this->created([DocumentsController::MODULE_NAME => $record, 'message' => DocumentsController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        try {
            $record = $this->findOneById($id);
            if ($record && $record->media_id) {
                $this->mediaController_->delete($record->media_id);
            }

            if ($record->delete()) {
                $this->addLog(LogTypeEnum::Info, null, $record, null, LogAction::Deleted, ModuleEnum::Documents);
                return $this->created(['message' => DocumentsController::RECORD_DELETED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function performMassDelete(MassDeleteDocumentRequest $request)
    {
        try {
            $data = $request->all();
            $documentIds = $data['document_id'];
            foreach ($documentIds as $documentId) {
                $this->destroy($documentId);
            }
            return $this->created(['message' => DocumentsController::RECORD_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
