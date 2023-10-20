<?php

namespace App\Imports;

use App\Events\SignupSuccessfullEvent;
use App\Http\Controllers\EmsEmployee\Models\Employee;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ImportEmployee implements
    ToCollection,
    WithHeadingRow,
    WithValidation
{
    use Importable;

    public function collection(Collection $rows)
    {

        foreach ($rows as $row) {

               $password =  Str::random(12);
               $verificationToken =  Str::random(12);
                Employee::create([
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'email' => $row['email'],
                    'password' => Hash::make($row['password']) ?? Hash::make($password),
                    'password_confirmation' => Hash::make($row['password_confirmation']) ?? Hash::make($password),
                    'employee_number' => $row['employee_number'] ?? $row['employee_number'] ?? null,
                    'gender' => $row['gender'] ?? $row['gender'] ?? null,
                    'address' => $row['address'] ?? $row['address'] ?? null,
                    'city' => $row['city'] ?? $row['city'] ?? null,
                    'country' => $row['country'] ?? $row['country'] ?? null,
                    'code' => $row['code'] ?? $row['code'] ?? null,
                    'state' => $row['state'] ?? $row['state'] ?? null,
                    'zip' => $row['zip'] ?? $row['zip'] ?? null,
                    'phone' => $row['phone'] ?? $row['phone'] ?? null,
                    'mobile' => $row['mobile'] ?? $row['mobile'] ?? null,
                    'employment_start' => $row['employment_start'] ?? $row['employment_start'] ?? null,
                    'salary' => $row['salary'] ?? $row['salary'] ?? null,
                    'about_me' => $row['about_me'] ?? $row['about_me'] ?? null,
                    'facebook_links' => $row['facebook_links'] ?? $row['facebook_links'] ?? null,
                    'linkedin_links' => $row['linkedin_links'] ?? $row['linkedin_links'] ?? null,
                    'twitter_links' => $row['twitter_links'] ?? $row['twitter_links'] ?? null,
                    'nationality' => $row['nationality'] ?? $row['nationality'] ?? null,
                    'birth_date' => $row['birth_date'] ?? $row['birth_date'] ?? null,
                    'marital_status' => $row['marital_status'] ?? $row['marital_status'] ?? null,
                    'personal_phone' => $row['personal_phone'] ?? $row['personal_phone'] ?? null,
                    'personal_email' => $row['personal_email'] ?? $row['personal_email'] ?? null,
                    'ec_full_name' => $row['ec_full_name'] ?? $row['ec_full_name'] ?? null,
                    'ec_phone' => $row['ec_phone'] ?? $row['ec_phone'] ?? null,
                    'ec_email' => $row['ec_email'] ?? $row['ec_email'] ?? null,
                    'ec_relationship' => $row['ec_relationship'] ?? $row['ec_relationship'] ?? null,
                ]);

               if ($row['send_email'] == 'Yes') {
                   event(new SignupSuccessfullEvent( $row['first_name'],  $row['email'], $verificationToken));
               }

            }
    }

    public function rules(): array
    {

        return [
            '*.send_email' => ['required', Rule::in(['Yes', 'No'])],
            '*.email' => ['required','email', 'distinct'],
            '*.employee_number' => ['string','distinct'],
            '*.first_name' => ['required','max:20'],
            '*.last_name' => ['required','max:20'],
            '*.password' => ['nullable','min:6','max:20','confirmed'],
            '*.password_confirmation' => ['nullable','min:6','max:20'],
            '*.address' => ['nullable', 'string'],
            '*.city' => ['nullable', 'string'],
            '*.country' => ['nullable', 'string'],
            '*.code' => ['nullable', 'string'],
            '*.state' => ['nullable', 'string'],
            '*.zip' => ['nullable', 'string'],
            '*.phone' => ['nullable', 'numeric', 'digits_between:8,15'],
            '*.mobile' => ['nullable', 'numeric', 'digits_between:8,15'],
            '*.facebook_links' => ['nullable', 'url'],
            '*.linkedin_links' => ['nullable', 'url'],
            '*.twitter_links' => ['nullable', 'url'],
            '*.about_me' => ['nullable', 'string'],
            '*.employment_start' => ['nullable', 'date'],
            '*.birth_date' => ['nullable', 'date'],
            '*.marital_status' => ['nullable', 'string'],
            '*.nationality' => ['nullable', 'string'],
            '*.personal_phone' => ['nullable', 'numeric', 'digits_between:10,15'],
            '*.personal_email' => ['nullable', 'email'],
            '*.gender' => ['nullable', Rule::in(['Male', 'Female'])],
            '*.ec_full_name' => ['nullable', 'string'],
            '*.ec_phone' => ['nullable', 'numeric', 'digits_between:10,15'],
            '*.ec_email' => ['nullable', 'email'],
            '*.ec_relationship' => ['nullable', 'string'],
        ];
    }


    public function chunkSize(): int
    {
        return 1000;
    }
}
