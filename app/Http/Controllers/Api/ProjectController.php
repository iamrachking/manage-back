<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectCollection;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->get();
        
        if ($projects) {
            return $this->success(ProjectCollection::collection($projects));
        }
        
        return $this->fail();
    }
    
    public function show(Project $project)
    {
        if ($project->exists()) {
            return $this->success(new ProjectCollection($project));
        }
        
        return $this->fail();
    }
}