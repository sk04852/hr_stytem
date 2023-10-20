<?php namespace App\Http\Controllers\Deposits\Models;
use Illuminate\Database\Eloquent\Model as Model;

class Deposit extends Model {

    protected $table = 'deposits';
	protected $fillable=[
                            "id",
                            "type",
                            "client_id",
                            "method_id",
                            "amount",
                            "bonus",
                            "total",
                        ];

	protected $softDelete = false;
    protected $primaryKey = 'id';
    
    public function client()
    {
        return $this->hasOne('App\Http\Controllers\Clients\Models\Client');
    }
}
