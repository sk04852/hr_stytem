<?php
namespace App\Http\Services;
use App\Http\Controllers\Tickets\Models\Ticket;
use App\Events\TicketNotifyUser;
use App\Http\Controllers\Tickets\Enums\TicketPriorityEnum;
use App\Http\Controllers\Tickets\Enums\TicketStatusEnum;
use App\Http\Controllers\Tickets\Models\TicketReply;
use App\Http\Controllers\Users\Models\User;
use App\Http\Controllers\Modules\Enums\ModuleEnum;

class TicketsService {

    private $model_;
    private $userService_;
    private $watchersService_;
    private $ticketReplyModel_;
    public function __construct(Ticket $model,
                                TicketReply $ticketReplyModel,
                                UserService $userService,
                                WatchersService $watchersService) {
        $this->model_ = $model;
        $this->userService_ = $userService;
        $this->watchersService_ = $watchersService;
        $this->ticketReplyModel_ = $ticketReplyModel;
    }

    public function create() {

    }

    public function fetchTicketsAssignedToUserByUserId(int $userId, string $status = 'Pending') {
        return $this->fetchTickets(['assigned_to'=> $userId, 'status'=> $status]);
    }

    public function fetchTickets(array $condition, string $orderBy = 'id', string $sort = 'DESC') {
        return $this->model_->where($condition)->orderBy($orderBy, $sort)->get();
    }

    public function fetchPendingTickets(array $condition = []) {
        $condition = array_merge($condition, ['status'=> 'Pending']);
        return $this->fetchTickets($condition);
    }

    public function fetchTicketsByPriority(string $priority, $condition = []) {
        $condition = array_merge($condition, ['priority'=> $priority]);
        return $this->fetchTickets($condition);
    }

    public function notifyUser(Ticket $ticket, $user, array $params = []) {
        if($user == null) {
            $userId = $ticket->getAssignedTo();
            $user = $this->userService_->getUser($userId);
        }

        if(!is_null($user) && !$user instanceof User) {
            dd($user);
            $user = $this->userService_->getUser($user);
        }

        if(is_null($user))
            return;
        event(new TicketNotifyUser($ticket, $user, $params));
    }

    public function getTicketById(int $ticketId) {
        return  $this->model_->where('id', $ticketId)->first();
    }

    public function updateTicketStatus(Ticket $ticket, TicketStatusEnum $status) {
        $this->model_->update(['status'=> $status]);
        $this->notifyUser($ticket, null, ['action'=> 'statusChanged', 'status'=> $status]);
    }

    public function updateTicketPriority(Ticket $ticket, TicketPriorityEnum $priority) {
        $this->model_->update(['priority'=> $priority]);
        $this->notifyUser($ticket, null, ['action'=> 'priorityChanged', 'priority'=> $priority]);
    }

    public function addWatcher(Ticket $ticket, int $userId) {
        $this->watchersService_->create(ModuleEnum::Tickets, $ticket->id, $userId);
    }

    public function removeWatcher(Ticket $ticket, int $userId) {
        $this->watchersService_->remove(ModuleEnum::Tickets, $ticket->id, $userId);
    }

    public function getWatchers(Ticket $ticket) {
        return $this->watchersService_->getWatchers(ModuleEnum::Tickets, $ticket->id);
    }

    public function notifyWatchers(Ticket $ticket, array $params = []) {
        $watchers = $this->getWatchers($ticket);
        if($watchers->count() >= 0) {
            $watchers->each(function($watcher) use($ticket, $params) {
                $this->notifyUser($ticket, $watcher->user, $params);
            });
        }
    }

    public function createReply(Ticket $ticket, int $userId, string $note) {
        $ticketReply = $this->ticketReplyModel_->newInstance();
        $ticketReply->ticket_id = $ticket->id;
        $ticketReply->user_id = $userId;
        $ticketReply->note = $note;
        $ticket->replies()->save($ticketReply);
        $this->notifyWatchers($ticket, ['action'=> 'newReply']);
    }


}

?>
