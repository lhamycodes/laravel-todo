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

class TaskController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [
            'task_name.required' => 'The Task Name is Required',
        ];

        $validator = $this->validate($request, [
            'task_name' => 'required|string',
        ], $messages);

        $project = Project::where(['uuid' => $request->__project_uuid, 'user_id' => Auth::user()->id])->first();
        if($project !== null){
            if($validator){
                $projectId = $project->id;
                do {
                    $uuid = Str::orderedUuid();
                    $code_avail = Task::where('uuid', $uuid)->first();
                } while (!empty($code_avail));

                try{
                    // Create task 
                    $task = new Task;
                    $task->uuid = $uuid;
                    $task->user_id = Auth::user()->id;
                    $task->project_id = $projectId;
                    $task->name = $request->task_name;

                    // Save Task
                    $saveTask = $task->save();
                    if($saveTask){
                        $output = [
                            "status" => 200,
                            "response" => [
                                "message" => "Task Successfully Added",
                            ]
                        ];
                    }
                    else {
                        $output = [
                            "status" => 404,
                            "response" => "Could Not Add task to Project",
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
        else {
            $output = [
                "status" => 401,
                "response" => [
                    "message" => "Cannot add Task to inexistent Project",
                    "redirectTo" => "",
                ]
            ];
            return response()->json($output);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $messages = [
            '__taskStatus.required' => 'The Task Status is Required',
            '__taskID.required' => 'The Task UUID is Required',
        ];

        $validator = $this->validate($request, [
            '__taskStatus' => 'required',
            '__taskID' => 'required',
        ], $messages);

        if($validator){
            // Update task
            // $task = Task::find($request->__taskID);
            $task = Task::where(['uuid' => $request->__taskID, 'user_id' => Auth::user()->id ])->first();
            $task->status = $request->__taskStatus;
            
            // Save Task
            $updateTask = $task->save();
            
            $taskProjectId = $task->project_id;
            if($updateTask){
                $completed = Task::where(['user_id' => Auth::user()->id, 'project_id' => $taskProjectId, 'status' => '1'])->count();
                $allTask = Task::where(['user_id' => Auth::user()->id, 'project_id' => $taskProjectId])->count();
                
                $updateProject = Project::find($taskProjectId);

                if($completed == 0){
                    $updateProject->status = '0';
                }
                else if($completed > 0){
                    if($completed !== $allTask) {
                        $updateProject->status = '1';
                    }
                    else {   
                        $updateProject->status = '2';
                    }
                }

                $updateProject->save();

                $output = [
                    "status" => 200,
                    "response" => [
                        "message" => "Task Successfully Updated",
                    ]
                ];
            }
            else {
                $output = [
                    "status" => 404,
                    "response" => "Could Not Update task to Project",
                ];
            }
            return response()->json($output);
        }
        else {
            return redirect()->back()->withErrors($validator)->withInput();            
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Task $task)
    {
        $task = Task::where(['uuid' => $request->__taskID, 'user_id' => Auth::user()->id ])->first();

        // Delete Task
        $deleteTask = $task->delete();
        if($deleteTask){
            $output = [
                "status" => 200,
                "response" => [
                    "message" => "Task Successfully Deleted",
                ]
            ];
        }
        else {
            $output = [
                "status" => 404,
                "response" => "Could Not Delete task from Project",
            ];
        }
        return response()->json($output);
    }
}
