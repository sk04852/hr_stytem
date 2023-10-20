<?php

namespace Tests\Unit;
use Tests\TestCase;
use App\Exceptions\FaultyTransactionException;
use App\Models\Module;
use App\Http\Controllers\Users\Models\User;


class TransactionsTest extends TestCase
{
    /** @test */
    public function test_create_quick_debit_creates_debit()
    {
        $this->withoutExceptionHandling();
        $module = Module::find(19);
        $userEntity = new User();
        $userEntity = $userEntity->find(1);
        $transactionId = $this->generateRandomTransactionId(10);
        $transactionService = app()->make('App\Http\Services\TransactionsService');
        $this->assertTrue($transactionService->quickDebit($module, $userEntity, $transactionId, 100));
    }

    /** @test */
    public function test_create_quick_credit_creates_credit()
    {
        $this->withoutExceptionHandling();
        $module = Module::find(19);
        $userEntity = new User();
        $userEntity = $userEntity->find(1);
        $transactionId = $this->generateRandomTransactionId(10);
        $transactionService = app()->make('App\Http\Services\TransactionsService');
        $this->assertTrue($transactionService->quickCredit($module, $userEntity, $transactionId, 500));
    }

    /** @test */
    public function test_create_credit_transaction_saves_transaction()
    {
        $this->withoutExceptionHandling();
        $module = Module::find(20);
        $userEntity = new User();
        $userEntity = $userEntity->find(2);
        $transactionId = $this->generateRandomTransactionId(10);
        $transactionService = app()->make('App\Http\Services\TransactionsService');
        $this->assertTrue($transactionService->addTransaction($module, $userEntity, $transactionId, 2, 500, "Test Transaction"));
    }

    /** @test */
    public function test_create_debit_transaction_saves_transaction()
    {
        $this->withoutExceptionHandling();
        $module = Module::find(20);
        $userEntity = new User();
        $userEntity = $userEntity->find(2);
        $transactionId = $this->generateRandomTransactionId(10);
        $transactionService = app()->make('App\Http\Services\TransactionsService');
        $this->assertTrue($transactionService->addTransaction($module, $userEntity, $transactionId, 1, 1200, "Test Transaction"));
    }


    /** @test */
    public function test_create_quick_credit_fails_with_invalid_entity_exception()
    {
        $this->expectException(FaultyTransactionException::class);
        $module = Module::find(19);
        $userEntity = new User();
        $transactionId = $this->generateRandomTransactionId(10);
        $transactionService = app()->make('App\Http\Services\TransactionsService');
        $transactionService->quickCredit($module, $userEntity, $transactionId, 500);
    }

    /** @test */
    public function test_create_quick_debit_fails_with_invalid_entity_exception()
    {
        $this->expectException(FaultyTransactionException::class);
        $module = Module::find(19);
        $userEntity = new User();
        $transactionId = $this->generateRandomTransactionId(10);
        $transactionService = app()->make('App\Http\Services\TransactionsService');
        $transactionService->quickDebit($module, $userEntity, $transactionId, 500);
    }

    /** @test */
    public function test_create_quick_debit_fails_with_invalid_module_exception()
    {
        $this->expectException(\TypeError::class);
        $module = Module::find(1000);
        $userEntity = new User();
        $userEntity = $userEntity->find(2);
        $transactionId = $this->generateRandomTransactionId(10);
        $transactionService = app()->make('App\Http\Services\TransactionsService');
        $transactionService->quickDebit($module, $userEntity, $transactionId, 500);
    }

    /** @test */
    public function test_fetch_transactions_lists_transactions()
    {
        $this->withoutExceptionHandling();
        $module = Module::find(19);
        $userEntity = new User();
        $userEntity = $userEntity->find(1);
        $transactionService = app()->make('App\Http\Services\TransactionsService');
        $numberOfTransactions = 10;
        $relationModuleId = getModuleIdFromEntity($userEntity);
        $transactions = $transactionService->fetchTransactions($module, $userEntity, null, 10);
        foreach($transactions as $transaction) {
            $this->assertTrue(
                $transaction->transaction_module_id == $module->id && 
                $transaction->relation_id == $userEntity->id && 
                $transaction->relation_module_id == $relationModuleId
            );
        }

        $this->assertTrue(count($transactions) <= $numberOfTransactions);
    }
    

    /** @test */
    public function test_fetch_single_transaction_gets_one_trasction()
    {
        $this->withoutExceptionHandling();
        $transactionId = "mAbHobhw38";
        $transactionService = app()->make('App\Http\Services\TransactionsService');
        $transaction = $transactionService->fetchTransactionByTransactionId($transactionId);
        $this->assertTrue(!is_null($transaction));
    }

    private function generateRandomTransactionId($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}