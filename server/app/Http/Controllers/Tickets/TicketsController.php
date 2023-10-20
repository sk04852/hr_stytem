<?php

namespace App\Http\Controllers\Tickets;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response as ResponseCode;
use App\Http\Controllers\Tickets\Models\Ticket;
use App\Http\Controllers\Tickets\Requests\TicketReplyRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Tickets\Requests\CreateTicketRequest as CreateRequest;
use App\Http\Controllers\Tickets\Requests\UpdateTicketRequest as UpdateRequest;
use App\Http\Controllers\Tickets\Requests\TicketMassUpdateRequest;
use App\Http\Controllers\Tickets\Requests\TicketMassDeleteRequest;
use App\Http\Controllers\Tickets\Requests\TicketMassUpdateStatusRequest;
use App\Http\Controllers\Tickets\Requests\TicketMassUpdatePriorityRequest;
use App\Http\Services\MediaService;
use App\Http\Services\TicketsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Modules\Enums\FieldOptionTypeEnum;
use App\Http\Controllers\Modules\Enums\LogAction;
use App\Http\Controllers\Modules\Enums\LogTypeEnum;
use App\Http\Controllers\Modules\Enums\ModuleEnum;

class TicketsController extends Controller
{

    const MODULE_NAME = 'ticket';
    const COLLECTION_NAME = 'tickets';

    private $model_;
    private $mediaService_;
    private $ticketsService_;

    public function __construct(
        Ticket $model,
        MediaService $mediaService,
        TicketsService $ticketsService
    ) {
        parent::__construct($model);
        $this->model_ = $model;
        $this->mediaService_ = $mediaService;
        $this->ticketsService_ = $ticketsService;
    }

