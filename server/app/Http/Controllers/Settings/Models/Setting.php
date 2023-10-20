<?php namespace App\Http\Controllers\Settings\Models;
use Illuminate\Database\Eloquent\Model as Model;
class Setting extends Model {

    protected $table = 'settings';
	protected $fillable=[
                            "id",
                            "name",
                            "value",
                        ];

	protected $softDelete = false;
	protected $primaryKey = 'id';
}
