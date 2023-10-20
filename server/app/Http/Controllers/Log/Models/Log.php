<?php namespace App\Http\Controllers\Log\Models;

use Illuminate\Database\Eloquent\Model as Model;
class Log extends Model {
	protected $table = 'log';
	protected $fillable=[
                            "id",
                            "message",
                            "type"
	];
	protected $softDelete = false;
	protected $primaryKey = 'id';
}
