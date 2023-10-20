<?php namespace App\Http\Controllers\Tickets\Models;

use App\Models\BaseModel;
use App\Models\User;


class TicketReply extends BaseModel
{
	protected $table = 'ticket_replies';
	public static $snakeAttributes = false;


	protected $fillable = [
        'ticket_id',
        'user_id',
        'note'
    ];
    
    public $primaryKey  = 'id';


	public function user()
	{
		return $this->belongsTo(User::class, 'assigned_to');
	}
    
    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }

}
