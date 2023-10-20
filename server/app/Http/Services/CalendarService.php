<?php
namespace App\Http\Services;

use App\Http\Controllers\Calendar\Models\Calendar;

class CalendarService {
    private $model_;
    
    public function __construct(Calendar $model) {
        $this->model_ = $model;
    }

    public function getUserDefaultCalendarById(int $userId) {
        $userDefaultCalendar = $this->model_->where('created_by', $userId)->where('is_default', 'Yes')->first();
        if(is_null($userDefaultCalendar)) {
            return $this->createUserDefaultCalendar($userId);
        }

        return $userDefaultCalendar;
    }

    public function createUserDefaultCalendar(int $userId) {
        $defaultCalendar = [
            'title'=> 'My Personal Calendar',
            'visibility'=> 'Private',
            'source'=> 'Default',
            'is_default'=> 'Yes',
            'created_by'=> $userId,
        ];
        return $this->model_->create($defaultCalendar);
    }

}

?>