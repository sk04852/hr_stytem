<?php

namespace App\Http\Controllers\Acl;

use App\Http\Controllers\Controller;
use App\Http\Services\RBACLService;
use Exception;
use Illuminate\Support\Facades\Log;

class RBACLController extends Controller
{
    const MODULES_COLLECTION_NAME = 'modules';
    private $rBACLService_;
    public function __construct(RBACLService $rBACLService)
    {
        $this->rBACLService_ = $rBACLService;
    }

    public function getSystemPermissions() {
        try{
            $permissions = $this->rBACLService_->getSystemPermissions();
            return $this->created([RBACLController::MODULES_COLLECTION_NAME => $permissions]);
        }catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }
}
