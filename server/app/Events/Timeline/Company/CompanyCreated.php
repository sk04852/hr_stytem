<?php

namespace App\Events\Timeline\Company;


use App\Http\Controllers\Companies\Models\CompanyPr;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $company_pr;

    public $event_name;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CompanyPr $company_pr, string $event_name = 'Company Created')
    {
        $this->company_pr = $company_pr;
        $this->event_name = $event_name;
    }
}
