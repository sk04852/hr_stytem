<?php
namespace App\Http\Controllers\Areas;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Areas\Requests\CreateAreaRequest as CreateRequest;
use App\Http\Controllers\Areas\Requests\UpdateAreaRequest as UpdateRequest;
use App\Http\Controllers\Areas\Models\Area;

class AreasController extends Controller
{
    protected $models = 'Area';
    const COLLECTION_NAME = 'areas';
    
    public function __construct(Area $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            
            $data = $this->model->when(!empty($request->name), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })
            ->when(!empty($request->params1), function ($query) use ($request) {
                return $query->where('params1', 'like','%'.  $request->params1 . '%');
            })
            ->when(!empty($request->params2), function ($query) use ($request) {
                return $query->where('params2', 'like','%'.  $request->params2 . '%');
            })
            ->orderBy('id', 'DESC')->paginate(50);
            if ($data->isNotEmpty()) {
            return $this->created([AreasController::COLLECTION_NAME => $data]);

            }
            return $this->noRecord(['message'=>AreasController::RECORD_NOT_FOUND]);
        } catch (Exception $ex) {
            
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $data = $request->only(['name','params1','params2']);
            $result = $this->model->create($data);
            if ($result) {
                return $this->created(['message'=> AreasController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $data = $this->model->find($id);
            if ($data) {
                return $this->created([AreasController::COLLECTION_NAME => $data]);
            }
            return $this->noRecord(['message'=>AreasController::RECORD_NOT_FOUND]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $data = $request->only(['name','params1','params2']);
            $business = $this->model->find($request->id);
            $result = $business->update($data);
            if ($result) {
                return $this->created(['message'=>AreasController::RECORD_UPDATED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {

        try {
            $area = $this->model->find($id);
            if ($area) {
                $area->delete();
                return $this->created(['message'=> AreasController::RECORD_DELETED]);
            }
            return $this->noRecord(['message'=>AreasController::RECORD_NOT_FOUND]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    
}