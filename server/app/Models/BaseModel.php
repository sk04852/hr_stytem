<?php
namespace App\Models;

use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Traits\ModelDefaultTraits;
use App\Events\CacheInvalidatorEvent;
use App\Http\Controllers\Companies\Models\Company;
use App\Http\Controllers\Outlets\Models\Outlet;
use App\Http\Controllers\Users\Models\User;
use App\Traits\GetOnlyFillablesTrait;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use ModelDefaultTraits;
    use GetOnlyFillablesTrait;

    private $ignoreAbles = ['company_id', 'outlet_id'];
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

    public static function modelCacheKey(int $id) {
        return sprintf(
                        "%s/%s", (new static)->getTable(),
                        ($id !=null)? $id: 0,
        );
    }

    public static function invalidatesCaches($model) {
        event(new CacheInvalidatorEvent($model));
    }

    public function scopeForCompany($query, int $companyId) {
        if($companyId > 0) {
            $query->where('company_id', $companyId);
        }
        return $query;
    }

    public function company() {
        $this->belongsTo(Company::class);
    }

    public function outlet() {
        $this->belongsTo(Outlet::class);
    }

    public function createdBy() {
        $this->belongsTo(User::class);
    }

    public function massUpdate(array $values, string $columnName, $caseKey = 'id', $isStringId = false)
    {
        $table = $this->getTable();
        $cases = []; $ids = [];  $params = [];
        foreach ($values as $id=> $value) {
            $id = ($isStringId)? $id: (int) $id;
            if($isStringId) {
                $cases[] = "WHEN '{$id}' then ?";
                $ids[] = "'".$id."'";
            } else {
                $cases[] = "WHEN {$id} then ?";
                $ids[] = $id;
            }
            $params[] = $value;
        }
        $ids = implode(',', $ids);
        $cases = implode(' ', $cases);
        $params[] = Carbon::now();
        return DB::update("UPDATE `{$table}` SET `{$columnName}` = CASE `$caseKey` {$cases} END, `updated_at` = ? WHERE `$caseKey` in ({$ids})", $params);
    }
}

