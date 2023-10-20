<?php

namespace App\Events\Timeline\Company;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Companies\Models\CompanyPr;

class CompanyUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $company_pr;

    public $event_name;

    public $old_values;

    public $new_values;

    public $data;

    public $additional_information;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CompanyPr $company_pr, string $event_name =  'Company Updated', $old_values = null, $new_values = null, $data = null, $additional_information = null)
    {
        $this->company_pr = $company_pr;
        $this->event_name = $event_name;
        $this->old_values = $old_values;
        $this->new_values = $new_values;
        $this->data = $data;
        $this->additional_information = $additional_information;
    }
}
