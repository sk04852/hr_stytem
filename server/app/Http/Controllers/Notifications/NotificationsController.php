<?php

namespace App\Http\Controllers\Notifications;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Notifications\Requests\NotificationRequest;
use App\Http\Controllers\Notifications\Models\Notification;
use App\Http\Controllers\Notifications\Models\NotificationReceiver;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Notifications\Models\Notification as ThisModel;
use App\Http\Controllers\Notifications\Requests\CreateNotificationRequest;
use App\Http\Enums\NotificationReadStatus;
use App\Http\Enums\NotificationTypeEnum;
use App\Http\Services\NotificationsService;

class NotificationsController extends Controller
{
    const MODULE_NAME = 'notification';
    const COLLECTION_NAME = 'notifications';
    const NOTIFICATIONS_DELETED = 'Notifications has been deleted successfuly';
    const NOTIFICATION_READ = 'Notifications marked As read';
    const UNAUTHORIZED = 'You are not authorized';
    private $notificationsService_;

    public function __construct(ThisModel $model,
                                NotificationsService $notificationsService)
    {
        parent::__construct($model);
        $this->notificationsService_ = $notificationsService;
    }

    public function index(Request $request)
    {
        try {
            $records = $this->model->notificationFilters($request)
                                    ->orderBy($this->getSortBy(), $this->getSort())
                                    ->paginate($this->getPerPage());
            if ($records->isNotEmpty()) {
                return $this->created([NotificationsController::COLLECTION_NAME => $records]);
            }
            return $this->noRecord(['message' => NotificationsController::RECORD_NOT_FOUND, NotificationsController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function getMyNotifications(string $type = "unread") {
        try {
            $status = NotificationReadStatus::All;
            if($type == 'unread') {
                $status = NotificationReadStatus::Unread;
            } else if($type == 'read') {
                $status = NotificationReadStatus::Read;
            }
            $notifications = $this->notificationsService_->getUserNotifications($this->userId(), $status);
            return $this->created([NotificationsController::MODULE_NAME=> $notifications]);
        } catch(Exception $ex) {
            $this->serverError($ex);
        }
    }

    public function markAsRead(Request $request) {
        try {
            if(!$request->has('notification_id'))
                throw new Exception("Notification id(s) not provided");

            $notificationIds = $request->get('notification_id');
            $notificationIds = (!is_array($notificationIds))? [$notificationIds]: $notificationIds;
            $markedAsRead = $this->notificationsService_->markAsRead($this->userId(), $notificationIds);
            if($markedAsRead) {
                return $this->created(["message"=> "Done"]);
            } else {
                return $this->failed(["message"=> "Not marked"]);
            }
        } catch(Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function markAsUnread(Request $request) {
        try {
            if(!$request->has('notification_id'))
                throw new Exception("Notification id(s) not provided");

            $notificationIds = $request->get('notification_id');
            $notificationIds = (!is_array($notificationIds))? [$notificationIds]: $notificationIds;
            $markedAsRead = $this->notificationsService_->markAsUnread($this->userId(), $notificationIds);
            if($markedAsRead) {
                return $this->created(["message"=> "Done"]);
            } else {
                return $this->failed(["message"=> "Not marked"]);
            }
        } catch(Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function deleteUserNotifications(Request $request) {
        try {
            if(!$request->has('notification_id'))
                throw new Exception("Notification id(s) not provided");

            $notificationIds = $request->get('notification_id');
            $notificationIds = (!is_array($notificationIds))? [$notificationIds]: $notificationIds;
            $deleted = $this->notificationsService_->deleteUserNotifications($this->userId(), $notificationIds);
            if($deleted) {
                return $this->created(["message"=> "Deleted"]);
            } else {
                return $this->failed(["message"=> "Not deleted"]);
            }
        } catch(Exception $ex) {
            return $this->serverError($ex);
        }
    }

    //Perform Mass Delete
    public function doMassDelete(Notification $notifications, NotificationRequest $request)
    {
        //Getting just notification_id Column
        $allNotificationIds = NotificationReceiver::pluck('notification_id');
        //Get Only those Entries Which are Repeated
        $RepeatedNotificationIds = array_unique(array_diff_assoc($allNotificationIds->toArray(), array_unique($allNotificationIds->toArray())));
        //IT WILL RETURN THE ITEMS THAT ARE present in the first array that are not present in second array
        $checkReceivers = array_diff($request->input('notification_id'), $RepeatedNotificationIds);
        //CHECK If Receiver is USER
        if ($request->input('receiver_type') == 'User') {
            $records = $notifications->userNotifications()
              ->when($request->input('receiver_id'), function ($query) use ($request) {
                  return $query->whereIn('receiver_id', $request->input('receiver_id'));
            });
            if ($records->exists()) {
                //Check if Notification Has Other Receivers
                if (empty($checkReceivers)) {
                    $records->whereIn('notification_id', $request->input('notification_id'))->delete();
                    return $this->created(['message' => NotificationsController::NOTIFICATIONS_DELETED]);
                } else {
                    Notification::whereIn('id', $checkReceivers)->delete();
                    $records->whereIn('notification_id', $request->input('notification_id'))->delete();
                    return $this->created(['message' => NotificationsController::NOTIFICATIONS_DELETED]);
                }
            } else {
                return $this->noRecord(['message' => NotificationsController::RECORD_NOT_FOUND]);
            }
            //CHECK IF RECEIVER IS CLIENT
        } elseif ($request->input('receiver_type') == 'Client') {
            $records = $notifications->clientNotifications()
            ->when($request->input('receiver_id'), function ($query) use ($request) {
                return $query->whereIn('receiver_id', $request->input('receiver_id'));
            });

            if ($records->exists()) {
                //Check if Notification Has Other Receivers
                if (empty($checkReceivers)) {
                    $records->whereIn('notification_id', $request->input('notification_id'))->delete();
                    return $this->created(['message' => NotificationsController::NOTIFICATIONS_DELETED]);
                } else {
                    Notification::whereIn('id', $checkReceivers)->delete();
                    $records->whereIn('notification_id', $request->input('notification_id'))->delete();
                    return $this->created(['message' => NotificationsController::NOTIFICATIONS_DELETED]);
                }
            } else {
                return $this->noRecord(['message' => NotificationsController::RECORD_NOT_FOUND]);
            }
        }
    }

    public function store(CreateNotificationRequest $request) {
        try {
            $this->notificationsService_->createUserNotification(
                                                                $request->type,
                                                                $request->body,
                                                                $request->payload,
                                                                $request->receiver_id,
                                                            );
            return $this->created(['message' => NotificationsController::RECORD_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function getMyNotification (int $userId) {
        try{
            return $this->notificationsService_->getMyNotification($userId, );
        }catch(Exception $ex) {
            $this->serverError($ex);
        }
    }

    public function getNotificationBatchForDelivery() {
        try {
            $notifications = $this->notificationsService_->getNotificationBatchForDelivery(10);
            return $this->created([NotificationsController::MODULE_NAME=> $notifications]);
        } catch(Exception $ex) {
            $this->serverError($ex);
        }
    }
}
