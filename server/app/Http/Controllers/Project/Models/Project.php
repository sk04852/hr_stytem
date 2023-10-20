<?php

namespace App\Http\Controllers\Project\Models;

use App\Http\Controllers\AssignProjects\Models\AssignProject;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';
    public static $snakeAttributes = false;

    protected $fillable = [
        'company_id',
        'project_name',
        'description',
        'link',
        'status',
    ];

    public function projectsFilter($request){ 

        return Project::when(!empty($request->company_id), function ($query) use ($request) {
            return $query->where('company_id', '=', $request->company_id);
        })
        ->when(!empty($request->project_name), function ($query) use ($request) {
            $query->where('project_name', 'like', '%' . $request->project_name . '%');
        })
        ->when(!empty($request->description), function ($query) use ($request) {
        $query->where('description', 'like', '%' . $request->description . '%');
        })
        ->when(!empty($request->link), function ($query) use ($request) {
        $query->where('link', 'like', '%' . $request->link . '%');
        })
        ->when(!empty($request->status), function ($query) use ($request) {
        $query->where('status', 'like', '%' . $request->status . '%');
    });
}

    public function assignProjects() {
        return $this->hasMany(AssignProject::class);
    }
}
 