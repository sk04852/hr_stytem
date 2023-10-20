<?php

namespace App\Http\Controllers\SMTPConnections\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class SMTPConnection extends BaseModel
{
    use SoftDeletes;
	protected $table = 'smtp_connections';
	public static $snakeAttributes = false;

	protected $fillable = [
            'company_id',
            'title',
            'server_name',
            'mail_host',
            'port',
            'user_name',
            'password',
            'from_email',
            'requires_authentication',
            'status'
	];

}
