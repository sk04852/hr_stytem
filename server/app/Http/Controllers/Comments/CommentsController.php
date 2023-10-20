<?php

namespace App\Http\Controllers\Comments;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Http\Controllers\Comments\Models\Comment as ThisModel;
use App\Http\Controllers\Comments\Requests\CreateCommentRequest as CreateRequest;
use App\Http\Controllers\Comments\Requests\UpdateCommentRequest as UpdateRequest;
use App\Events\WorkflowEvent;
use App\Http\Controllers\Modules\Enums\LogAction;
use App\Http\Controllers\Modules\Enums\LogTypeEnum;
use App\Http\Controllers\Modules\Enums\ModuleEnum;
use App\Http\Controllers\Modules\Model\Module;
use DateTime;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    const MODULE_NAME = 'comment';
    const COLLECTION_NAME = 'comments';
    const MODULE_ID_REQUIRE = 'Module id is required';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            if (empty($request->module_id))
                throw new Exception(CommentsController::MODULE_ID_REQUIRE);

            $queryBuilder = $this->model->when(!empty($request->module_id), function ($query) use ($request) {
                return $query->where('module_id', $request->module_id);
            })
                ->when(!empty($request->relation_id), function ($query) use ($request) {
                    return $query->where('relation_id', $request->relation_id);
                })
                ->when(!empty($request->created_by), function ($query) use ($request) {
                    return $query->where('created_by', $request->created_by);
                })
                ->when(!empty($request->is_reply), function ($query) use ($request) {
                    return $query->where('is_reply', $request->is_reply);
                })
                ->when(!empty($request->reply_to), function ($query) use ($request) {
                    return $query->where('reply_to', $request->reply_to);
                })
                ->when(!empty($request->start_date), function ($query) use ($request) {
                    return $query->where('start_date', $request->start_date);
                })
                ->when(!empty($request->end_date), function ($query) use ($request) {
                    return $query->where('end_date', $request->end_date);
                });
            if (isset($filters['module'])) {
                if (!$filters['module'] === "CalendarEvent") {
                    $queryBuilder = $queryBuilder->withRelation();
                }
            }

            $records = $queryBuilder
                ->orderBy($this->getSortBy(), $this->getSort())
                ->withCommenter()
                ->paginate($this->getPerPage());
            return $this->created([CommentsController::COLLECTION_NAME => $records]);
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
            $uuid = Str::uuid();
            $comments = [];
            $relationIds = [];
            if (!$request->has('relation_ids')) {
                $relationIds[] = $request->get('relation_id');
            } else {
                $relationIds = $request->get('relation_ids');
            }

            $moduleId = 0;
            if ($request->has('module_id') && $request->module !== null) {
                $moduleId = $request->get('module_id');
            }

            if ($request->get('module')) {
                $module = Module::where('name', $request->module)->first();
                $moduleId = $module->id;
            }
            foreach ($relationIds as $relationId) {
                $comments[] = [
                    'uuid' => $uuid,
                    'module_id' => $moduleId,
                    'relation_id' => $relationId,
                    'created_by' => $this->userId(),
                    'last_commented_by' => $this->userId(),
                    'comment' => $request->get('comment'),
                    'is_reply' => $request->get('is_reply'),
                    'created_at' => new DateTime(),
                    'updated_at' => new DateTime()
                ];
            }
            if ($this->model->insert($comments)) {
                $this->addLog(LogTypeEnum::Info, null, $this->model, $comments, LogAction::Created, ModuleEnum::Comments);
                event(new WorkflowEvent(getModuleIdFromEntity($this->model), $this->model, 'create'));
                return $this->created([
                    CommentsController::MODULE_NAME => $this->model,
                    'message' => CommentsController::RECORD_CREATED,
                    'comment' => $this->getCommentByUUId($uuid)
                ]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->getCommentById($id);
            return $this->created([CommentsController::MODULE_NAME => $record]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    private function getCommentById(int $id)
    {
        return $this->model->where('id', $id)->withCommenter()->firstOrFail();
    }

    private function getCommentByUUId(string $uuid)
    {
        return $this->model->where('uuid', $uuid)->withCommenter()->firstOrFail();
    }

    public function edit($id)
    {
        return;
    }

    public function update(UpdateRequest $request, $id)
    {
        try {
            $record = $this->findOneById($id);
            $record->fill($request->all());
            $oldData = $record->getOriginal();
            if ($record->save()) {
                $this->addLog(LogTypeEnum::Info, null, $oldData, $record, LogAction::Updated, ModuleEnum::Comments);
                return $this->created([CommentsController::MODULE_NAME => $record, 'message' => CommentsController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        try {
            $record = $this->findOneById($id);
            if ($record->delete()) {
                $this->addLog(LogTypeEnum::Info, null, $record, null, LogAction::Deleted, ModuleEnum::Comments);
                return $this->created(['message' => CommentsController::RECORD_DELETED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
