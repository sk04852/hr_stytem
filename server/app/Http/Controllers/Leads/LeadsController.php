<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Comments\Models\Comment;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\Models\User;
use App\Http\Controllers\Leads\Models\Lead;
use App\Http\Controllers\Leads\Models\LeadComment;
use App\Http\Controllers\Leads\Models\LeadMarketingDetail;
use App\Http\Controllers\Leads\Requests\CreateLeadRequest;
use App\Http\Controllers\Leads\Requests\TransferOwnershipRequest;
use App\Http\Controllers\Leads\Requests\LeadMassDeleteRequest;
use App\Http\Controllers\Leads\Requests\LeadMassCommentRequest;
use App\Http\Controllers\Leads\Requests\UpdateLeadRequest;
use App\Http\Controllers\Leads\Requests\LeadCommentRequest;
use App\Http\Controllers\Leads\Requests\UpdateLeadCommentRequest;
use App\Http\Controllers\Leads\Requests\DeleteLeadCommentRequest;
use App\Http\Controllers\Modules\Enums\LogAction;
use App\Http\Controllers\Modules\Enums\LogTypeEnum;
use App\Http\Controllers\Modules\Enums\ModuleEnum;

class LeadsController extends Controller
{
    private $model_ = NULL;
    private $comment_ = NULL;
    private $userModel_ = NULL;

    const LEAD_NOT_FOUND = 'Lead not found, invalid lead';
    const LEAD_DELETED = 'Lead has been deleted';
    const LEADS_DELETED = 'Leads have been deleted';
    const LEAD_COMMENT_ADDED = 'Comment added';
    const LEAD_COMMENTS_ADDED = 'Comments have been added';
    const OPERATION_FAILED = 'Operation failed, please try again later';
    const LEAD_NOT_DELETED = 'Unable to delete lead';
    const LEAD_UPDATED = 'Lead data updated';
    const LEAD_CREATED = 'Lead created successfuly';
    const LEAD_COMMENT_CREATED = 'A new comment has been posted';
    const LEAD_COMMENT_UPDATE = 'Comment has been updated';
    const LEAD_COMMENT_DELETED = 'Comment has been deleted';
    const LEAD_COMMENT_NOT_FOUND = 'Invalid comment, unable to update';
    const LEAD_NOT_UPDATED = 'Unable to update lead data';
    const LEAD_OWNERSHIP_CHANGED = 'Leads ownership has been changed';
    const LEAD_STATUS_NEW = 'New';

    private $fieldsToFindDuplicateLead = [
        'primary_email',
        'secondary_email',
        'primary_phone',
        'mobile'
    ];

    public function __construct(Lead $model, LeadComment $comment, User $userModel)
    {
        parent::__construct($model);
        $this->model_ = $model;
        $this->comment_ = $comment;
        $this->userModel_ = $userModel;
    }

    public function listAll(Request $request)
    {

        $allLeads = $this->model_->leadsFilters($request)->with(["leadStatus", "user", "leadMarketingDetails", "leadComments","RegistrationCountry"])->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
        return $this->created(['leads' => $allLeads, 'count' => $allLeads->count()]);
    }

