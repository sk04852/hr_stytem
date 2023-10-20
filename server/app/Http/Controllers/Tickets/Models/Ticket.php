<?php

namespace App\Http\Controllers\Tickets\Models;

// use App\Models\User;
use App\Models\Account;
use App\Models\BaseModel;
use App\Http\Controllers\Options\Models\FieldOption;
use App\Http\Controllers\Users\Models\User;

use function PHPSTORM_META\map;

class Ticket extends BaseModel
{
    protected $table = 'tickets';
    public static $snakeAttributes = false;
    public $timestamps = true;

    protected $casts = [
        'assigned_to' => 'int',
        'account_id' => 'int',
        'lead_id' => 'int',
        'brand_id' => 'int'
    ];

    protected $dates = [
        'last_comment_at'
    ];

    protected $fillable = [
        'title',
        'assigned_to',
        'complain_id',
        'created_by',
        'type',
        'priority',
        'status',
        'severity',
        'language',
        'last_comment_at',
        'last_commented_by',
        'notification_email',
        'description',
        'resolution',
        'category',
        'hours',
        'days',
        'amount',
        'currency',
        'credit_card_last_4_digits',
        'account_number',
        'widthrawal_type',
        'bank_details',
        'beneficiary_name',
        'beneficiary_address',
        'transfer_to',
        'requested_leverage',
        'error_description',
        'error_code',
        'unique_id',
        'transaction_id',
        'gateway_instance',
        'gateway_name',
        'last_commented_by',
        'notification_email',
        'organizations',
        'product_name',
        'contact_name'
    ];

    public $primaryKey  = 'id';

    //workflow
    protected static function booted()
    {
        // static::saved(function($model){
        //     event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'saved'));
        // });

        // static::created(function($model){
        //     event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'create'));
        // });

        // static::updated(function($model){
        //     event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'update'));
        // });

        // static::deleted(function($model){
        //     event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'delete'));
        // });
    }

    public function filter($request)
    {
        return $this->when(!empty($request->type), function ($query) use ($request) {
            return  $query->where('type', $request->type);
        })
            ->when(!empty($request->priority), function ($query) use ($request) {
                return  $query->where('priority', $request->priority);
            })
            ->when(!empty($request->language), function ($query) use ($request) {
                return  $query->where('language', $request->language);
            })
            ->when(!empty($request->severity), function ($query) use ($request) {
                return  $query->where('severity', $request->severity);
            })
            ->when(!empty($request->status), function ($query) use ($request) {
                return  $query->where('status', $request->status);
            })
            ->when(!empty($request->assigned_to), function ($query) use ($request) {
                return  $query->where('assigned_to', $request->assigned_to);
            })
            ->when(!empty($request->created_by), function ($query) use ($request) {
                return  $query->where('created_by', $request->created_by);
            })
            ->when(!empty($request->client_id), function ($query) use ($request) {
                return  $query->where('client_id', $request->client_id);
            })
            ->when(!empty($request->business_id), function ($query) use ($request) {
                return  $query->where('business_id', $request->business_id);
            })
            ->when(!empty($request->outlet_id), function ($query) use ($request) {
                return  $query->where('outlet_id', $request->outlet_id);
            })
            ->when(!empty($request->status), function ($query) use ($request) {
                return  $query->where('status', $request->status);
            })
            ->when(!empty($request->from), function ($query) use ($request) {
                return  $query->whereDate('created_at', '>=', $request->from);
            })
            ->when(!empty($request->to), function ($query) use ($request) {
                return  $query->whereDate('created_at', '<=', $request->to);
            })
            ->when(!empty($request->title), function ($query) use ($request) {
                return $query->where('title', 'like', '%' . $request->title . '%');
            });
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function assigned_to()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function ticketFailedDeposits()
    {
        return $this->hasMany(TicketFailedDeposit::class);
    }

    public function ticketInternalTransfaers()
    {
        return $this->hasMany(TicketInternalTransfaer::class);
    }

    public function ticketLeverageRequests()
    {
        return $this->hasMany(TicketLeverageRequest::class);
    }

    public function ticketWithdrawalRequests()
    {
        return $this->hasMany(TicketWithdrawalRequest::class);
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    public function priority()
    {
        return $this->belongsTo(FieldOption::class, 'priority');
    }

    public function language()
    {
        return $this->belongsTo(FieldOption::class, 'language');
    }

    public function severity()
    {
        return $this->belongsTo(FieldOption::class, 'severity');
    }

    public function status()
    {
        return $this->belongsTo(FieldOption::class, 'status');
    }

    public function category()
    {
        return $this->belongsTo(FieldOption::class, 'category');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by')->select('first_name');
    }

    public function scopeWithAssignedTo($query)
    {
        return $query->with(['assigned_to' => function ($query) {
            $query->select(['id', 'first_name', 'last_name', 'email']);
        }]);
    }

    public function scopeWithCreatedBy($query)
    {
        return $query->with(['created_by' => function ($query) {
            $query->select(['id', 'first_name', 'last_name', 'email']);
        }]);
    }
}
