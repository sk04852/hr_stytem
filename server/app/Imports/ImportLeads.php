<?php

namespace App\Imports;

use App\Http\Controllers\Leads\Models\Lead;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportLeads implements
    ToCollection,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure
{
    use Importable, SkipsFailures;

    private $errors = [];

    public function collection(Collection $rows)
    {

        $rows = $rows->toArray();

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
                    $leadsData[] = [
                            'brand_id' => $row['brand_id'] ?? $row['brand_id'] ?? null,
                            'lead_id' => $row['lead_id'] ?? $row['lead_id'] ?? null,
                            'title' => $row['title'] ?? $row['title'] ?? null,
                            'first_name' => $row['first_name'] ?? $row['first_name'] ?? null,
                            'last_name' => $row['last_name'] ?? $row['last_name'] ?? null,
                            'primary_phone' => $row['primary_phone'] ?? $row['primary_phone'] ?? null,
                            'secondary_phone' => $row['secondary_phone'] ?? $row['secondary_phone'] ?? null,
                            'mobile' => $row['mobile'] ?? $row['mobile'] ?? null,
                            'city' => $row['city'] ?? $row['city'] ?? null,
                            'country' => $row['country'] ?? $row['country'] ?? null,
                            'lead_source' => $row['lead_source'] ?? $row['lead_source'] ?? null,
                            'created_by' => $row['created_by'] ?? $row['created_by'] ?? 1,
                            'primary_email' => $row['primary_email'] ?? $row['primary_email'] ?? null,
                            'secondary_email' => $row['secondary_email'] ?? $row['secondary_email'] ?? null,
                            'assigned_to' => $row['assigned_to'] ?? $row['assigned_to'] ?? null,
                            'lead_status' => $row['lead_status'] ?? $row['lead_status'] ?? null,
                            'ip' => $row['ip'] ?? $row['ip'] ?? null,
                            'lead_supplier' => $row['lead_supplier'] ?? $row['lead_supplier'] ?? null,
                            'promo_code' => $row['promo_code'] ?? $row['promo_code'] ?? null,
                        ];
                    }
        }
        if (isset($leadsData)) {
            Lead::insert($leadsData);
        }
    }

     public function getErrors()
     {
         return $this->errors;
     }

    public function rules(): array
    {

        return [
            'brand_id' => ['required','integer'],
            'lead_id' => ['nullable','unique:leads,lead_id'],
            'title' => ['nullable'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'created_by' => ['required'],
            'primary_phone' => ['nullable'],
            'assigned_to' => ['nullable', 'integer'],
            'lead_status' => ['nullable' , Rule::in([
                                'New',
                                'Not Interested',
                                'Interested',
                                'Call Back',
                                'Call Back Future',
                                'Voice Mail',
                                'Pending',
                                'No Answer',
                                'Wrong Number'
                            ])],
        ];
    }


    public function chunkSize(): int
    {
        return 1000;
    }
}
