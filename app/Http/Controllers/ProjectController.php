<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Project;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use DateTime;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::find(Auth::user()->id);
        $userProjects = $user->projects;
        return view('dashboard.project.all', compact('userProjects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.project.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [
            'project_name.required' => 'The Project Name Field is Required',
            'project_description.required' => 'The Project Description Field is Required',
            'project_description.max' => 'The Project Description Field Cannot be more than 2000 Characters',
            'project_start.required' => 'The Project Start Date Field is Required',
            'project_due.required' => 'The Project End Date Field is Required',
        ];

        $validator = $this->validate($request, [
            'project_name' => 'required|string',
            'project_description' => 'required|max:2000|string',
            'project_start' => 'required',
            'project_due' => 'required',
            'project_cover_image' => 'image|nullable|max:1999'
        ], $messages);

        if($validator){

            do {
                $uuid = Str::orderedUuid();
                $code_avail = Project::where('uuid', $uuid)->first();
            } while (!empty($code_avail));

            try{
                if($request->hasFile('project_cover_image')){
                    $fileNameWithExt = $request->file('project_cover_image')->getClientOriginalName();

                    $fileName = str_slug(trim(strtolower(pathinfo($fileNameWithExt, PATHINFO_FILENAME))), '_');

                    $fileExt = $request->file('project_cover_image')->getClientOriginalExtension();

                    $fileNameToStore = $fileName."_".time().".".$fileExt;

                    $path = $request->file('project_cover_image')->storeAs('public/project_cover', $fileNameToStore);
                }
                else {
                    $fileNameToStore = "NULL";
                }
                // Create Project 
                $project = new Project;
                $project->uuid = $uuid;
                $project->user_id = Auth::user()->id;
                $project->name = $request->project_name;
                $project->description = $request->project_description;
                $project->start_date = $request->project_start;
                $project->end_date = $request->project_due;
                $project->display_image = $fileNameToStore;

                // Save Project
                $saveProject = $project->save();
                if($saveProject){
                    $output = [
                        "status" => 200,
                        "response" => [
                            "message" => "Project Created Successfully",
                            "redirectTo" => "$project->uuid",
                        ]
                    ];
                }
                else {
                    $output = [
                        "status" => 404,
                        "message" => "Could Not Create Project",
                    ];
                }
                return response()->json($output);
            }
            catch (\Exception $e) {
                return $e;
            }
        }
        else {
            return redirect()->back()->withErrors($validator)->withInput();            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        if(!empty($uuid)){
            $user_id = Auth::user()->id;
            $project = Project::where('uuid', $uuid)->get()[0];
            if(Auth::user()->id == $project->user_id){
                $tasks = Project::find($project->id)->tasks;
                $allTask = count($tasks);
                $completedTask = Task::where(['project_id' => $project->id, 'user_id' => $user_id, 'status' => '1'])->count();
                
                $dt1 = new DateTime(dateFormatter($project->start_date));
                $dt2 = new DateTime(dateFormatter($project->end_date));
                $interval = $dt1->diff($dt2);

                // if($allTask !== 0){
                    $projectDue = $interval->format('%a');
                // }
                // else {
                //     $projectDue = 'Task-Not-Added';
                // }

                return view('dashboard.project.index', compact(['project', 'tasks', 'allTask', 'completedTask', 'projectDue']));
            }
            else {
                return redirect('dashboard');
            }
        }
        else {
            return redirect('dashboard');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        //
    }
}
