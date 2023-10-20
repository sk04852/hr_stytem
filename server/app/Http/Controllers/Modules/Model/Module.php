<?php

namespace App\Http\Controllers\Modules\Model;

use App\Http\Controllers\Acl\Models\Permission;
use App\Http\Controllers\Transactions\Models\Transaction;
use App\Models\BaseModel;
use Exception;

class Module extends BaseModel
{
    // Transactions Relation
	protected $table = 'modules';
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'transaction_module_id');
    }

    public function permissions() {
        return $this->hasMany(Permission::class);
    }



}
