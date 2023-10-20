<?php

namespace App\Http\Controllers\People\Models;

use DateTime;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\People\Interfaces\IPerson;
use App\Traits\GetOnlyFillablesTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\ModelDefaultTraits;

class Person extends Authenticatable implements JWTSubject, IPerson
{
    use Notifiable;
    use SoftDeletes;
    use ModelDefaultTraits;
    use GetOnlyFillablesTrait;

    public function setAssignedId(int $userId)
    {
        $this->assigned_id = $userId;
    }

    public function setPasswordRecoveryToken($token)
    {
        $this->password_recovery_token_created_at = new DateTime();
        $this->password_recovery_token = $token;
        $this->save();
    }

    public function setEmailVerificationToken($token)
    {
        $this->email_verification_token_created_at = new DateTime();
        $this->email_verification_token = $token;
        $this->save();
    }

    public function setEmailVerified()
    {
        $this->email_verified = true;
        $this->password_recovery_token_created_at = null;
        $this->password_recovery_token = null;
        return $this->save();
    }

    public function setMemberEmailVerified()
    {
        $this->email_verified = true;
        $this->password_recovery_token_created_at = null;
        $this->password_recovery_token = null;
        $this->email_verification_token_created_at = null;
        $this->email_verification_token = null;
        $this->email_verified_at = new DateTime();
        return $this->save();
    }

    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function setPassword(string $password, int $changed_by = null)
    {
        $this->password = Hash::make($password);
        $this->password_recovery_token_created_at = null;
        $this->password_recovery_token = null;
        $this->password_last_changed_at = new DateTime();
        if ($changed_by != null) {
            $this->password_last_changed_by = $changed_by;
        }
        return $this->save();
    }

    public function toDigest()
    {
        return $this->select(['id', 'first_name', 'last_name'])->get();
    }

    public function scopeFilter($query, array $filters) {
        if(array_key_exists('assigned_to', $filters)) {
            $query->where('assigned_to',$filters['assigned_to']);
        }

        if(array_key_exists('email', $filters)) {
            $query->where('email', 'like', '%'.$filters['email'].'%');
        }

        if(array_key_exists('first_name', $filters)) {
            $query->where('first_name', 'like', '%'.$filters['first_name'].'%');
        }

        if(array_key_exists('last_name', $filters)) {
            $query->where('last_name', 'like', '%'.$filters['last_name'].'%');
        }

        if(array_key_exists('username', $filters)) {
            $query->where('username', 'like', '%'.$filters['username'].'%');
        }

        if(array_key_exists('city', $filters)) {
            $query->where('city', $filters['city']);
        }

        if(array_key_exists('state', $filters)) {
            $query->where('state', $filters['state']);
        }

        if(array_key_exists('status', $filters)) {
            $query->where('status', $filters['status']);
        }

        if(array_key_exists('account_status', $filters)) {
            $query->where('account_status', $filters['account_status']);
        }

        if(array_key_exists('registration_country', $filters)) {
            $query->where('registration_country', $filters['registration_country']);
        }

        if(array_key_exists('ib_id', $filters)) {
            $query->where('ib_id', $filters['ib_id']);
        }

        if(array_key_exists('client_type', $filters)) {
            $query->where('client_type', $filters['client_type']);
        }
    }

    public static function getClass()
    {
        return get_called_class();
    }

    public static function getDefiningClass()
    {
        return get_class();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
