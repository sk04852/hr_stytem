<?php

namespace App\Http\Controllers\Leads\Models;

use App\Http\Controllers\Comments\Models\Comment;
use App\Http\Controllers\Documents\Models\Document;
use App\Events\WorkflowEvent;
use App\Http\Controllers\Options\Models\FieldOption;
use App\Http\Controllers\Users\Models\User;
use App\Http\Resources\Searchables\SearchableLeadResource;
use App\Models\BaseModel;
use App\Http\Controllers\Search\Indexers\LeadsIndexConfigurator;
use ScoutElastic\Searchable;

class Lead extends BaseModel
{
    use Searchable;
    protected $indexConfigurator = LeadsIndexConfigurator::class;

    // Here you can specify a mapping for model fields
    protected $mapping = [
        'properties' => [
            'text' => [
                'type' => 'text',
            ],
        ]
    ];

    protected $table = 'leads';
    public static $snakeAttributes = false;

    protected $casts = [
        'brand_id' => 'int',
        'assigned_to' => 'int',
    ];

    protected $fillable = [
        'brand_id',
        'title',
        'first_name',
        'last_name',
        'is_opt_out',
        'call_me_in',
        'prefix',
        'primary_phone',
        'secondary_phone',
        'mobile',
        'street',
        'po_box',
        'postal_code',
        'city',
        'country',
        'is_duplicate',
        'description_details',
        'lead_source',
        'primary_email',
        'secondary_email',
        'assigned_to',
        'lead_status',
        'ip',
        'created_by',
        'lead_supplier',
        'registration_country',
        'page_url',
        'state',
        'website_language',
        'lead_id',
        'external_lead_id',
        'spoken_language',
        'promo_code',
        'text'
    ];

    protected $primaryKey = 'id';


    //workflow
    protected static function booted()
    {
        static::saved(function ($model) {
            event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'saved'));
        });

        static::created(function ($model) {
            event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'create'));
        });

        static::updated(function ($model) {
            event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'update'));
        });

        static::deleted(function ($model) {
            event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'delete'));
        });
    }

    /* Auto Start */

    public function fromView()
    {
        return $this->setTable('v_leads');
    }

    public function leadComments()
    {
        return $this->hasMany(LeadComment::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'module_id');
    }

    public function leadMarketingDetails()
    {
        return $this->hasMany(LeadMarketingDetail::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function user()
    {
        return  $this->belongsTo(User::class, 'assigned_to');
    }

    public function setLastUpdatedBy(int $userId)
    {
        $this->last_updated_by = $userId;
    }

    public function getCreatedBy()
    {
        return $this->created_by;
    }

    public function saveLeadMarketingDetails(LeadMarketingDetail $marketingDetail)
    {
        $this->leadMarketingDetails()->save($marketingDetail);
    }

    public function updateLeadMarketingDetails(LeadMarketingDetail $marketingDetail, array $updateData)
    {
        $marketingDetail->update($updateData);
        return $marketingDetail;
    }

    public function markAsDuplicate()
    {
        $this->is_duplicate = true;
    }

    /* Auto End */

    public function getBrandId()
    {
        return $this->brand_id;
    }

    public function setBrandId(int $brandId)
    {
        return $this->brand_id = $brandId;
    }

    public function getLeadId()
    {
        return $this->lead_id;
    }

    public function setLeadId(string $leadId)
    {
        $this->lead_id = $leadId;
    }

    public function setDefaultLeadStatus()
    {
        $defaultLeadStatusId = $this->fetchDefaultLeadStatusId();
        $this->lead_status = $defaultLeadStatusId;
    }

    public function fetchDefaultLeadStatusId()
    {
        return FieldOption::where('name', 'New')->where('description', 'Lead Status')->first()->id;
    }

    public function leadsFilters($request)
    {
        return $this->when(!empty($request->first_name), function ($query) use ($request) {
            return $query->where('first_name', 'like', '%' . $request->first_name . '%');
        })
            ->when(!empty($request->last_name), function ($query) use ($request) {
                return $query->where('last_name', 'like', '%' . $request->last_name . '%');
            })
            ->when(!empty($request->primary_email), function ($query) use ($request) {
                return $query->where('primary_email', 'like', '%' . $request->primary_email . '%');
            })
            ->when(!empty($request->lead_status), function ($query) use ($request) {
                return $query->where('lead_status', $request->lead_status);
            })
            ->when(!empty($request->primary_phone), function ($query) use ($request) {
                return $query->where('primary_phone', $request->primary_phone);
            })
            ->when(!empty($request->assigned_to), function ($query) use ($request) {
                return $query->where('assigned_to', $request->assigned_to);
            })
            ->when(!empty($request->created_at), function ($query) use ($request) {
                return $query->where('created_at', 'like', '%' .$request->created_at. '%');
            })
            ->when(!empty($request->updated_at), function ($query) use ($request) {
                return $query->where('updated_at', 'like', '%' .$request->updated_at. '%');
            })
            ->when(!empty($request->lead_supplier), function ($query) use ($request) {
                return $query->where('lead_supplier', 'like', '%' . $request->lead_supplier . '%');
            })
            ->when(!empty($request->city), function ($query) use ($request) {
                return $query->where('city',  'like', '%' . $request->city . '%');
            })
            ->when(!empty($request->country), function ($query) use ($request) {
                return $query->where('country',  'like', '%' . $request->country . '%');
            })
            ->when(!empty($request->alphabet), function ($query) use ($request) {
                return $query->where('last_name',  'like', $request->alphabet . '%');
            })
            ->when(!empty($request->referrer_id), function ($query) use ($request) {
                $query->whereHas('leadMarketingDetails', function ($query) use ($request) {
                    $query->where('referrer_id', $request->referrer_id);
                });
            });
    }


    public function documents()
    {
        return $this->morphMany(Document::class, 'relation');
    }

    public function leadStatus()
    {
        return  $this->belongsTo(FieldOption::class, 'lead_status');
    }

    public function toSearchableArray()
    {
        $data = new SearchableLeadResource($this);
        return $data->toArray($this);
    }

    public function RegistrationCountry()
    {
        return  $this->belongsTo(FieldOption::class, 'registration_country');
    }

    public function searchableAs()
    {
        return '_doc';
    }
}
