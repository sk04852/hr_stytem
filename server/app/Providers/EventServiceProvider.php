<?php

namespace App\Providers;

use App\Events\ClientStatementEvent;
use App\Events\EmployeeCodeForSignupEvent;
use App\Events\ForgotPasswordEvent;
use App\Events\IncomingPaymentEvent;
use App\Events\UpdateIncomingPaymentEvent;
use App\Events\InvoiceEmailEvent;
use App\Events\NewClientAddedEvent;
use App\Events\RegisterationWithPromoCode;
use App\Events\WorkflowEvent;
use App\Listeners\WorkflowListener;
use App\Listeners\UpdateIncomingPaymentEventListener;
use App\Events\ResendVerificationTokenEvent;
use App\Events\SendEmailEvent;
use App\Events\SignupSuccessfullEvent;
use App\Events\TicketNotifyUser;
use App\Events\TimeOffApprovedEvent;
use App\Events\TwoFactorAuthDisabledEvent;
use App\Events\TwoFactorAuthEnabledEvent;
use App\Events\UserSignedUpEvent;
use App\Events\VerificationEmailByTokenEvent;
use App\Http\Controllers\Accounts\Models\Account;
use App\Listeners\EmployeeCodeForSignupListener;
use App\Listeners\ResendVerificationTokenListener;
use App\Listeners\SignupSuccessfullListener;
use App\Listeners\TimeOffApprovedListener;
use App\Listeners\VerificationEmailByTokenListener;
use App\Listeners\WorkflowEventListener;
use App\Listeners\TicketNotifyUserLister;
use App\Listeners\CacheInvalidatorEventListner;
use App\Listeners\ClientStatementListener;
use App\Listeners\ForgotPasswordListener;
use App\Listeners\IncomingPaymentNotificationListener;
use App\Listeners\InvoiceEmailListener;
use App\Listeners\NewClientAddedListener;
use App\Listeners\RegisterationWithPromoCodeListener;
use App\Listeners\SendEmailEventListener;
use App\Listeners\TwoFactorAuthDisabledListener;
use App\Listeners\TwoFactorAuthEnabledListener;
use App\Listeners\UserSignedUpEventListener;
use App\Observers\AccountObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Observers\CandidateCVObserver;
use App\Http\Controllers\CandidateCV\Models\CandidateCVJobHistory;
use App\Observers\CandidateCVJobHistoryObserver;
use App\Http\Controllers\CandidateCV\Models\CandidateCVEducation;
use App\Observers\CandidateCVEducationObserver;
use App\Http\Controllers\CandidateCV\Models\CandidateCVFiles;
use App\Observers\CandidateCVFilesObserver;
use App\Http\Controllers\Jobs\Models\JobPr;
use App\Observers\JobPrObserver;
use App\Http\Controllers\Companies\Models\Company;
use App\Observers\CompanyObserver;
use App\Http\Controllers\Companies\Models\CompanyContact;
use App\Observers\CompanyContactObserver;
use App\Http\Controllers\Companies\Models\CompanyLocation;
use App\Observers\CompanyLocationObserver;
use App\Http\Controllers\Companies\Models\CompanyFile;
use App\Observers\CompanyFileObserver;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        WorkflowEvent::class => [
            WorkflowListener::class,
        ],
        TimeOffApprovedEvent::class => [
            TimeOffApprovedListener::class,
        ],
        SignupSuccessfullEvent::class => [
            SignupSuccessfullListener::class,
        ],
        ResendVerificationTokenEvent::class => [
            ResendVerificationTokenListener::class,
        ],
        VerificationEmailByTokenEvent::class => [
            VerificationEmailByTokenListener::class,
        ],
        EmployeeCodeForSignupEvent::class => [
            EmployeeCodeForSignupListener::class,
        ],
        CacheInvalidatorEvent::class => [
            CacheInvalidatorEventListner::class
        ],
        WorkflowEvent::class => [
            WorkflowEventListener::class,
        ],

        // Tickets
        TicketNotifyUser::class => [
            TicketNotifyUserLister::class
        ],

        // Clients
        NewClientAddedEvent::class => [
            NewClientAddedListener::class
        ],

        // Registeration With PromoCode

        RegisterationWithPromoCode::class => [
            RegisterationWithPromoCodeListener::class
        ],

        ForgotPasswordEvent::class => [
            ForgotPasswordListener::class
        ],

        // Client Statements
        ClientStatementEvent::class => [
            ClientStatementListener::class
        ],

        // Send Invoice Via Email
        InvoiceEmailEvent::class => [
            InvoiceEmailListener::class
        ],

        //Send Incoming Payment Via Email
        IncomingPaymentEvent::class => [
            IncomingPaymentNotificationListener::class
        ],

        //Send Updating Payment Via Email
        UpdateIncomingPaymentEvent::class => [
            UpdateIncomingPaymentEventListener::class
        ],
        UserSignedUpEvent::class => [
            UserSignedUpEventListener::class
        ],
        SendEmailEvent::class => [
            SendEmailEventListener::class
        ],
        TwoFactorAuthDisabledEvent::class => [
            TwoFactorAuthDisabledListener::class
        ],
        TwoFactorAuthEnabledEvent::class => [
            TwoFactorAuthEnabledListener::class
        ],

    ];

    /**
     * Class event subscribers.
     *
     * @var array
     */
    protected $subscribe = [
        //Company Listener
//        \App\Listeners\CompanyEventListener::class,
        \App\Listeners\AutoGeneratingURLListener::class,
        \App\Listeners\TimelineEventListener::class,
        \App\Listeners\UserModuleListener::class
    ];

    public function boot()
    {
        parent::boot();
//        CandidateCV::observe(CandidateCVObserver::class);
//        CandidateCVJobHistory::observe(CandidateCVJobHistoryObserver::class);
//        CandidateCVEducation::observe(CandidateCVEducationObserver::class);
//        CandidateCVFiles::observe(CandidateCVFilesObserver::class);
//        JobPr::observe(JobPrObserver::class);
//        Company::observe(CompanyObserver::class);
//        CompanyContact::observe(CompanyContactObserver::class);
//        CompanyLocation::observe(CompanyLocationObserver::class);
//        CompanyFile::observe(CompanyFileObserver::class);
        //Account::observe(AccountObserver::class);
    }
}
