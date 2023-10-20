<?php

namespace App\Http\Controllers\Accounts;

use App\Events\RegisterationWithPromoCode;
use App\Events\WorkflowEvent;
use Exception;
use Illuminate\Support\Str;
use App\Notifications\VerifyAccountNotification;
use App\Http\Controllers\Users\Models\User;
use App\Http\Controllers\Accounts\Models\Account;
use App\Http\Controllers\People\Models\Person;
use App\Http\Controllers\TradingAccounts\Models\TradingAccount;
use App\Http\Controllers\Accounts\Models\AccountContactInformation;
use App\Http\Controllers\Accounts\Models\AccountLeadConversion;
use App\Http\Controllers\Accounts\Models\AccountLegalInformation;
use App\Http\Controllers\Accounts\Models\AccountMarketingDetail;
use App\Http\Controllers\Accounts\Models\AccountPersonalInformation;
use App\Http\Controllers\Accounts\Models\AccountTradingExperience;
use App\Http\Controllers\Accounts\Requests\AccountMassDeleteRequest;
use App\Http\Controllers\Accounts\Requests\AccountUpdateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Accounts\Requests\RegisterAccountRequest;
use App\Http\Controllers\Accounts\Requests\resendVerifyEmailTokenRequest;
use App\Http\Controllers\Accounts\Requests\VerifyAccountEmailByTokenRequest;
use App\Http\Controllers\Comments\Models\Comment;
use App\Http\Controllers\Accounts\Requests\TransferOwnershipRequest;
use App\Http\Controllers\People\PeopleController;
use App\Http\Controllers\People\Requests\PersonChangePasswordRequest;
use App\Http\Controllers\People\Requests\PersonUpdateRequest;
use App\Http\Controllers\PromoCodes\Models\PromoCode;
use App\Http\Services\EmailsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Modules\Enums\LogAction;
use App\Http\Controllers\Modules\Enums\LogTypeEnum;
use App\Http\Controllers\Modules\Enums\ModuleEnum;

class AccountsController extends PeopleController
{
    private $accountPersonalInformationModel = NULL;
    private $accountTradingAccountModel = NULL;
    private $accountTradingExperienceModel = NULL;
    private $accountMarketingDetailsModel = NULL;
    private $accountLegalInformationModel = NULL;
    private $accountLeadConversionModel = NULL;
    private $user = NULL;
    const MODULE_NAME = 'Accounts';
    const COLLECTION_NAME = 'accounts';
    const ACCOUNT_NOT_FOUND = 'Account not found, invalid account';
    const ACCOUNT_DELETED = 'Account has been deleted';
    const ACCOUNTS_DELETED = 'Accounts have been deleted';
    const ACCOUNT_COMMENT_ADDED = 'Comment added';
    const ACCOUNT_COMMENTS_ADDED = 'Comments have been added';
    const ACCOUNT_NOT_DELETED = 'Unable to delete account';
    const ACCOUNT_UPDATED = 'Account data updated';
    const ACCOUNT_CREATED = 'Account created successfuly';
    const ACCOUNT_COMMENT_CREATED = 'A new comment has been posted';
    const ACCOUNT_COMMENT_UPDATE = 'Comment has been updated';
    const ACCOUNT_COMMENT_DELETED = 'Comment has been deleted';
    const ACCOUNT_COMMENT_NOT_FOUND = 'Invalid comment, unable to update';
    const ACCOUNT_NOT_UPDATED = 'Unable to update account data';
    const ACCOUNT_OWNERSHIP_CHANGED = 'Accounts ownership has been changed';
    public function __construct(
        Account $model,
        AccountPersonalInformation $accountPersonalInformationModel,
        AccountTradingExperience $accountTradingExperienceModel,
        TradingAccount $accountTradingAccountModel,
        AccountMarketingDetail $accountMarketingDetailsModel,
        AccountLeadConversion $accountLeadConversionModel,
        AccountLegalInformation $accountLegalInformation,
        EmailsService $emailsService,
        User $user
    ) {
        parent::__construct($model, $emailsService);
        $this->model = $model;
        $this->accountPersonalInformationModel = $accountPersonalInformationModel;
        $this->accountTradingAccountModel = $accountTradingAccountModel;
        $this->accountTradingExperienceModel = $accountTradingExperienceModel;
        $this->accountMarketingDetailsModel = $accountMarketingDetailsModel;
        $this->accountLegalInformationModel = $accountLegalInformation;
        $this->accountLeadConversionModel = $accountLeadConversionModel;
        $this->user = $user;
    }

