<?php
    use Illuminate\Support\Facades\Route;

    Route::apiResource('notifications', 'NotificationsController');
    Route::post('notifications/mass_delete', 'NotificationsController@doMassDelete');
    Route::get('get-notification/{user_id}', 'NotificationsController@getMyNotification');
    Route::get('user_notifications/{type?}', 'NotificationsController@getMyNotifications');
    Route::post('user_notifications/mark/read', 'NotificationsController@markAsRead');
    Route::post('user_notifications/mark/unread', 'NotificationsController@markAsUnread');
    Route::post('user_notifications/delete', 'NotificationsController@deleteUserNotifications');
    Route::get('notifications_for_delivery', 'NotificationsController@getNotificationBatchForDelivery');
?>
