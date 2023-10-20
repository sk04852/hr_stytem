<?php

namespace App\Http\Controllers\Documents\Models;

use App\Http\Controllers\Leads\Models\Lead;
use App\Http\Controllers\Users\Models\User;
use App\Models\BaseModel;
use App\Events\WorkflowEvent;
use App\Http\Controllers\Companies\Models\Company;
use App\Http\Controllers\EmsEmployee\Models\Employee;

class Document extends BaseModel
{
	protected $table = 'documents';
	public static $snakeAttributes = false;

	protected $fillable = [
        'company_id',
        'relation_type',
        'relation_id',
        'type',
        'assigned_to',
        'description',
        'document_no',
        'status',
        'description',
        'expiry_date',
        'issue_date'
    ];

    //workflow
    protected static function booted()
    {
        static::saved(function($model){
            event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'saved'));
        });

        static::created(function($model){
            event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'create'));
        });

        static::updated(function($model){
            event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'update'));
        });

        static::deleted(function($model){
            event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'delete'));
        });
    }

    public function scopeDocumentFilters($query, array $filters)
    {
        if (array_key_exists('module', $filters)) {
            $query->where('module',$filters['module']);
        }
        if (array_key_exists('relation_id', $filters)) {
            $query->where('relation_id', '=', $filters['relation_id']);
        }
        if (array_key_exists('company_id', $filters)) {
            $query->where('company_id', $filters['company_id']);
        }
        if (array_key_exists('assigned_to', $filters)) {
            $query->where('assigned_to',  $filters['assigned_to']);
        }
        if (array_key_exists('document_no', $filters)) {
            $query->where('document_no', $filters['document_no']);
        }
        if (array_key_exists('status', $filters)) {
            $query->where('status', $filters['status']);
        }
        if (array_key_exists('issue_date', $filters)) {
            $query->where('issue_date', $filters['issue_date']);
        }
        if (array_key_exists('created_by', $filters)) {
            $query->where('created_by',  $filters['created_by']);
        }
    }

    public function assignedTo()
	{
		return $this->hasOne(User::class, 'id', 'assigned_to');
    }

    public function scopeWithUser($query) {
        return $query->with(['assignedTo' => function($query){ $query->select(['id', 'first_name', 'last_name', 'email']);}]);
    }

    public function lead()
	{
		return $this->hasOne(Lead::class, 'id', 'relation_id');
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function employee() {
        if($this->relation_type === 'Employee' && $this->relation_id != NULL) {
            return $this->belongsTo(Employee::class, 'relation_id');
        }
    }
}
