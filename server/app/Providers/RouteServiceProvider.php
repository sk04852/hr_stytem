<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
        // HAMZA ALTAF WORK START AT POS AND EMS

        // ACL
        $this->mapApiAclRoutes();

        // Groups
        $this->mapApiGroupRoutes();

        // User Groups
        $this->mapApiUserGroupRoutes();

        // Generics
        $this->mapApiGenericRoutes();

        // Expenses
        $this->mapApiExpensesRoutes();

        // Employees
        $this->mapApiEmployeesRoutes();

        // Employments
        $this->mapApiEmploymentsRoutes();

        // Companies
        $this->mapApiCompaniesRoutes();

        // Assets
        $this->mapApiAssetRoutes();

        // Assign Assets
        $this->mapApiAssignAssetRoutes();

        // Tools
        $this->mapApiToolsRoutes();

        // Invoices
        $this->mapApiInvoicesRoutes();

        // Reports
        $this->mapApiReportsRoutes();

        // Assign Employees
        $this->mapApiAssignEmployeesRoutes();

        // Assign Projects
        $this->mapApiAssignProjectsRoutes();

        // Clients
        $this->mapApiClientsRoutes();

        // Suppliers
        $this->mapApiSuppliersRoutes();

        // Currencies
        $this->mapApiCurrenciesRoutes();

        // Payments
        $this->mapApiPaymentsRoutes();

        // Account Types
        $this->mapApiAccountTypesRoutes();

        // Accounts
        $this->mapApiAccountsRoutes();

        // SMS Configurations
        $this->mapSmsConfigurationRoutes();

        // Leads
        $this->mapApiLeadsRoutes();

        //Brands
        $this->mapBrandsRoutes();

        // Transaction Types
        $this->mapApiTransactionTypesRoutes();

        // Monetary Transactions
        $this->mapApiMonetaryTransactionsRoutes();

        // Promo Codes
        $this->mapApiPromoCodesRoutes();

        // Notifications
        $this->notificationsRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    protected function mapApiGroupRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Groups')
            ->group(base_path('routes/route-files/groups.php'));
    }

    protected function mapApiTransactionTypesRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\TransactionTypes')
            ->group(base_path('routes/route-files/transaction-types.php'));
    }

    protected function mapApiMonetaryTransactionsRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\MonetaryTransactions')
            ->group(base_path('routes/route-files/monetary-transactions.php'));
    }

    protected function mapApiPromoCodesRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\PromoCodes')
            ->group(base_path('routes/route-files/promo-codes.php'));
    }

    protected function mapApiUserGroupRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\UserGroup')
            ->group(base_path('routes/route-files/user-groups.php'));
    }

    protected function mapApiAclRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Acl')
            ->group(base_path('routes/route-files/acl.php'));
    }

    protected function mapApiLeadsRoutes()
    {
        // Route::middleware('api')
        //     ->prefix('api')
        //     ->namespace($this->namespace . '\Leads')
        //     ->group(base_path('routes/route-files/excel.php'));
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Leads')
            ->group(base_path('routes/route-files/leads.php'));
    }

    protected function mapApiAssignEmployeesRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\AssignEmployees')
            ->group(base_path('routes/route-files/assign-employees.php'));
    }

    protected function mapApiAssignProjectsRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\AssignProjects')
            ->group(base_path('routes/route-files/assign-projects.php'));
    }

    // HAMZA WORK START AT POS
    protected function mapApiGenericRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Generics')
            ->group(base_path('routes/route-files/generics.php'));
    }

    protected function mapApiExpensesRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Expenses')
            ->group(base_path('routes/route-files/expenses.php'));
    }

    protected function mapApiEmployeesRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\EmsEmployee')
            ->group(base_path('routes/route-files/employees.php'));
    }

    protected function mapApiEmploymentsRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Employments')
            ->group(base_path('routes/route-files/employments.php'));
    }

    protected function mapApiCompaniesRoutes()
    {

        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Companies')
            ->group(base_path('routes/route-files/companies.php'));
    }

    protected function mapApiCompensationsRoutes()
    {

        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\EmsCompensation')
            ->group(base_path('routes/route-files/compensations.php'));
    }

    protected function mapApiAssetRoutes()
    {

        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Assets')
            ->group(base_path('routes/route-files/assets.php'));
    }

    protected function mapApiAssignAssetRoutes()
    {

        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\AssignAsset')
            ->group(base_path('routes/route-files/assign-assets.php'));
    }

    protected function mapApiToolsRoutes()
    {

        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Tools')
            ->group(base_path('routes/route-files/tools.php'));
    }


    protected function mapApiInvoicesRoutes()
    {

        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Invoices')
            ->group(base_path('routes/route-files/invoices.php'));
    }

    protected function mapApiReportsRoutes()
    {

        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Reports')
            ->group(base_path('routes/route-files/reports.php'));
    }

    protected function mapApiDriverInvoicesRoutes()
    {

        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\DriversInvoices')
            ->group(base_path('routes/route-files/drivers-invoices.php'));
    }

    protected function mapApiClientsRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Clients')
            ->group(base_path('routes/route-files/clients.php'));
    }

    protected function mapApiSuppliersRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Suppliers')
            ->group(base_path('routes/route-files/suppliers.php'));
    }

    protected function mapApiCurrenciesRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Currencies')
            ->group(base_path('routes/route-files/currencies.php'));
    }

    protected function mapApiVendorProductsRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\VendorProducts')
            ->group(base_path('routes/route-files/vendor-products.php'));
    }

    protected function mapApiPreOrderRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Estimates')
            ->group(base_path('routes/route-files/estimates.php'));
    }

    protected function mapApiOutletSettingsRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Outlets')
            ->group(base_path('routes/route-files/outlet-settings.php'));
    }

    protected function mapApiPaymentsRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Payments')
            ->group(base_path('routes/route-files/payments.php'));
    }

    protected function mapApiCreditNotesRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\CreditNotes')
            ->group(base_path('routes/route-files/credit-notes.php'));
    }

    protected function mapApiAccountTypesRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\AccountTypes')
            ->group(base_path('routes/route-files/account-types.php'));
    }

    protected function mapApiAccountsRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Accounts')
            ->group(base_path('routes/route-files/accounts.php'));
    }

    protected function mapSmsConfigurationRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\SMSConfigurations')
            ->group(base_path('routes/route-files/sms-configurations.php'));
    }

    protected function mapBrandsRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace . '\Brands')
            ->group(base_path('routes/route-files/brands.php'));
    }

    protected function notificationsRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace.'\Notifications')
            ->group(base_path('routes/route-files/notifications.php'));
    }
}
