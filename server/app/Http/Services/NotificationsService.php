<?php
namespace App\Http\Services;

use App\Http\Controllers\Notifications\Models\Notification;
use App\Http\Controllers\Notifications\Models\NotificationReceiver;
use App\Http\Enums\NotificationDeliveryStatus;
use App\Http\Enums\NotificationReadStatus;
use Exception;
use Illuminate\Support\Facades\DB;

class NotificationsService {
    const NOTIFICATION_CREATED = 'Notification has been created successfully';
    const NOTIFICATION_NOT_CREATED = 'Notification was not created successfult';
    const NOTIFICATION_FAILED = 'Failed to send notification';
    const NOTIFICATION_NOT_FOUND = 'You do not have any notifications';
    private $notificationModel_;
    private $notificationReceiverModel_;

    public function __construct(
                                Notification $notificationModel,
                                NotificationReceiver $notificationReceiverModel)
    {
        $this->notificationModel_ = $notificationModel;
        $this->notificationReceiverModel_ = $notificationReceiverModel;
    }


    public function createUserNotification($type, $body, $payload, $receiverId) {
        $notification = $this->notificationModel_->create([
                                                            'body' => $body,
                                                            'type' => $type,
                                                            'delivered'=> false,
                                                            'payload' => $payload ? json_encode($payload) : null
                        ]);
        $notification->receivers()->sync([$receiverId]);
        return $notification;
    }

    public function createMassNotification($type, $body, $payload= [], $receivers = []) {
        $notification = $this->notificationModel_->create([
            'body' => $body,
            'type' => $type,
            'delivered'=> false,
            'payload' => $payload ? json_encode($payload) : null
        ]);
        $notification->receivers()->sync($receivers);
    }

    public function markAsRead(int $receiverId, array $notificationIds) {
        $notificationReceiver = $this->notificationReceiverModel_->where('receiver_id', $receiverId)->whereIn('notification_id', $notificationIds);
        return $notificationReceiver->update(['read_at'=> now()]);
    }

    public function markAsUnread(int $receiverId, array $notificationIds) {
        $notificationReceiver = $this->notificationReceiverModel_->where('receiver_id', $receiverId)->whereIn('notification_id', $notificationIds);
        return $notificationReceiver->update(['read_at'=> null]);
    }

    public function getMyNotification(int $userId) {
        $notifications = $this->notificationModel_->where('receiver_id', $userId)->get();
        return $notifications;
    }

    public function getUserNotifications(int $userId, int $status) {
        $query = $this->notificationReceiverModel_->where('receiver_id', $userId);
        if(NotificationReadStatus::Read == $status) {
            $query = $query->where('read_at', '!=', NULL);
        } else if(NotificationReadStatus::Unread == $status) {
            $query = $query->where('read_at', NULL);
        }
        $result = $query->with('notification')->get();
        return $result->map(function($entity){ return $entity->notification; });
    }

    public function deleteUserNotifications($receiverId, array $notificationIds) {
        $notificationReceiver = $this->notificationReceiverModel_->where('receiver_id', $receiverId)->whereIn('notification_id', $notificationIds);
        $deleted = $notificationReceiver->delete();
        if($deleted) {
            // If a notification does not have any receiver that notification should also be deleted.
            $notficationsHavingReceivers = $this->notificationReceiverModel_->whereIn('notification_id', $notificationIds)->select([DB::raw('count(receiver_id) as total_receivers'), 'notification_id'])->groupBy('notification_id')->get()->map(function($notificationReceiver){
                return $notificationReceiver->notification_id;
            });
            $toBeDeleted = array_diff($notificationIds, $notficationsHavingReceivers->toArray());
            if(count($toBeDeleted) > 0) {
                $this->notificationModel_->whereIn('id', $toBeDeleted)->delete();
            }
        }
        return $deleted;
    }

    public function getNotificationBatchForDelivery(int $howMany) {
        return $this->notificationModel_->where('delivery_status', 'Pending')->with('receivers')->orderBy('created_at', 'ASC')->limit($howMany)->get();
    }
}

?>
