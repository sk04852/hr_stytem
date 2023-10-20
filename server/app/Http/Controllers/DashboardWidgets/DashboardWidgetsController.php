<?php
namespace App\Http\Controllers\DashboardWidgets;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Leads\Models\Lead;
use App\Http\Controllers\MonetaryTransactions\Models\MonetaryTransaction;
use App\Http\Controllers\Options\Models\FieldOption;
use App\Http\Controllers\Tickets\Models\Ticket;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class DashboardWidgetsController extends Controller {

    private $leadModel_;
    private $fieldOptionModel_;
    private $ticketModel_;
    private $monetarytTransaction_;

    public function __construct(
        Lead $leadModel,
        FieldOption $fieldOption,
        Ticket $ticket,
        MonetaryTransaction $monetarytTransaction) {
        parent::__construct(
            $this->leadModel_ = $leadModel,
            $this->fieldOptionModel_ = $fieldOption,
            $this->ticketModel_ = $ticket,
            $this->monetarytTransaction_ = $monetarytTransaction
        );
    }

    private function colorGenrater() {
        $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        return '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
    }

    public function getLeadsByStatus() {
        try  {
            $records = array();
            $LeadStatus = $this->fieldOptionModel_->where('type_id', 27)->select('id', 'type_id', 'name')->get();
            foreach ($LeadStatus as $key => $lead) {
                $color = $this->colorGenrater();
                $totalLeads = $this->leadModel_->where('lead_status', $lead->id)->get()->count();
                $lead['total_leads'] = $totalLeads;
                $lead['color'] = $color;
                array_push($records, $lead);
            }
            return $this->created(['leads_by_status' => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function getTicketsByStatus() {
        try {
            $TicketStatus = $this->fieldOptionModel_->where('type_id', 23)->select('id', 'type_id', 'name')->get();
            $records = array();
            foreach ($TicketStatus as $key => $ticket) {
                $totalTicket = $this->ticketModel_->where('status', $ticket->id)->count();
                $ticket['total_tickets'] = $totalTicket;
                array_push($records, $ticket);
            }
            return $this->created(['tickets_by_status' => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function getDashbaordItems() {
        try {
            $dashboardItems['total_leads'] = $this->leadModel_->count();
            $dashboardItems['total_tickets'] = $this->ticketModel_->count();
            $dashboardItems['total_deposits'] = $this->monetarytTransaction_->join('transaction_types as tt', 'tt.id', 'transaction_type_id')->where('tt.action', 'Add')->sum('amount');
            $dashboardItems['total_withdrawals'] = $this->monetarytTransaction_->join('transaction_types as tt', 'tt.id', 'transaction_type_id')->where('tt.action', 'Subtract')->sum('amount');
            return $this->created(['dashboard_items' => $dashboardItems]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function getDepositsByDays() {
        try {
            $records = array();
            $dates = array();
            for ($i=9; $i > 0; --$i) {
                array_push($dates, Carbon::now()->subDays($i));
            }
            array_push($dates, Carbon::now());
            // return $dates;
            $dates = array_reverse($dates);
            foreach ($dates as $key => $depositsByDays) {
                $data = array();
                $data['total_deposits'] = DB::table('monetary_transactions as mt')->join('transaction_types as tt', 'tt.id', 'transaction_type_id')->where('tt.action', 'Add')->whereDate('mt.created_at', $depositsByDays)->sum('mt.amount');
                $data['date'] = $depositsByDays->format('d-M-Y');
                array_push($records, $data);
            }
            return $this->created(['deposits_by_days' => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function getMonthlyDepositsWidthdrawals() {
        try {
            $record = array();
            $data = array();
            $data['total'] = DB::table('monetary_transactions as mt')->join('transaction_types as tt', 'tt.id', 'transaction_type_id')->where('tt.action', 'Add')->whereMonth('mt.created_at', Carbon::now())->sum('mt.amount');
            $data['label'] = 'Deposits';
            $data['color'] = $this->colorGenrater();
            array_push($record, $data);
            $data['total'] = DB::table('monetary_transactions as mt')->join('transaction_types as tt', 'tt.id', 'transaction_type_id')->where('tt.action', 'Subtract')->whereMonth('mt.created_at', Carbon::now())->sum('mt.amount');
            $data['label'] = 'Withdrawals';
            $data['color'] = $this->colorGenrater();
            array_push($record, $data);
            return $this->created(['monthy_deposits_withdrawals' => $record]);
        } catch (Exception $ex) {
            $this->serverError($ex);
        }
    }

    public function getDepositsByAssignee() {
        try {
            $record = array();
            $users = $this->monetarytTransaction_->groupBy('assigned_to')->get();
            foreach($users as $key => $data) {
                $obg = array();
                $obg['total_deposits'] = $this->monetarytTransaction_->join('transaction_types as tt', 'tt.id', 'transaction_type_id')->where('assigned_to', $data->assigned_to)->where('tt.action', 'Add')->sum('amount');
                $obg['user_name'] = $this->monetarytTransaction_->join('users as usr', 'usr.id', 'assigned_to')->where('assigned_to', $data->assigned_to)->select('first_name', 'last_name')->first();
                $obg['color'] = $this->colorGenrater();
                array_push($record, $obg);
            }
            return $this->created(['deposits_by_assignee' => $record]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    private function getAmount($date, string $type, $action, ?string $column) {
        if ($action == 'sum') {
            return DB::table('monetary_transactions as mt')->join('transaction_types as tt', 'tt.id', 'transaction_type_id')->where('tt.action', $type)->whereDate('mt.created_at', $date)->$action($column);
        } else if($action == 'count') {
            return DB::table('monetary_transactions as mt')->join('transaction_types as tt', 'tt.id', 'transaction_type_id')->where('tt.action', $type)->whereDate('mt.created_at', $date)->$action();
        }
    }

    private function getPercentage(int $newVAl, int $oldVal) {

        if ($newVAl > $oldVal) {
            $Data = ((-($oldVal - $newVAl)) / $newVAl) * 100;

        } else {
            $Data = (($newVAl - $oldVal) / $oldVal)* 100;
        }

        return $Data;
    }

    public function getDailyDepositsWithdrawals() {
        try {
            $yeasterdayDate = Carbon::now()->subDay(1);
            $todayDate = Carbon::now();
            $todayData = array();
            $yesterdayData = array();
            $todayData['deposits_total'] = $this->getAmount($todayDate, 'Add', 'sum', 'mt.amount');
            $todayData['deposits_count'] = $this->getAmount($todayDate, 'Add', 'count', null);
            $todayData['withdrawals_total'] = $this->getAmount($todayDate, 'Subtract', 'sum', 'mt.amount');
            $todayData['withdrawals_count'] = $this->getAmount($todayDate, 'Subtract', 'count', null);
            $yesterdayData['deposits_total'] = $this->getAmount($yeasterdayDate, 'Add', 'sum', 'mt.amount');
            $yesterdayData['deposits_count'] = $this->getAmount($yeasterdayDate, 'Add', 'count', null);
            $yesterdayData['withdrawals_total'] = $this->getAmount($yeasterdayDate, 'Subtract', 'sum', 'mt.amount');
            $yesterdayData['withdrawals_count'] = $this->getAmount($yeasterdayDate, 'Subtract', 'count', null);
            $percentage = array();
            $percentage['deposits_total'] = round($this->getPercentage($todayData['deposits_total'], $yesterdayData['deposits_total']));
            $percentage['deposits_count'] = round($this->getPercentage($todayData['deposits_count'], $yesterdayData['deposits_count']));
            $percentage['withdrawals_total'] = round($this->getPercentage($todayData['withdrawals_total'], $yesterdayData['withdrawals_total']));
            $percentage['withdrawals_count'] = round($this->getPercentage($todayData['withdrawals_count'], $yesterdayData['withdrawals_count']));
            return $this->created(['daily_deposits_withdrawals' => ['today' =>$todayData, 'yesterday' => $yesterdayData, 'percentage' => $percentage]]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