    public function create(CreateLeadRequest $request, Lead $lead, LeadMarketingDetail $marketingData)
    {
        try {
            $data = $request->all();
            if (!empty($request->assigned_to)) {
                $assignedTo = $request->assigned_to;
            } else {
                $assignedTo = $this->userId();
            }
            $lead->fill($data);
            $marketingData->fill($data);
            $lead->setDefaultLeadStatus();
            $lead->setCreatedBy($this->userId());
            $lead->setAssignedTo($assignedTo);
            $lead->setLastUpdatedBy($this->userId());
            if ($this->isDuplicate($data)) {
                $lead->markAsDuplicate();
            }
            if ($lead->save()) {
                $this->addLog(LogTypeEnum::Info, null, $lead, $lead, LogAction::Created, ModuleEnum::Leads);
                $lead->leadMarketingDetails()->save($marketingData);
                $this->addLog(LogTypeEnum::Info, null, $marketingData ,$marketingData, LogAction::Created, ModuleEnum::LeadMarketingDetail);
                return $this->created(['lead' => $this->findById($lead->id), 'message' => LeadsController::LEAD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function getOne(int $id, Lead $lead)
    {
        $lead = $this->findById($id, ["leadMarketingDetails", "leadComments"]);
        if ($lead == null)
            return $this->noRecord(['messsage' => LeadsController::LEAD_NOT_FOUND]);
        else
            return $this->created(['lead' => $lead]);
    }

    public function getCommentsByLeadId(int $id)
    {
        $lead =  $this->model_->find($id);
        if (!$lead) {
            return $this->noRecord(['messsage' => LeadsController::LEAD_NOT_FOUND]);
        }
        return $lead->with(['leadComments.comment'])->first();
    }

    public function update(UpdateLeadRequest $request, Lead $lead, LeadMarketingDetail $marketingData)
    {
        try {
            $auth = auth()->user();
            $data = $request->all();
            $leadId = $data['id'];
            $lead = $this->findById($leadId);
            $oldData = $lead->getOriginal();
            $marketingDataExists = $marketingData->where('lead_id', $leadId)->first();
            $oldMarketingDetails = $marketingDataExists->getOriginal();
            $lead->setLastUpdatedBy($auth->id);
            if ($lead->update($data)) {
                $this->addLog(LogTypeEnum::Info, null, $oldData, $lead, LogAction::Updated, ModuleEnum::Leads);
                if ($this->leadContainsMarketingDetails($data)) {
                    if ($marketingDataExists) {
                        $data['lead_id'] = $leadId;
                       $updatedLeadMarketingDetail = $lead->updateLeadMarketingDetails($marketingDataExists, $data);
                        $this->addLog(LogTypeEnum::Info, null, $oldMarketingDetails, $updatedLeadMarketingDetail, LogAction::Updated, ModuleEnum::LeadMarketingDetail);
                    } else {
                        $marketingData = new LeadMarketingDetail($data);
                        $lead->saveLeadMarketingDetails($marketingData);
                        $this->addLog(LogTypeEnum::Info, null, $oldMarketingDetails, $marketingData, LogAction::Updated, ModuleEnum::LeadMarketingDetail);
                    }
                }
                return $this->created(['lead' => $this->findById($lead->id), 'message' => LeadsController::LEAD_UPDATED]);
            } else {
                return $this->failed(['message' => LeadsController::LEAD_NOT_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(int $id)
    {
        try {
            $lead = $this->findById($id, ["leadMarketingDetails", "leadComments"]);
            if (!$lead) {
                return $this->noRecord(['messsage' => LeadsController::LEAD_NOT_FOUND]);
            }
            if ($this->delete($id)) {
                $this->addLog(LogTypeEnum::Info, null, $lead, null, LogAction::Deleted, ModuleEnum::Leads);
                return $this->deleted(['messsage' => LeadsController::LEAD_DELETED]);
            } else {
                return $this->failed(['message' => LeadsController::LEAD_NOT_DELETED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function delete(int $id)
    {
        if ($id == null)
            throw new Exception(LeadsController::LEAD_NOT_FOUND);
        $lead = $this->findById($id);
        if ($lead == null)
            throw new Exception(LeadsController::LEAD_NOT_FOUND);
        return $lead->delete();
    }

    public function performTransferOwnership(TransferOwnershipRequest $request)
    {
        try {
            $data = $request->all();
            $leadIds = $data['lead_id'];
            $ownerId = $data['owner_id'];
            $this->transferOwnership($leadIds, $ownerId);
            return $this->created(['message' => LeadsController::LEAD_OWNERSHIP_CHANGED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function transferOwnership(array $leadIds, int $ownerId)
    {
        $this->model_->whereIn('id', $leadIds)->update(['assigned_to' => $ownerId]);
    }

    public function performMassDelete(LeadMassDeleteRequest $request, Comment $commentModel)
    {
        try {
            $data = $request->all();
            $leadIds = $data['lead_id'];
            $this->massDelete($leadIds, $commentModel);
            return $this->created(['message' => LeadsController::LEADS_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function massDelete(array $leadIds, Comment $commentModel)
    {
        $commentIds = [];
        $leads =  $this->model_->whereIn('id', $leadIds);
        $leadComments = $this->comment_->whereIn('id', $leadIds)->get();
        foreach ($leadComments as $leadComment) {
            array_push($commentIds, $leadComment->id);
        }
        $commentModel->whereIn('id', $commentIds)->delete();
        $leads->delete();
    }

    public function performMassComment(LeadMassCommentRequest $request)
    {
        try {
            $data = $request->all();
            $leadIds = $data['lead_id'];
            $comment = $data['comment'];
            $this->addMassComment($leadIds, $comment, $this->user()->id);
            return $this->created(['message' => LeadsController::LEAD_COMMENTS_ADDED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function addMassComment(array $leadIds, string $comment, int $created_by)
    {
        $leads = $this->getLeadsByIds($leadIds);
        $leadCommentData = [];
        $newComment = new Comment();
        $moduleId =  getModuleIdFromEntity($newComment);
        $leadCommentModuleId =  getModuleIdFromEntity($this->comment_);
        $insertedComment =  Comment::create([
            'comment' => $comment,
            'created_by' => $created_by,
            'relation_id' => $leadCommentModuleId,
            'module_id' => $moduleId,
        ]);
        foreach ($leads as $lead) {
            $leadCommentData[] = [
                'lead_id' => $lead->id,
                'comment_id' => $insertedComment->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        LeadComment::insert($leadCommentData);
    }

    public function findById(int $leadId, array $withEntities = [])
    {
        $lead = $this->model_->where('id', $leadId);
        if ($lead) {
            return (count($withEntities) > 0) ?
                $lead->with($withEntities)->first() :
                $lead->first();
        } else
            return null;
    }

    public function getLeadsByIds(array $leadIds)
    {
        return $this->model_->whereIn('id', $leadIds)->get();
    }

    public function isDuplicate(array $data, $fields = [])
    {
        $searchAbleFields = (count($fields) > 0) ? $fields : $this->fieldsToFindDuplicateLead;
        $duplicates =
            $this->model_->where(function ($query) use ($data, $searchAbleFields) {
                foreach ($searchAbleFields as $field) {
                    if (isset($data[$field]))
                        $query->orWhere($field, $data[$field]);
                }
                return $query;
            })->count();
        return $duplicates > 0 ? true : false;
    }

    public function findDuplicates(Request $request)
    {
        $searchAbleFields = $request->fields;

        $duplicates = Lead::query();
        foreach ($searchAbleFields as $key => $field) {
            $duplicates->whereIn($field, function ($q) use ($field) {
                $q->select($field)
                    ->from('leads')
                    ->groupBy($field)
                    ->havingRaw('COUNT(*) > 1');
            });
        }
        $duplicates->with(["leadMarketingDetails", "leadComments"])->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());

        return $this->created(['leads' => $duplicates->get()]);
    }

    private function generateLeadId()
    {
        return Str::uuid();
    }

    public function findCommentById(int $commentId, array $withEntities = [])
    {
        $comment = LeadComment::where('id', $commentId);
        if ($comment) {
            return (count($withEntities) > 0) ?
                $comment->with($withEntities)->first() :
                $comment->first();
        } else
            return null;
    }

    public function comment(LeadCommentRequest $request)
    {
        try {
            $data = $request->all();
            $this->addCommentByLeadId($data["lead_id"], $data["comment"]);
            return $this->created([
                'comment' => $this->findById($data["lead_id"])->leadComments,
                'message' => LeadsController::LEAD_COMMENT_CREATED
            ]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function addCommentByLeadId(int $leadId, string $comment)
    {
        $auth = auth()->user();
        $lead = $this->findById($leadId);
        $leadComment = new LeadComment();
        $comment = $this->saveComment($auth->id, $comment, $leadComment);
        $leadComment->lead_id = $lead->id;
        $leadComment->comment_id = $comment->id;
        return $lead->leadComments()->save($leadComment);
    }

    public function saveComment(int $createdBy, string $comment, LeadComment $leadCommentModule)
    {
        $newComment = new Comment();
        $moduleId =  getModuleIdFromEntity($newComment);
        $leadCommentModuleId =  getModuleIdFromEntity($leadCommentModule);
        $newComment->comment = $comment;
        $newComment->created_by = $createdBy;
        $newComment->relation_id = $leadCommentModuleId;
        $newComment->module_id = $moduleId;
        $newComment->save();
        return $newComment;
    }

    public function updateComment(UpdateLeadCommentRequest $request)
    {
        try {
            $data = $request->all();
            $this->updateLeadCommentByLeadId($data["id"], $data["comment"]);
            return $this->created([
                'message' => LeadsController::LEAD_COMMENT_UPDATE
            ]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function updateLeadCommentByLeadId(int $commentId, string $comment)
    {
        $leadComment = LeadComment::where('id', $commentId)->first();
        if ($leadComment == null)
            throw new Exception(LeadsController::LEAD_COMMENT_NOT_FOUND);
        return  $leadComment->comment->update(['comment' => $comment]);
    }

    public function deleteComment(DeleteLeadCommentRequest $request)
    {
        try {
            $data = $request->all();
            $this->deleteCommentByCommentId($data["id"]);
            return $this->created([
                'message' => LeadsController::LEAD_COMMENT_DELETED
            ]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function deleteCommentByCommentId(int $commentId)
    {
        $leadComment = $this->findCommentById($commentId);
        $leadComment->comment->delete();
        return $leadComment->delete();
    }

    private function leadContainsMarketingDetails(array $data)
    {
        $leadMarketingDetails = new LeadMarketingDetail;
        $fields = $leadMarketingDetails->getFillable();
        return Arr::hasAny($data, $fields);
    }

    private function getShowLeadsForId()
    {
        // More logic should go in here, whether we wanna fetch leads for this user only, or user
        // has permissions to see other leads as well.
        return $this->userId();
    }
}
