<?php

namespace App\Http\Controllers\Expenses;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Services\ExpensesService;
use App\Http\Resources\Expense\ExpenseCollection;
use App\Http\Controllers\Expenses\Models\ExpenseCategory;
use App\Http\Controllers\Expenses\Models\Expense as ThisModel;
use App\Http\Controllers\Expenses\Requests\CreateExpenseCategoryRequest;
use App\Http\Controllers\Expenses\Requests\UpdateExpenseCategoryRequest;
use App\Http\Controllers\Expenses\Requests\CreateExpenseRequest as CreateRequest;
use App\Http\Controllers\Expenses\Requests\UpdateExpenseRequest as UpdateRequest;

class ExpensesController extends Controller
{
    const MODULE_NAME = 'expenses';
    const COLLECTION_NAME = 'expenses';
    const EXPENSE_CATEGORIES = 'expense-categories';
    const EXPENSE_CATEGORY_NOT_FOUND = 'Expense category not found';
    const EXPENSE_CATEGORY_DELETED = 'Expense category delete successfully';
    const EXPENSE_CATEGORY_NOT_DELETED = 'Expense category delete failed';

    private $userService_;
    private $expenseCategoryModel_;
    private $expensesService_;

    public function __construct(ThisModel $model,
                                UserService $userService,
                                ExpensesService $expensesService,
                                ExpenseCategory $expenseCategoryModel)
    {
        parent::__construct($model);
        $this->userService_ = $userService;
        $this->expenseCategoryModel_ = $expenseCategoryModel;
        $this->expensesService_ = $expensesService;
    }
    /*
        A logged in user should be able to see following expenses.
        1: Created by user.
        2: Reported by user.
        3: Any expense that belongs to a category created by logged in user.
        4: Any expense that belongs to a category shared with logged in user.
    */
    public function index(Request $request)
    {
        try {
            $userCategories = $this->userService_->getExpenseCategoriesByUserId($this->userId());
            $userId = $this->userId();
            $records = $this->model->expenseFilters($request)->forThisCompany($this->companyId())
            ->withExpenseCategory()->withReportedBy()->withCreatedBy()
            ->where(function($query) use($userCategories, $userId) {
                $query->whereIn('category_id', $userCategories->map(function($category) {
                    return $category->id;
                }))->orWhere('reporter_id', $userId)
                ->orWhere('created_by_id', $userId);
            })
            ->orderBy($this->getSortBy(), $this->getSort())
            ->paginate($this->getPerPage());

            if ($records->isNotEmpty()) {
                return $this->created([ExpensesController::COLLECTION_NAME => $records]);
                return ExpenseCollection::collection($records);
            }
            return $this->created(["message" => ExpensesController::RECORD_NOT_FOUND, ExpensesController::COLLECTION_NAME => $records],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
            try {
                $data = $request->all();
                $data['company_id'] = $this->companyId();
                $data['created_by_id'] = $this->userId();
                $data['status'] = $this->getDefaultStatus();
                $data = $this->model->create($data);
                if($data) {
                    return $this->created(['message'=> ExpensesController::RECORD_CREATED]);
                }
            } catch (Exception $ex) {
                return $this->serverError($ex);
            }
    }

    public function show($id)
    {
        try {
            $record = $this->model->withExpenseCategory()->withReportedBy()->withCreatedBy()->find($id);
            if( $record ){
                return $this->created([ExpensesController::MODULE_NAME => $record]);
            }
                return $this->noRecord(['message' => ExpensesController::RECORD_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $data = $request->all();
            $record = $this->model->find($data['id'])->update($data);
            if($record) {
                return $this->created(['message' => ExpensesController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function updateCategory(UpdateExpenseCategoryRequest $request)
    {
        try {
            $data = $request->all();
            $record = ExpenseCategory::find($data['id'])->update($data);
            if($record) {
                return $this->created(['message' => ExpensesController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        try {
            $expense = $this->model->find($id);
            if($expense) {
               $expense->delete();
               return $this->created(['message'=> ExpensesController::RECORD_DELETED]);
            }
            return $this->noRecord(['message' => ExpensesController::RECORD_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function baseQuery(Request $request) {
        $userCategories = $this->userService_->getExpenseCategoriesByUserId($this->userId());
        $userId = $this->userId();
        return
        $this->model->expenseFilters($request)->forThisCompany($this->companyId())
        ->withExpenseCategory()->withReportedBy()->withCreatedBy()
        ->where(function($query) use($userCategories, $userId) {
            $query->whereIn('category_id', $userCategories->map(function($category) {
                return $category->id;
            }))->orWhere('reporter_id', $userId)
            ->orWhere('created_by_id', $userId);
        });
    }

    public function getDefaultStatus() {
        return 'Open';
    }

    public function expenses(Request $request) {

        $totalExpenses = $this->baseQuery($request)->sum('amount');
        $currentMonthExpenses = $this->baseQuery($request)->whereMonth('created_at', Carbon::now()->month)->sum('amount');
        $currentWeekExpenses = $this->baseQuery($request)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');
        return response()->json(['expenses' => [
                    'total_expenses' => $totalExpenses,
                    'current_month_expenses' => $currentMonthExpenses,
                    'current_week_expenses' => $currentWeekExpenses,
            ]
        ]);

    }

    // Expense Categories
    public function storeCategory(CreateExpenseCategoryRequest $request) {
        try {
            $data = $request->all();
            $data['created_by'] = $this->userId();
            $record = $this->expenseCategoryModel_->fill($data);
            if($record->save()) {
                if($request->has('shared_with') && $request->shared_with != NULL) {
                    foreach($request->shared_with as $userId) {
                        if($this->userId() != $userId) {
                            $this->expensesService_->shareExpenseCategoryWithUser($record->id, $userId);
                        }
                    }
                }

                $this->expensesService_->shareExpenseCategoryWithUser($record->id, $this->userId());
                return $this->created(['message' => ExpensesController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function listCateory() {
        $records = ExpenseCategory::all();
        return $this->created([ExpensesController::EXPENSE_CATEGORIES => $records]);
    }

    public function destroyExpense(int $id) {
        try {
            $expense = ExpenseCategory::find($id); 
            if ($expense) {
                $this->expensesService_->deleteExpenseCategoryWithUser($expense->id,$expense->created_by);
                $expense->delete();
                return $this->created(['message'=> ExpensesController::RECORD_DELETED]);
            }  
            return $this->noRecord(['message' => ExpensesController::RECORD_NOT_FOUND],200);

        }
        catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        }
        catch (Exception $ex) {
            $this->serverError($ex);
        }
    }

    public function chart() {
        $chartData = array();
        $chartData['jan'] = $this->model->whereYear('created_at' ,Carbon::now()->year)->whereMonth('created_at', 1)->sum('amount');
        $chartData['feb'] = $this->model->whereYear('created_at' ,Carbon::now()->year)->whereMonth('created_at', 2)->sum('amount');
        $chartData['mar'] = $this->model->whereYear('created_at' ,Carbon::now()->year)->whereMonth('created_at', 3)->sum('amount');
        $chartData['apr'] = $this->model->whereYear('created_at' ,Carbon::now()->year)->whereMonth('created_at', 4)->sum('amount');
        $chartData['may'] = $this->model->whereYear('created_at' ,Carbon::now()->year)->whereMonth('created_at',5)->sum('amount');
        $chartData['jun'] = $this->model->whereYear('created_at' ,Carbon::now()->year)->whereMonth('created_at', 6)->sum('amount');
        $chartData['jul'] = $this->model->whereYear('created_at' ,Carbon::now()->year)->whereMonth('created_at', 7)->sum('amount');
        $chartData['aug'] = $this->model->whereYear('created_at' ,Carbon::now()->year)->whereMonth('created_at', 8)->sum('amount');
        $chartData['sep'] = $this->model->whereYear('created_at' ,Carbon::now()->year)->whereMonth('created_at', 9)->sum('amount');
        $chartData['oct'] = $this->model->whereYear('created_at' ,Carbon::now()->year)->whereMonth('created_at', 10)->sum('amount');
        $chartData['nov'] = $this->model->whereYear('created_at' ,Carbon::now()->year)->whereMonth('created_at', 11)->sum('amount');
        $chartData['dec'] = $this->model->whereYear('created_at' ,Carbon::now()->year)->whereMonth('created_at', 12)->sum('amount');
        return $this->created(['chart' => $chartData]);
    }
}
