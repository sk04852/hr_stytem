<?php

namespace App\Imports;

use App\Http\Controllers\Accounts\Models\Account;
use App\Http\Controllers\Accounts\Models\AccountPersonalInformation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\Rule;

class ImportAccounts implements
    ToCollection,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure
{
    use Importable, SkipsFailures;

    private $errors = [];
    private $accountId = 0;

    public function collection(Collection $rows)
    {

        $rows = $rows->toArray();

            $account = Account::latest('id')->first();
            if ($account) {
                $this->accountId = $account->id;
            }

        foreach ($rows as $row) {

            $validator = Validator::make($row, $this->rules());

            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $messages) {
                    foreach ($messages as $error) {
                        $this->errors[] = $error;
                    }
                }
            }
            else {
                    $AccountsData[] = [
                            'id' => $this->accountId++,
                            'first_name' => $row['first_name'] ?? $row['first_name'] ?? null,
                            'middle_name' => $row['middle_name'] ?? $row['middle_name'] ?? null,
                            'account_name' => $row['account_name'] ?? $row['account_name'] ?? null,
                            'last_name' => $row['last_name'] ?? $row['last_name'] ?? null,
                            'username' => $row['username'] ?? $row['username'] ?? null,
                            'email' => $row['email'] ?? $row['email'] ?? null,
                            'password' => $row['password'] ?? $row['password'] ?? null,
                            'confirm_password' => $row['confirm_password'] ?? $row['confirm_password'] ?? null,
                            'brand_name' => $row['brand_name'] ?? $row['brand_name'] ?? null,
                            'phone' => $row['phone'] ?? $row['phone'] ?? null,
                            'mobile' => $row['mobile'] ?? $row['mobile'] ?? null,
                            'address' => $row['address'] ?? $row['address'] ?? null,
                            'city' => $row['city'] ?? $row['city'] ?? null,
                            'state' => $row['state'] ?? $row['state'] ?? null,
                            'zip' => $row['zip'] ?? $row['zip'] ?? null,
                            'type' => $row['type'] ?? $row['type'] ?? null,
                            'description' => $row['description'] ?? $row['description'] ?? null,
                            'assigned_to' => $row['assigned_to'] ?? $row['assigned_to'] ?? null,
                            'account_description' => $row['account_description'] ?? $row['account_description'] ?? null,
                            'email_opt_out' => $row['email_opt_out'] ?? $row['email_opt_out'] ?? null,
                            'notify_owner' => $row['notify_owner'] ?? $row['notify_owner'] ?? null,
                            'ip' => $row['ip'] ?? $row['ip'] ?? null,
                            'registration_country' => $row['registration_country'] ?? $row['registration_country'] ?? null,
                            'ownership' => $row['ownership'] ?? $row['ownership'] ?? null,
                            'id_type' => $row['id_type'] ?? $row['id_type'] ?? null,
                            'introducing_broker' => $row['introducing_broker'] ?? $row['introducing_broker'] ?? null,
                            'member_of' => $row['member_of'] ?? $row['member_of'] ?? null,
                            'original_retention_owner' => $row['original_retention_owner'] ?? $row['original_retention_owner'] ?? null,
                            'verification_status' => $row['verification_status'] ?? $row['verification_status'] ?? null,
                            'provider_name' => $row['provider_name'] ?? $row['provider_name'] ?? null,
                            'addtional_information' => $row['addtional_information'] ?? $row['addtional_information'] ?? null,
                            'trading_status' => $row['trading_status'] ?? $row['trading_status'] ?? null,
                            'primary_tp_account' => $row['primary_tp_account'] ?? $row['primary_tp_account'] ?? null,
                            'account_enabled' => $row['account_enabled'] ?? $row['account_enabled'] ?? null,
                            'ftd_amount' => $row['ftd_amount'] ?? $row['ftd_amount'] ?? null,
                            'ftd_status' => $row['ftd_status'] ?? $row['ftd_status'] ?? null,
                            'ftd_date' => $row['ftd_date'] ?? $row['ftd_date'] ?? null,
                            'ftd_currency' => $row['ftd_currency'] ?? $row['ftd_currency'] ?? null,
                            'redeposit_status' => $row['redeposit_status'] ?? $row['redeposit_status'] ?? null,
                            'last_deposit_date' => $row['last_deposit_date'] ?? $row['last_deposit_date'] ?? null,
                            'ftd_owner' => $row['ftd_owner'] ?? $row['ftd_owner'] ?? null,
                            'secondary_income' => $row['secondary_income'] ?? $row['secondary_income'] ?? null,
                            'secondary_income_specify' => $row['secondary_income_specify'] ?? $row['secondary_income_specify'] ?? null,
                            'secondary_source_of_income' => $row['secondary_source_of_income'] ?? $row['secondary_source_of_income'] ?? null,
                            'anticipated_account_turnover_annually' => $row['anticipated_account_turnover_annually'] ?? $row['anticipated_account_turnover_annually'] ?? null,
                            'secondary_source_of_income_specify' => $row['secondary_source_of_income_specify'] ?? $row['secondary_source_of_income_specify'] ?? null,
                            'fund_method_country' => $row['fund_method_country'] ?? $row['fund_method_country'] ?? null,
                            'proof_of_residence' => $row['proof_of_residence'] ?? $row['proof_of_residence'] ?? null,
                            'proof_of_identity' => $row['proof_of_identity'] ?? $row['proof_of_identity'] ?? null,
                            'document_status' => $row['document_status'] ?? $row['document_status'] ?? null,
                            'account_type_requested' => $row['account_type_requested'] ?? $row['account_type_requested'] ?? null,
                            'website_language' => $row['website_language'] ?? $row['website_language'] ?? null,
                            'account_status' => $row['account_status'] ?? $row['account_status'] ?? null,
                            'recovery_question' => $row['recovery_question'] ?? $row['recovery_question'] ?? null,
                            'recovery_answer' => $row['recovery_answer'] ?? $row['recovery_answer'] ?? null,
                            'document_verified' => $row['document_verified'] ?? $row['document_verified'] ?? null,
                            'compliance_completed' => $row['compliance_completed'] ?? $row['compliance_completed'] ?? null,
                            'trading_disabled' => $row['trading_disabled'] ?? $row['trading_disabled'] ?? null,
                            'password_for_email' => $row['password_for_email'] ?? $row['password_for_email'] ?? null,
                            'client_category' => $row['client_category'] ?? $row['client_category'] ?? null,
                            'requested_leverage' => $row['requested_leverage'] ?? $row['requested_leverage'] ?? null,
                            'ib_id' => $row['ib_id'] ?? $row['ib_id'] ?? null,
                            'account_type' => $row['account_type'] ?? $row['account_type'] ?? null,
                            'client_type' => $row['client_type'] ?? $row['client_type'] ?? null,
                            'type' => $row['type'] ?? $row['type'] ?? null,
                        ];
                    }

        }
        if (isset($AccountsData)) {
            Account::insert($AccountsData);
        }
    }

     public function getErrors()
     {
         return $this->errors;
     }

    public function rules(): array
    {

        return [
                "first_name"=>"nullable|max:20",
                "account_name"=>"required|max:20",
                "middle_name"=>"max:20",
                "last_name"=>"max:20",
                "lead_id" => "nullable|exists:leads,id",
                "email" => "nullable|email|max:150|unique:accounts,email",
                "assigned_to" => "required|exists:users,id",
                "password" => "nullable|min:6,max:20|confirmed",
                "password_confirmation" => "nullable|min:6,max:20"
        ];
    }


    public function chunkSize(): int
    {
        return 1000;
    }
}
