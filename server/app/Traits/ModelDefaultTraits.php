<?php
namespace App\Traits;
use DateTime;

trait ModelDefaultTraits
{
    public function scopeCreatedBetween($query, DateTime $startDateTime, DateTime $endDateTime) {
        $query->where([
            ['created_at', '>=', $startDateTime],
            ['created_at', '<=', $endDateTime]
        ]);
    }

    public function scopeUpdatedBetween($query, DateTime $startDateTime, DateTime $endDateTime) {
        $query->where([
            ['updated_at', '>=', $startDateTime],
            ['updated_at', '<=', $endDateTime]
        ]);
    }

    public function scopeDeletedBetween($query, DateTime $startDateTime, DateTime $endDateTime) {
        $query->where([
            ['deleted_at', '>=', $startDateTime],
            ['deleted_at', '<=', $endDateTime]
        ]);
    }

    public function scopeCreatedBy($query, int $userId) {
        return $query->whereCreatedBy($userId);
    }

    public function scopeSimpleSearch($query, array $columns, string $keyword){
        foreach($columns as $column){
            $query = $query->orWhere($column, 'like', '%'.$keyword.'%');
        }
        return $query;
    }

    public function scopeFilter($query, array $filters) {
        if(array_key_exists('module', $filters)) {
            $query->where('module', $filters['module']);
        }

        if(array_key_exists('relation_id', $filters)) {
            $query->where('relation_id', $filters['relation_id']);
        }

        if(array_key_exists('created_by', $filters)) {
            $query->where('created_by', $filters['created_by']);
        }

        if(array_key_exists('is_reply', $filters)) {
            $query->where('is_reply', $filters['is_reply']);
        }

        if(array_key_exists('reply_to', $filters)) {
            $query->where('reply_to', $filters['reply_to']);
        }

        if(array_key_exists('start_date', $filters)) {
            $query->where('created_at','>=', $filters['start_date']);
        }

        if(array_key_exists('end_date', $filters)) {
            $query->where('created_at','<=',$filters['end_date']);
        }

        if(array_key_exists('status', $filters)) {
            $query->where('status',$filters['status']);
        }

        if(array_key_exists('email', $filters)) {
            $query->where('email', 'like', '%'.$filters['email'].'%');
        }

        if(array_key_exists('company_id', $filters)) {
            $query->where('company_id',$filters['company_id']);
        }
    }

    public function setCreatedBy(int $userId) {
        if(in_array('created_by', $this->getFillable())) {
            $this->created_by = $userId;
        }
    }
    
    public function getCreatedBy() {
        return $this->created_by;
    }
    
    public function setAssignedTo(int $userId) {
        if(in_array('assigned_to', $this->getFillable())) {
            $this->assigned_to = $userId;
        }
    }

    public function getAssignedTo() {
        return $this->assigned_to;
    }

    public function user() {
        // This function should be overwritten by the class that inherits it.
    }
    
    public function scopeWithUser($query) {
        return $query->with(['user' => function($query){ $query->select(['id', 'first_name', 'last_name', 'email']);}]);
    }

    public function scopeForThisCompany($query, int $companyId) {
        return $query->where('company_id', NULL)->orWhere('company_id', $companyId);
    }

    public function scopeToDigest($query, array $cols = null, $orderByCol = null, $order = 'ASC') {
        $colsToSelect = ($cols == null)? ['id', 'name']: $cols;
        $orderByCol = ($orderByCol == null)? 'name': $orderByCol;
        return $query->select($colsToSelect)->orderBy($orderByCol, $order)->get();
    }
}