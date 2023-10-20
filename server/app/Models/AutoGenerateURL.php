<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoGenerateURL extends Model
{
	protected $table = 'auto_generate_urls';

	public $timestamps = true;

	public static $snakeAttributes = false;

    protected $fillable = [
        'id',
        'model_type',
        'model_id',
        'token',
        'status',
        'user_pr_id',
        'company_contact_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Get the parent url model (post or video).
     */
    public function model()
    {
        return $this->morphTo();
    }
}