    public function index(Request $request)
    {
        try {
            if ($request->has('status')) {
                $statusesFieldOptions = getFieldOptionsByType(FieldOptionTypeEnum::TicketStatuses);
                $selectedStatusFieldOption = pickFieldOptionFromList($statusesFieldOptions, $request->status);
                $request->merge([
                    'status' => $selectedStatusFieldOption->id,
                ]);
            }
            if ($request->has('priority')) {
                $prioritiesFieldOptions = getFieldOptionsByType(FieldOptionTypeEnum::TicketPriorities);
                $selectedPriorityFieldOption = pickFieldOptionFromList($prioritiesFieldOptions, $request->priority);
                $request->merge([
                    'priority' => $selectedPriorityFieldOption->id,
                ]);
            }

            $tickets = $this->model_->filter($request)
                ->where(array('created_by' => $this->userId()))
                ->withAssignedTo()
                ->withCreatedBy()
                ->with('priority', 'language', 'severity', 'status', 'category')->paginate(50);
            if ($tickets) {
                return response()->json(['tickets' => $tickets], ResponseCode::HTTP_OK);
            } else {
                return response()->json(["error" => "No tickets founds."], ResponseCode::HTTP_OK);
            }
        } catch (Exception $ex) {
            return response()->json(["error" => "Server error: " . $ex->getMessage()], ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function infoTicket(int $id)
    {
        try {
            $ticket = Ticket::where('parent_id', $id)->get();
            if ($ticket) {
                return $this->created(['ticket' => $ticket], ResponseCode::HTTP_OK);
            } else {
                return $this->failed(["error" => "No ticket records founds."], ResponseCode::HTTP_OK);
            }
        } catch (Exception $ex) {
            return response()->json(["error" => "Server error: " . $ex->getMessage()], ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $data = $request->all();
            $data['created_by'] = $this->userId();
            $this->model_ = $this->model_->fill($data);
            if ($this->model_->save()) {
                $this->addLog(LogTypeEnum::Info, null, $this->model_, $this->model_, LogAction::Created, ModuleEnum::Tickets);
                if (isset($data['media']) && count($data['media']) > 0) {
                    $this->mediaService_->attachMedia($this->model_, $data['media']);
                }
                $this->ticketsService_->addWatcher($this->model_, $data['created_by']);
                $this->ticketsService_->notifyWatchers($this->model_, ['action' => 'newTicket']);
                return $this->created(["message" => "Ticket successfully created.", 'ticket' => $this->model_], ResponseCode::HTTP_OK);
            } else {
                return $this->faield(["error" => "Ticket create failed."], ResponseCode::HTTP_FORBIDDEN);
            }
        } catch (Exception $ex) {
            return response()->json(["error" => "Server error: " . $ex->getMessage()], ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->model_->where('id', $id)
                ->withAssignedTo()
                ->withCreatedBy()
                ->with('priority', 'language', 'severity', 'status', 'category')
                ->first();
            if (!$record)
                return $this->failed(["message" => "Ticket not found", ResponseCode::HTTP_NOT_FOUND]);
            return $this->created([TicketsController::MODULE_NAME => $record]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return $this->noRecord(["message" => TicketsController::MODULE_NAME . ' not found, invalid id']);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $id = $request->get('id');
            $record = $this->findOneById($id);
            $record->fill($request->all());
            $oldData = $record->getOriginal();
            if ($record->save()) {
                $this->addLog(LogTypeEnum::Info, null, $oldData, $record, LogAction::Updated, ModuleEnum::Tickets);
                return $this->created([TicketsController::MODULE_NAME => $record, 'message' => TicketsController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function replyTicket(TicketReplyRequest $request)
    {
        try {
            $data = $request->all();
            $data['client_id'] = $this->userId();
            $this->model_ = $this->model_->fill($data);
            if ($this->model_->save()) {
                $this->ticket = Ticket::find($data['parent_id']);
                $this->ticket['replied'] = true;
                $this->ticket->update();
                return response()->json(["message" => "Ticket Reply successfully created.", 'reply' => $this->model_], ResponseCode::HTTP_OK);
            } else {
                return response()->json(["error" => "Ticket create failed."], ResponseCode::HTTP_FORBIDDEN);
            }
        } catch (Exception $ex) {
            return response()->json(["error" => "Server error: " . $ex->getMessage()], ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id)
    {
        try {
            $record = $this->model_->where('id', $id)->first();
            if ($record) {
                if ($record->delete()) {
                    $this->addLog(LogTypeEnum::Info, null, $record, null, LogAction::Deleted, ModuleEnum::Tickets);
                    return $this->created(['message' => TicketsController::RECORD_DELETED]);
                }
            } else {
                return $this->noRecord(['message' => TicketsController::NO_RECORD]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function massUpdate(TicketMassUpdateRequest $request)
    {
        try {
            $data = $request->all();
            $ticketIds = $data['id'];
            $updateData = $request->except(['id']);
            $this->model_->whereIn('id', $ticketIds)->update($updateData);
            return $this->created(['message' => TicketsController::RECORD_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function massDelete(TicketMassDeleteRequest $request)
    {
        try {
            $data = $request->all();
            $ids = $data['id'];
            $this->model->whereIn('id', $ids)->delete();
            return $this->created(['message' => TicketsController::MASS_RECORDS_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function massUpdateStatus(TicketMassUpdateStatusRequest $request)
    {
        try {
            $data = $request->all();
            $ticketIds = $data['id'];
            $status = $request->status;
            $statusesFieldOptions = getFieldOptionsByType(FieldOptionTypeEnum::TicketStatuses);
            $selectedStatusFieldOption = pickFieldOptionFromList($statusesFieldOptions, $status);
            if (is_null($selectedStatusFieldOption))
                throw new Exception("Invalid Status");
            $this->massChangeStatus($ticketIds, $selectedStatusFieldOption->id);
            return $this->created(['message' => TicketsController::RECORD_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function massUpdatePriority(TicketMassUpdatePriorityRequest $request)
    {
        try {
            $data = $request->all();
            $ticketIds = $data['id'];
            $priority = $request->priority;
            $prioritiesFieldOptions = getFieldOptionsByType(FieldOptionTypeEnum::TicketPriorities);
            $selectedPriorityFieldOption = pickFieldOptionFromList($prioritiesFieldOptions, $priority);
            if (is_null($selectedPriorityFieldOption))
                throw new Exception("Invalid Status");
            $this->massChangePriority($ticketIds, $selectedPriorityFieldOption->id);
            return $this->created(['message' => TicketsController::RECORD_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function massChangeStatus(array $ids, $status)
    {
        $this->model->whereIn('id', $ids)->update(['status' => $status]);
    }

    public function massChangePriority(array $ids, $priority)
    {
        $this->model->whereIn('id', $ids)->update(['priority' => $priority]);
    }
}
