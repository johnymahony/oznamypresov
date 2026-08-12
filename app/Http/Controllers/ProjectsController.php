<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    public function index()
    {
        $projects = Project::whereNull('phase_id')->orWhere('phase_id', '<', 8)->get();

        return view('projects.list', ['projects' => $projects]);
    }

    public function detail(Project $project)
    {
        return view('projects.detail', ['project' => $project]);
    }
}
