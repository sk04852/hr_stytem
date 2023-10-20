<?php

namespace App\Http\Controllers\Groups;

use Exception;
use App\Http\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Groups\Models\Group;
use App\Http\Controllers\Groups\Requests\CreateGroupRequest as CreateRequest;
use App\Http\Controllers\Groups\Requests\UpdateGroupRequest as UpdateRequest;
use Illuminate\Http\Request;

class GroupsController extends Controller
{
    protected $models = 'Group';
    const GROUP_NOT_FOUND = 'Group not found';
    const GROUP_CREATED = 'Group created successfully';
    const GROUP_DELETED = 'Group has been deleted';
    const GROUP_UPDATED = 'Group has been updated';
    const COLLECTION_NAME = 'groups';
    private $log_ = true;
    
    private $shouldBelongToCompany_ = true;

    public function __construct(Group $model, UserService $userService)
    {
        parent::__construct($model);
        $this->userService_ = $userService;
    }

    public function index(Request $request)
    {
        try{
            $data = $this->model->forThisCompany($this->companyId())
            ->when(!empty($request->name), function ($query) use ($request) {
                return $query->where('group_name', $request->name);
            })->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
                if ( $data->isNotEmpty() ) {    
                    return $this->created([GroupsController::COLLECTION_NAME => $data]);
                }
                return $this->created([GroupsController::COLLECTION_NAME => []]);
        }catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function activeGroup($request){
        if($request->is_default == 'yes'){
             Group::where('is_default','=','yes')->update(['is_default' => null]);
        } 
    }

    public function store(CreateRequest $request)
    {
        try{       
            $this->activeGroup($request);
            $groupData = $request->all();

            if($this->shouldBelongToCompany_) {
                $groupData['company_id'] = $this->companyId();
            }

            $result = Group::create($groupData);
            if ($result) {
                if($this->logActions($this->log_) == true)
                $this->addLog('info', $this->created, $result,$this->models, $this->type(), $this->userId());
                return $this->created(['message'=> GroupsController::GROUP_CREATED]);
            }
        }catch (Exception $ex) {
            $this->addLog('Error',$this->created,$ex->getMessage(),$this->models,'System','0');
            return $this->serverError($ex);
        } 
    }

    public function update(UpdateRequest $request)
    {
        try{
            $this->activeGroup($request);
            $group = Group::find($request->id);  
            $result = $group->update($request->all());
            if ($result) {
                if($this->logActions($this->log_) == true)
                $this->addLog('info',$this->updated,$group,$this->models,$this->type(),$this->userId());
                return $this->created(['message' => GroupsController::GROUP_UPDATED]);
            }
        }catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            $this->addLog('Error',$this->updated,$ex->getMessage(),$this->models,'System','0');
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        try{
            $group = Group::find($id);   
            if ( $group ) { 
                $group->delete();
                if($this->logActions($this->log_) == true)
                $this->addLog('info',$this->deleted,$group,$this->models,$this->type(),$this->userId());
                return $this->created(['message' => GroupsController::GROUP_DELETED]);
            } 
                return $this->noRecord(['message'=>GroupsController::GROUP_NOT_FOUND]);
        }catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            $this->addLog('Error',$this->deleted,$ex->getMessage(),$this->models,'System','0');
            return $this->serverError($ex);
        }
    }
}
