<?php

namespace App\Http\Controllers\Statements;

use App\Events\ClientStatementEvent;
use App\Http\Controllers\Clients\Models\Client;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientStatements\ClientStatementResource;
use App\Http\Services\ClientStatementsService;
use Illuminate\Http\Request;

class ClientStatementsController extends Controller
{
    // you can remove this service
    const INVOICE_NUMBER = 'Invalid invoice number';
    const STATEMENT_SENT = 'Transactions statement sent to client successfully';
    private $statementService_;

    public function __construct()
    {
       
    }


    
}