    protected function preRegistration(Person $model)
    {
        $userDefaultBrand = $this->user->getDefaultBrand($this->userId());
        $model->setAssignedId($this->userId());
        $model->setBrandId($userDefaultBrand->id);
        return $model;
    }

    protected function postRegistration(Person $model)
    {
        event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'create'));
        event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'saved'));
        $this->getAccountEmailVerificationToken($model);
        $this->savePersonalInformation($model);
        $this->saveTradingExperience($model);
        return $model;
    }
    private function savePersonalInformation(Person $model)
    {
        $personalInformation = request()->get('personal_information');
        $personalInformation['us_citizen'] = ($personalInformation['us_citizen'] == 1) ? 1 : 0;
        $personalInformation['recieve_newsletters'] = ($personalInformation['recieve_newsletters'] == 1) ? 1 : 0;
        $this->accountPersonalInformationModel->fill($personalInformation);
        $model->accountPersonalInformation()->save($this->accountPersonalInformationModel);
    }
    private function saveTradingExperience(Person $model)
    {
        $tradingExperience = request()->get('trading_experience');
        $tradingExperience['have_previously_traded_cfds'] = ($tradingExperience['have_previously_traded_cfds'] == true) ? true : false;
        $this->accountTradingExperienceModel->fill($tradingExperience);
        $model->accountTradingExperience()->save($this->accountTradingExperienceModel);
    }
    public function resendVerifyEmailToken(resendVerifyEmailTokenRequest $request)
    {
        try {
            $data = $request->all();
            $account = $this->model->where('email', $data['email'])->first();
            if ($account) {
                $this->getAccountEmailVerificationToken($account);
                return $this->created(['Account' => $account, 'message' => AccountsController::ACCOUNT_RESEND_TOKEN]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    public function getAccountEmailVerificationToken($account)
    {
        $NotificationData = [
            'full_name' => $account->getFullName(),
            'token' => Str::random(12)
        ];
        $account->setPasswordRecoveryToken($NotificationData['token']);
        // $account->notify(new VerifyAccountNotification($NotificationData));
    }
    public function verifyAccountEmailByToken(VerifyAccountEmailByTokenRequest $request)
    {
        try {
            $data = $request->all();
            $account = $this->model->where('password_recovery_token', $data['token'])->first();
            $tokenCreatedAt = new \DateTime($account->password_recovery_token_created_at);
            $now = new \DateTime();
            if ($now->diff($tokenCreatedAt)->format('%i') > 60) {
                return $this->failed(["error" => AccountsController::VERIFY_EMAIL_ERROR_TOKEN]);
            } else {
                $account->setEmailVerified();
                return $this->created(["message" => AccountsController::VERIFY_EMAIL_SUCCESS]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function profile(int $account_id)
    {
        try {
            $account = $this->findById($account_id, [
                                                        'user',
                                                        'assignedTo',
                                                        'accountPersonalInformation',
                                                        'accountContactInformation',
                                                        'brand',
                                                    ]);
            $account->number_of_trading_accounts = $account->tradingAccounts->count();
            return $this->created(['account' => $account]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return $this->noRecord(["message" => AccountsController::ACCOUNT_NOT_FOUND]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function createAcccount(
            RegisterAccountRequest $request,
            Account $account,
            User $user,
            AccountPersonalInformation $accountPersonalInformation,
            AccountTradingExperience $accountTradingExperience,
            AccountContactInformation $accountContactDetail,
            AccountLegalInformation $accountLegelInformation,
            AccountLeadConversion $accountLeadConversion,
            AccountMarketingDetail $accountMarketingDetail) {

        $data = $request->all();
        $password = $request->has('password') ? $request->has('password') :  env("ACCOUNT_DEFAULT_PASSWORD");
        $data['password'] = Hash::make($password);
        $user->encrypted_password = Crypt::encryptString($request->password);
        $user->role_id = 3; // Role = Guest
        $user->type_id = 2; // User Type = Account
        $user->company_id = $this->companyId();
        $user->fill($data);
        $user->save();

        $this->addLog(LogTypeEnum::Info, null, $user, $user, LogAction::Created, ModuleEnum::Users);

        $data['user_id'] = $user->id;

        if (isset($data['external_kyc'])) {
            $data['verification_status'] = !isset($data['external_kyc']['verification_status'])? "Not Verified": $data['external_kyc']['verification_status'];
            $data['provider_name'] = !isset($data['external_kyc']['provider_name'])? "No Provider": $data['external_kyc']['provider_name'];
            $data['addtional_information'] = !isset($data['external_kyc']['provider_name'])? "N/A": $data['external_kyc']['addtional_information'];
        }

        if (isset($data['description_details'])) {
            $data['description'] = !isset($data['description_details']['description'])? NULL: $data['description_details']['description'];
            $data['account_description'] = !isset($data['description_details']['account_description'])? NULL: $data['description_details']['account_description'];
        }
        if (isset($data['trading_Status'])) {
            $data['trading_status'] = !isset($data['trading_Status']['trading_status'])? fieldOptionNameToId("Self Traded", 137): $data['trading_Status']['trading_status'];
            $data['primary_tp_account'] = !isset($data['trading_Status']['primary_tp_account'])? NULL: $data['trading_Status']['primary_tp_account'];
            $data['account_enabled'] = !isset($data['trading_Status']['account_enabled'])? false : (bool) $data['trading_Status']['account_enabled'];
        }

        if (isset($data['financial_status'])) {
            $data['ftd_amount'] = !isset($data['financial_status']['ftd_amount'])? 0.00 : $data['financial_status']['ftd_amount'];
            $data['ftd_status'] = !isset($data['financial_status']['ftd_status'])? false : (bool) $data['financial_status']['ftd_status'];
            $data['ftd_date'] =!isset($data['financial_status']['ftd_date'])? NULL: new Carbon($data['financial_status']['ftd_date']);
            $data['ftd_currency'] = !isset($data['financial_status']['ftd_currency'])? NULL: $data['financial_status']['ftd_currency'];
            $data['redeposit_status'] = !isset($data['financial_status']['redeposit_status'])? false: $data['financial_status']['redeposit_status'];
            $data['last_deposit_date'] = !isset($data['financial_status']['last_deposit_date'])? NULL: new Carbon($data['financial_status']['last_deposit_date']);
            $data['ftd_owner'] = !isset($data['financial_status']['ftd_owner'])? NULL: $data['financial_status']['ftd_owner'];
            $data['secondary_income'] = !isset($data['financial_status']['secondary_income'])? NULL: $data['financial_status']['secondary_income'];
            $data['secondary_income_specify'] = !isset($data['financial_status']['secondary_income_specify'])? NULL: $data['financial_status']['secondary_income_specify'];
            $data['secondary_source_of_income'] = !isset($data['financial_status']['secondary_source_of_income'])? NULL: $data['financial_status']['secondary_source_of_income'];
            $data['anticipated_account_turnover_annually'] = !isset($data['financial_status']['anticipated_account_turnover_annually'])? NULL: $data['financial_status']['anticipated_account_turnover_annually'];
            $data['secondary_source_of_income_specify'] = !isset($data['financial_status']['secondary_source_of_income_specify'])? NULL: $data['financial_status']['secondary_source_of_income_specify'];
            $data['fund_method_country'] = !isset($data['financial_status']['fund_method_country'])? NULL: $data['financial_status']['fund_method_country'];
        }

        if (isset($data['documents_information'])) {
            $data['document_verified'] = !isset($data['documents_information']['document_verified'])? fieldOptionNameToId("Not Approved", 2): $data['documents_information']['document_verified'];
            $data['proof_of_residence'] = !isset($data['documents_information']['proof_of_residence'])?  fieldOptionNameToId("Not Approved", 2): $data['documents_information']['proof_of_residence'];
            $data['proof_of_identity'] = !isset($data['documents_information']['proof_of_identity'])?  fieldOptionNameToId("Not Approved", 2): $data['documents_information']['proof_of_identity'];
            $data['document_status'] = !isset($data['documents_information']['document_status'])?  fieldOptionNameToId("Not Approved", 2): $data['documents_information']['document_status'];
        }

        $account->fill($account->getOnlyFillables($data));

        if ($request->has('promo_code')) {
            $PromoCode = PromoCode::where('promo_code', $request->promo_code)->first();
            $oldData = $PromoCode->getOriginal();
            $account->assigned_to = $PromoCode->user_id;
            $PromoCode->update(['status' => 'Active']);
            $this->addLog(LogTypeEnum::Info, null, $oldData, $PromoCode, LogAction::Updated, ModuleEnum::PromoCodes);
            event(new RegisterationWithPromoCode($PromoCode));
        }

        $account->save();
        $this->addLog(LogTypeEnum::Info, null, $account, $account, LogAction::Created, ModuleEnum::Accounts);
        $data['account_id'] = $account->id;

        if ($request->has('personal_information')) {
            $newPersonalInformation = new AccountPersonalInformation($data['personal_information']);
            $account->saveAccountPersonalDetails($newPersonalInformation);
            $this->addLog(LogTypeEnum::Info, null, $newPersonalInformation, $newPersonalInformation, LogAction::Created, ModuleEnum::AccountPersonalInformation);

        } else {
            $accountPersonalInformation->fill($data);
            $accountPersonalInformation->save();
            $this->addLog(LogTypeEnum::Info, null, $accountPersonalInformation, $accountPersonalInformation, LogAction::Created, ModuleEnum::AccountPersonalInformation);
        }
        if ($request->has('trading_experience')) {
            $newAccountTradingExperience = new AccountTradingExperience($data['trading_experience']);
            $account->saveAccountTradingExperience($newAccountTradingExperience);
            $this->addLog(LogTypeEnum::Info, null, $newAccountTradingExperience, $newAccountTradingExperience, LogAction::Created, ModuleEnum::AccountTradingExperience);

        } else {
            $accountTradingExperience->fill($data);
            $accountTradingExperience->save();
            $this->addLog(LogTypeEnum::Info, null, $accountTradingExperience, $accountTradingExperience, LogAction::Created, ModuleEnum::AccountTradingExperience);
        }
        if ($request->has('contact_information_details')) {
            $newContactInformation = new AccountContactInformation($data['contact_information_details']);
            $account->saveAccountContactDetails($newContactInformation);
            $this->addLog(LogTypeEnum::Info, null, $newContactInformation, $newContactInformation, LogAction::Created, ModuleEnum::AccountContactInformation);

        } else {
            $accountContactDetail->fill($data);
            $accountContactDetail->save();
            $this->addLog(LogTypeEnum::Info, null, $accountContactDetail, $accountContactDetail, LogAction::Created, ModuleEnum::AccountContactInformation);

        }

        if ($request->has('legal_information')) {
            $accountLegalInformation = new AccountLegalInformation($data['legal_information']);
            $account->saveAccountLegalInformation($accountLegalInformation);
            $this->addLog(LogTypeEnum::Info, null, $accountLegalInformation, $accountLegalInformation, LogAction::Created, ModuleEnum::AccountLegalInformation);

        } else {
            $accountLegelInformation->fill($data);
            $accountLegelInformation->save();
            $this->addLog(LogTypeEnum::Info, null, $accountLegelInformation, $accountLegelInformation, LogAction::Created, ModuleEnum::AccountLegalInformation);

        }
        if ($request->has('lead_conversion')) {
            $newAccountLeadConversion = new AccountLeadConversion($data['lead_conversion']);
            $account->saveAccountLeadConversion($newAccountLeadConversion);
            $this->addLog(LogTypeEnum::Info, null, $newAccountLeadConversion, $newAccountLeadConversion, LogAction::Created, ModuleEnum::AccountLeadConversion);

        } else {
            $accountLeadConversion->fill($data);
            $accountLeadConversion->save();
            $this->addLog(LogTypeEnum::Info, null, $accountLeadConversion, $accountLeadConversion, LogAction::Created, ModuleEnum::AccountLeadConversion);

        }
        if ($request->has('account_marketing_details')) {
            $accountMarketingDetails = new AccountMarketingDetail($data['account_marketing_details']);
            $account->saveAccountMarketingDetails($accountMarketingDetails);
            $this->addLog(LogTypeEnum::Info, null, $accountMarketingDetails, $accountMarketingDetails, LogAction::Created, ModuleEnum::AccountMarketingDetail);
        } else {
            $accountMarketingDetail->fill($data);
            $accountMarketingDetail->save();
            $this->addLog(LogTypeEnum::Info, null, $accountMarketingDetail, $accountMarketingDetail, LogAction::Created, ModuleEnum::AccountMarketingDetail);
        }

        return $this->created(['message' => AccountsController::ACCOUNT_CREATED]);
    }

    public function index(Request $request)
    {
        $allAccounts = $this->model->accountsFilters($request)
            ->with(['user','assignedTo', 'accountPersonalInformation', 'accountContactInformation', 'tradingAccounts', 'accountTradingExperience', 'accountMarketingDetails', 'accountLegalInformation', 'accountLeadConversion', 'brand'])
            ->where('deleted_at', null)
            ->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
        return $this->created(['accounts' => $allAccounts, 'count' => $allAccounts->count()]);
    }


    public function accountMassDelete(AccountMassDeleteRequest $request)
    {
        try {
            $data = $request->all();
            $accountIds = $data['accounts_id'];
            $this->massDelete($accountIds);
            return $this->created(['message' => AccountsController::ACCOUNTS_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function changeAccountPassword(PersonChangePasswordRequest $request, int $accountId)
    {
        try {
            $data = $request->all();
            $account = $this->model->where('id', $accountId)->first();
            if (!$account) {
                return $this->noRecord(['message' => AccountsController::ACCOUNT_NOT_FOUND]);
            }
            if ($account->user->setPassword($data["password"], $this->userId(), true)) {
                return $this->created(["message" => PeopleController::RESET_SUCCESS]);
            } else {
                return $this->failed(["error" => PeopleController::RESET_ERROR]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function massDelete(array $accountIds)
    {
        $this->model->whereIn('id', $accountIds)->delete();
        return Comment::whereIn('module_id', $accountIds)->delete();
    }

    public function accountContactInformation(int $id)
    {
        return AccountContactInformation::where('account_id', $id)->first();
    }

    public function accountPersonalInformation(int $id)
    {
        return $this->accountPersonalInformationModel->where('account_id', $id)->first();
    }

    public function accountMarketingDetails(int $id)
    {
        return $this->accountMarketingDetailsModel->where('account_id', $id)->first();
    }

    public function accountLegalInformation(int $id)
    {
        return $this->accountLegalInformationModel->where('account_id', $id)->first();
    }

    public function accountLeadConversion(int $id)
    {
        return $this->accountLeadConversionModel->where('account_id', $id)->first();
    }

    public function accountTradingExperience(int $id)
    {
        return $this->accountTradingExperienceModel->where('account_id', $id)->first();
    }

    public function accountTradingInformation(int $id)
    {
        return $this->accountTradingAccountModel->where('account_id', $id)->get();
    }

    public function destroy(int $id)
    {
        try {
            $account = $this->model->where('id', $id)->first();
            if (!$account) {
                return $this->noRecord(['messsage' => AccountsController::ACCOUNT_NOT_FOUND]);
            }
            $accountContactInformationModel = $this->accountContactInformation($account->id);
            $accountPersonalInformation = $this->accountPersonalInformation($account->id);
            $accountMarketingDetails = $this->accountMarketingDetails($account->id);
            $accountLegalInformation = $this->accountLegalInformation($account->id);
            $accountLeadConversion = $this->accountLeadConversion($account->id);
            $accountTradingExperience = $this->accountTradingExperience($account->id);
            if ($account->delete()) {
                $this->addLog(LogTypeEnum::Info, null, $account, null, LogAction::Deleted, ModuleEnum::Accounts);
                if ($accountContactInformationModel) {
                    $accountContactInformationModel->delete();
                    $this->addLog(LogTypeEnum::Info, null, $accountContactInformationModel, null, LogAction::Deleted, ModuleEnum::AccountContactInformation);
                }
                if ($accountPersonalInformation) {
                    $accountPersonalInformation->delete();
                    $this->addLog(LogTypeEnum::Info, null, $accountPersonalInformation, null, LogAction::Deleted, ModuleEnum::AccountPersonalInformation);

                }
                if ($accountMarketingDetails) {
                    $accountMarketingDetails->delete();
                    $this->addLog(LogTypeEnum::Info, null, $accountMarketingDetails, null, LogAction::Deleted, ModuleEnum::AccountMarketingDetail);
                }
                if ($accountLegalInformation) {
                    $accountLegalInformation->delete();
                    $this->addLog(LogTypeEnum::Info, null, $accountLegalInformation, null, LogAction::Deleted, ModuleEnum::AccountLegalInformation);
                }
                if ($accountLeadConversion) {
                    $accountLeadConversion->delete();
                    $this->addLog(LogTypeEnum::Info, null, $accountLeadConversion, null, LogAction::Deleted, ModuleEnum::AccountLeadConversion);

                }
                if ($accountTradingExperience) {
                    $accountTradingExperience->delete();
                    $this->addLog(LogTypeEnum::Info, null, $accountTradingExperience, null, LogAction::Deleted, ModuleEnum::AccountTradingExperience);
                }
                return $this->deleted(['messsage' => AccountsController::ACCOUNT_DELETED]);
            } else {
                return $this->failed(['message' => AccountsController::ACCOUNT_NOT_DELETED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    protected function preDelete(Person $model)
    {
        $model->accountContactInformation()->delete();
        $model->accountPersonalInformation()->delete();
        $model->accountPersonalInformation()->delete();
        return $model;
    }
    protected function postDelete(Person $model)
    {
        event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'delete'));
        return $model;
    }
    protected function preUpdate(PersonUpdateRequest $request, Person $model)
    {
        return $model;
    }
    protected function postUpdate(PersonUpdateRequest $request, Person $model)
    {
        event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'update'));
        event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'saved'));
        if ($request->has('account_contact_information')) {
            $model->accountContactInformation()->update($request->get('account_contact_information'));
        }
        if ($request->has('account_personal_information')) {
            $model->accountPersonalInformation()->update($request->get('account_personal_information'));
        }
        return $model;
    }

    public function accountUpdate(AccountUpdateRequest $request)
    {
        try {
            if (!($this->checkIfAccountDeleted($request->id))) {
                return $this->noRecord(['messsage' => AccountsController::ACCOUNT_NOT_FOUND]);
            }
            $data = $request->all();
            $accountId = $data['id'];
            $data['password'] = $request->has('password') ? Hash::make($request->password) : Hash::make('456');
            $account = $this->findById($accountId);
            $oldData = $account->getOriginal();
            $user = $account->user;
            $oldUserData = $user->getOriginal();
            $accountContactInformationModel = $this->accountContactInformation($accountId);
            $OldAccountContactInformationData = $accountContactInformationModel->getOriginal();
            $accountPersonalInformation = $this->accountPersonalInformation($accountId);
            $OldAccountPersonalInformationData = $accountPersonalInformation->getOriginal();
            $accountMarketingDetails = $this->accountMarketingDetails($accountId);
            $OldAccountMarketingDetailsData = $accountMarketingDetails->getOriginal();
            $accountLegalInformation = $this->accountLegalInformation($accountId);
            $OldAccountLegalInformationData = $accountLegalInformation->getOriginal();
            $accountLeadConversion = $this->accountLeadConversion($accountId);
            $OldAccountLeadConversionData = $accountLeadConversion->getOriginal();
            $accountTradingExperience = $this->accountTradingExperience($accountId);
            $OldAccountTradingExperienceData = $accountTradingExperience->getOriginal();
            if (isset($data['external_kyc'])) {
                $data['verification_status'] = !isset($data['external_kyc']['verification_status'])? "Not Verified": $data['external_kyc']['verification_status'];
                $data['provider_name'] = !isset($data['external_kyc']['provider_name'])? "No Provider": $data['external_kyc']['provider_name'];
                $data['addtional_information'] = !isset($data['external_kyc']['provider_name'])? "N/A": $data['external_kyc']['addtional_information'];
            }

            if (isset($data['description_details'])) {
                $data['description'] = !isset($data['description_details']['description'])? NULL: $data['description_details']['description'];
                $data['account_description'] = !isset($data['description_details']['account_description'])? NULL: $data['description_details']['account_description'];
            }
            if (isset($data['trading_Status'])) {
                $data['trading_status'] = !isset($data['trading_Status']['trading_status'])? fieldOptionNameToId("Self Traded", 137): $data['trading_Status']['trading_status'];
                $data['primary_tp_account'] = !isset($data['trading_Status']['primary_tp_account'])? NULL: $data['trading_Status']['primary_tp_account'];
                $data['account_enabled'] = !isset($data['trading_Status']['account_enabled'])? false : (bool) $data['trading_Status']['account_enabled'];
            }

            if (isset($data['financial_status'])) {
                $data['ftd_amount'] = !isset($data['financial_status']['ftd_amount'])? 0.00 : $data['financial_status']['ftd_amount'];
                $data['ftd_status'] = !isset($data['financial_status']['ftd_status'])? false : (bool) $data['financial_status']['ftd_status'];
                $data['ftd_date'] =!isset($data['financial_status']['ftd_date'])? NULL: new Carbon($data['financial_status']['ftd_date']);
                $data['ftd_currency'] = !isset($data['financial_status']['ftd_currency'])? NULL: $data['financial_status']['ftd_currency'];
                $data['redeposit_status'] = !isset($data['financial_status']['redeposit_status'])? false: $data['financial_status']['redeposit_status'];
                $data['last_deposit_date'] = !isset($data['financial_status']['last_deposit_date'])? NULL: new Carbon($data['financial_status']['last_deposit_date']);
                $data['ftd_owner'] = !isset($data['financial_status']['ftd_owner'])? NULL: $data['financial_status']['ftd_owner'];
                $data['secondary_income'] = !isset($data['financial_status']['secondary_income'])? NULL: $data['financial_status']['secondary_income'];
                $data['secondary_income_specify'] = !isset($data['financial_status']['secondary_income_specify'])? NULL: $data['financial_status']['secondary_income_specify'];
                $data['secondary_source_of_income'] = !isset($data['financial_status']['secondary_source_of_income'])? NULL: $data['financial_status']['secondary_source_of_income'];
                $data['anticipated_account_turnover_annually'] = !isset($data['financial_status']['anticipated_account_turnover_annually'])? NULL: $data['financial_status']['anticipated_account_turnover_annually'];
                $data['secondary_source_of_income_specify'] = !isset($data['financial_status']['secondary_source_of_income_specify'])? NULL: $data['financial_status']['secondary_source_of_income_specify'];
                $data['fund_method_country'] = !isset($data['financial_status']['fund_method_country'])? NULL: $data['financial_status']['fund_method_country'];
            }

            if (isset($data['documents_information'])) {
                $data['document_verified'] = !isset($data['documents_information']['document_verified'])? fieldOptionNameToId("Not Approved", 2): $data['documents_information']['document_verified'];
                $data['proof_of_residence'] = !isset($data['documents_information']['proof_of_residence'])?  fieldOptionNameToId("Not Approved", 2): $data['documents_information']['proof_of_residence'];
                $data['proof_of_identity'] = !isset($data['documents_information']['proof_of_identity'])?  fieldOptionNameToId("Not Approved", 2): $data['documents_information']['proof_of_identity'];
                $data['document_status'] = !isset($data['documents_information']['document_status'])?  fieldOptionNameToId("Not Approved", 2): $data['documents_information']['document_status'];
            }
            if ($account->update($data)) {
                $this->addLog(LogTypeEnum::Info, null, $oldData, $account, LogAction::Updated, ModuleEnum::Accounts);
                if ($user) {
                    $UpdatedUserDetail =  $account->updateUserDetails($user, $data);
                    $this->addLog(LogTypeEnum::Info, null, $oldUserData, $UpdatedUserDetail, LogAction::Updated, ModuleEnum::Users);
                } else {
                    $user = new User($data);
                    $account->saveUserDetails($user);
                    $this->addLog(LogTypeEnum::Info, null, $user, $user, LogAction::Created, ModuleEnum::Users);
                }

                if ($accountContactInformationModel) {
                   $updatedAccountContactDetails = $account->updateAccountContactDetails($accountContactInformationModel, $data['contact_information_details']);
                   $this->addLog(LogTypeEnum::Info, null, $OldAccountContactInformationData, $updatedAccountContactDetails, LogAction::Updated, ModuleEnum::AccountContactInformation);

                } else {
                    $newContactInformation = new AccountContactInformation($data['contact_information_details']);
                    $account->saveAccountContactDetails($newContactInformation);
                    $this->addLog(LogTypeEnum::Info, null, $newContactInformation, $newContactInformation, LogAction::Created, ModuleEnum::AccountContactInformation);
                }
                if ($accountPersonalInformation) {
                    $updatedAccountPersonalDetails = $account->updateAccountPersonalDetails($accountPersonalInformation, $data['personal_information']);
                   $this->addLog(LogTypeEnum::Info, null, $OldAccountPersonalInformationData, $updatedAccountPersonalDetails, LogAction::Updated, ModuleEnum::AccountPersonalInformation);

                } else {
                    $newPersonalInformation = new AccountPersonalInformation($data['personal_information']);
                    $account->saveAccountPersonalDetails($newPersonalInformation);
                    $this->addLog(LogTypeEnum::Info, null, $newPersonalInformation, $newPersonalInformation, LogAction::Created, ModuleEnum::AccountPersonalInformation);
                }
                if ($accountMarketingDetails) {
                    $updatedAccountMarketingDetails = $account->updateAccountMarketingDetails($accountMarketingDetails, $data['account_marketing_details']);
                   $this->addLog(LogTypeEnum::Info, null, $OldAccountMarketingDetailsData, $updatedAccountMarketingDetails, LogAction::Updated, ModuleEnum::AccountMarketingDetail);

                } else {
                    $accountMarketingDetails = new AccountMarketingDetail($data['account_marketing_details']);
                    $account->saveAccountMarketingDetails($accountMarketingDetails);
                    $this->addLog(LogTypeEnum::Info, null, $accountMarketingDetails, $accountMarketingDetails, LogAction::Created, ModuleEnum::AccountMarketingDetail);
                }
                if ($accountLegalInformation) {
                    $updatedAccountLegalInformation = $account->updateAccountLegalInformation($accountLegalInformation, $data['legal_information']);
                   $this->addLog(LogTypeEnum::Info, null, $OldAccountLegalInformationData, $updatedAccountLegalInformation, LogAction::Updated, ModuleEnum::AccountLegalInformation);

                } else {
                    $accountLegalInformation = new AccountLegalInformation($data['legal_information']);
                    $account->saveAccountLegalInformation($accountLegalInformation);
                    $this->addLog(LogTypeEnum::Info, null, $accountLegalInformation, $accountLegalInformation, LogAction::Created, ModuleEnum::AccountLegalInformation);
                }
                if ($accountLeadConversion) {
                    $updatedAccountLeadConversion = $account->updateAccountLeadConversion($accountLeadConversion, $data['lead_conversion']);
                   $this->addLog(LogTypeEnum::Info, null, $OldAccountLeadConversionData, $updatedAccountLeadConversion, LogAction::Updated, ModuleEnum::AccountLeadConversion);

                } else {
                    $accountLeadConversion = new AccountLeadConversion($data['lead_conversion']);
                    $account->saveAccountLeadConversion($accountLeadConversion);
                    $this->addLog(LogTypeEnum::Info, null, $accountLeadConversion, $accountLeadConversion, LogAction::Created, ModuleEnum::AccountLeadConversion);

                }
                if ($accountTradingExperience) {
                    $updatedAccountTradingExperience = $account->updateAccountTradingExperience($accountTradingExperience, $data['trading_experience']);
                   $this->addLog(LogTypeEnum::Info, null, $OldAccountTradingExperienceData, $updatedAccountTradingExperience, LogAction::Updated, ModuleEnum::AccountTradingExperience);

                } else {
                    $accountTradingExperience = new AccountTradingExperience($data['trading_experience']);
                    $account->saveAccountTradingExperience($accountTradingExperience);
                    $this->addLog(LogTypeEnum::Info, null, $accountTradingExperience, $accountTradingExperience, LogAction::Created, ModuleEnum::AccountTradingExperience);
                }
                return $this->created(['message' => AccountsController::ACCOUNT_UPDATED]);
            } else {
                return $this->failed(['message' => AccountsController::ACCOUNT_NOT_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function performTransferOwnership(TransferOwnershipRequest $request)
    {
        try {
            $data = $request->all();
            $accountIds = $data['accounts_id'];
            $ownerId = $data['owner_id'];
            $this->transferOwnership($accountIds, $ownerId);
            return $this->created(['message' => AccountsController::ACCOUNT_OWNERSHIP_CHANGED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function transferOwnership(array $accountIds, int $ownerId)
    {
        $this->model->whereIn('id', $accountIds)->where('deleted_at', null)->update(['assigned_to' => $ownerId]);
    }

    public function checkIfAccountDeleted(int $accountId)
    {
        return $this->model->where('id', $accountId)->whereNull('deleted_at')->count();
    }
}
