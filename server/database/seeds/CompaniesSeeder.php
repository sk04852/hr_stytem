<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Companies\Models\Company;

class CompaniesSeeder extends Seeder
{

    public function run()
    {
        $company = new Company();
        $company->name = "Digibits Technolgies";
        $company->primary_email = "info@digibits.xyz";
        $company->city = "Nicosia";
        $company->status = 'Active';
        $company->address = "Address";
        $company->country = "Cyprus";
        $company->save();
    }
}
