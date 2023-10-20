<?php

use App\Http\Controllers\Acl\Models\Permission;
use App\Http\Controllers\Users\Models\UserType;
use App\Models\AclPermission;
use App\Models\AclRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Module;

class DefaultValuesSeeder extends Seeder
{
    public function run()
    {
        DB::table('media_types')->insert([
            [
                'name' => 'General Files',
                'short_name' => 'GF',
                'description' => 'All general type of files',
                'sort_order' => 1,
                'created_at' => new DateTime(),
                'updated_at' => new DateTime()
            ],
            [
                'name' => 'Logo',
                'short_name' => 'LO',
                'description' => 'All logo files',
                'sort_order' => 2,
                'created_at' => new DateTime(),
                'updated_at' => new DateTime()
            ],
            [
                'name' => 'Invoices',
                'short_name' => 'Invoice Files',
                'description' => 'Invoice Files',
                'sort_order' => 3,
                'created_at' => new DateTime(),
                'updated_at' => new DateTime()
            ],
            [
                'name' => 'Invoice Logo',
                'short_name' => 'Invoice Logo File',
                'description' => 'Invoice Logo File',
                'sort_order' => 4,
                'created_at' => new DateTime(),
                'updated_at' => new DateTime()
            ],
            [
                'name' => 'Company Logo',
                'short_name' => 'Company Logo File',
                'description' => 'Company Logo File',
                'sort_order' => 4,
                'created_at' => new DateTime(),
                'updated_at' => new DateTime()
            ],
            [
                'name' => 'Profile Pictures',
                'short_name' => 'Profile Pictures',
                'description' => 'Profile Pictures',
                'sort_order' => 6,
                'created_at' => new DateTime(),
                'updated_at' => new DateTime()
            ],
        ]);


        AclRole::insert([
            ['name' => 'Digibits Admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Company Admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Guest', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $modulesList = [
            ['name' => 'Acl', 'namespace' => 'App\\Http\\Controllers\\Acl', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'Accounts', 'namespace' => 'App\\Http\\Controllers\\Accounts', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete", "View Profile", "Import", "Export", "Change Account Passowrd", "Send Email", "Mass Delete", "Transfer Account Ownership"]],
            ['name' => 'ActivityLog', 'namespace' => 'App\\Http\\Controllers\\ActivityLog', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'Application', 'namespace' => 'App\\Http\\Controllers\\Application', 'status' => 'Active', "default_permissions" => [], "type" => "Core"],
            ['name' => 'Calendar', 'namespace' => 'App\\Http\\Controllers\\Calendar', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'Clients', 'namespace' => 'App\\Http\\Controllers\\Clients', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'Comments', 'namespace' => 'App\\Http\\Controllers\\Comments', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'Documents', 'namespace' => 'App\\Http\\Controllers\\Documents', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'Emails', 'namespace' => 'App\\Http\\Controllers\\Emails', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'EmailTemplates', 'namespace' => 'App\\Http\\Controllers\\EmailTemplates', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'Groups', 'namespace' => 'App\\Http\\Controllers\\Groups', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'People', 'namespace' => 'App\\Http\\Controllers\\People', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'Products', 'namespace' => 'App\\Http\\Controllers\\Products', 'status' => 'Inactive', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'Services', 'namespace' => 'App\\Http\\Controllers\\Services', 'status' => 'Inactive', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'Settings', 'namespace' => 'App\\Http\\Controllers\\Settings', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'Tasks', 'namespace' => 'App\\Http\\Controllers\\Tasks', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'Users', 'namespace' => 'App\\Http\\Controllers\\Users', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'SMTPConnections', 'namespace' => 'App\\Http\\Controllers\\SMTPConnections', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'SMTPs', 'namespace' => 'App\\Http\\Controllers\\SMTPs', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'TransectionType', 'namespace' => 'App\\Http\\Controllers\\Transactions', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'Transactions', 'namespace' => 'App\\Http\\Controllers\\Transactions', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'Suppliers', 'namespace' => 'App\\Http\\Controllers\\Suppliers', 'status' => 'Inactive', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'Workflow', 'namespace' => 'App\\Http\\Controllers\\Workflows', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'Customers', 'namespace' => 'App\\Http\\Controllers\\Generics', 'status' => 'Inactive', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'Invoices', 'namespace' => 'App\\Http\\Controllers\\Invoices', 'status' => 'Inactive', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'Estimates', 'namespace' => 'App\\Http\\Controllers\\Estimates', 'status' => 'Inactive', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'Tickets', 'namespace' => 'App\\Http\\Controllers\\Estimates', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'Companies', 'namespace' => 'App\\Http\\Controllers\\Companies', 'status' => 'Inactive', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'Payments', 'namespace' => 'App\\Http\\Controllers\\Payments', 'status' => 'Inactive', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'Leads', 'namespace' => 'App\\Http\\Controllers\\Leads', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete", "View Lead", "Import", "Export", "Send Email", "Mass Delete", "Transfer Ownership"]],
            ['name' => 'LeadComment', 'namespace' => 'App\\Http\\Controllers\\Leads', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'PromoCode', 'namespace' => 'App\\Http\\Controllers\\PromoCodes', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'MonetaryTransactions', 'namespace' => 'App\\Http\\Controllers\\MonetaryTransactions', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'FinancialTransactionExtraInfo', 'namespace' => 'App\\Http\\Controllers\\MonetaryTransactions', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'TradingAccounts', 'namespace' => 'App\\Http\\Controllers\\TradingAccounts', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'Brands', 'namespace' => 'App\\Http\\Controllers\\Brands', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"]],
            ['name' => 'Roles', 'namespace' => 'App\\Http\\Controllers\\Acl\\Roles', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'Permissions', 'namespace' => 'App\\Http\\Controllers\\Acl\\Permissions', 'status' => 'Active', "default_permissions" => [], "type" => "Core"],
            ['name' => 'Profiles', 'namespace' => 'App\\Http\\Controllers\\Acl\\Profiles', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'LeadMarketingDetail', 'namespace' => 'App\\Http\\Controllers\\Leads', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'AccountPersonalInformation', 'namespace' => 'App\\Http\\Controllers\\Accounts', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'AccountTradingExperience', 'namespace' => 'App\\Http\\Controllers\\Accounts', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'AccountContactInformation', 'namespace' => 'App\\Http\\Controllers\\Accounts', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'AccountLegalInformation', 'namespace' => 'App\\Http\\Controllers\\Accounts', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'AccountLeadConversion', 'namespace' => 'App\\Http\\Controllers\\Accounts', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'AccountMarketingDetail', 'namespace' => 'App\\Http\\Controllers\\Accounts', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
            ['name' => 'TradingPlatforms', 'namespace' => 'App\\Http\\Controllers\\TradingPlatforms', 'status' => 'Active', "default_permissions" => ["List", "View", "Create", "Update", "Delete"], "type" => "Core"],
        ];
        $preparedModule = [];
        foreach ($modulesList as $module) {
            $module['default_permissions'] = json_encode($module["default_permissions"]);
            $module['type'] = (isset($module['type'])) ? $module['type'] : 'Other';
            $preparedModule[] = $module;
        }

        Module::insert($preparedModule);

        $modules = Module::where('status', 'Active')->get();
        $permissionsToGenerate = [];
        foreach ($modules as $key => $module) {
            if ($module->default_permissions != null) {
                $permissions = json_decode($module->default_permissions);
                foreach ($permissions as $permission) {
                    $permissionsToGenerate[] = ['module_id' => $module->id, 'name' => $module->name . "." . str_replace(' ', '', $permission), 'description' => $permission, 'type' => 'Auto'];
                }
            }
        }
        Permission::insert($permissionsToGenerate);

        $userTypes = [
            ['name' => 'System User'],
            ['name' => 'Account'],
            ['name' => 'Tutor'],
            ['name' => 'Student'],
        ];

        UserType::insert($userTypes);
    }
}
