<?php

namespace App\Http\Controllers\Tasks\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';
    public static $snakeAttributes = false;
    public $timestamps = false;

    protected $fillable = [
        'Task-Id',
        'NAME',
    ];

    public function tasksFilter($request){

        return Task::when(!empty($request->TaskId), function ($query) use ($request) {
            return $query->where('Task-Id', '=', $request->TaskId);
        })
        ->when(!empty($request->nameD), function ($query) use ($request) {
            $query->where('NAME', $request->nameD);
        });
    }

}
