<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\UserPr;
use App\Traits\ZoomJWT;

class CreateZoomUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ZoomJWT;

    /**
     * @var UserPr
     */
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UserPr $user)
    {
        $this->user = $user->withoutRelations();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $path = '/users';
        $response = $this->zoomPost($path, [
            'action' => 'create',
            'user_info' => [
                'email' => $this->user->email,
                'type' => 1
            ]
        ]);

        if($response->status() == 201){
            $zoom_data = json_decode($response->body(), true);
//            UserPr::where('id', 27)->update(['zoom_id' => 'hi']);
            $this->user->zoom_id = $zoom_data['id'];
            $this->user->save();
        }
    }
}
