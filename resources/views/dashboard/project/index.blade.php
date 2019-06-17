@extends('layouts.dashboard.main')

@section('title', $project->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('projects') }}">Projects</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $project->name }}</li>
@endsection

@section('page-content')
    <div class="page-header">
        <h2>{{ $project->name }} </h2>
        <p class="lead">{{ $project->description }}</p>

        <h5>Add Task to Project</h5>
        <div class="row content-list-head">
            <form id="addTaskForm" action="{{ route('task.save') }}" method="POST" class="col-md-12">
                <div class="input-group col-xs-12">
                    @csrf
                    <input type="hidden" name="__project_uuid" value="{{ $project->uuid }}">
                    <input type="text" required placeholder="Task Name" name="task_name" class="form-control input-regular">
                    <span class="input-group-btn">
                        <button class="btn btn-primary input-lg input-regular" id="addTaskBtn" type="submit">Add Task</button>
                    </span>
                </div>
            </form>
        </div>
        <div>
            @php
            
                if($completedTask == 0 && $allTask == 0){
                    $progress = 0;
                    $color = 'danger';
                    $completionRate = '-/-';
                }
                else
                {
                    // $color = ($allTask == $completedTask)?'success':'warning';
                    if($allTask == $completedTask){
                        $color = 'success';
                    }
                    else if (($allTask - $completedTask) > $completedTask) {
                        $color = 'danger';
                    }
                    else {
                        $color = 'warning';
                    }
                    $progress = ceil(($completedTask/$allTask)*100);
                    $completionRate = $completedTask ."/". $allTask;
                }

                $dt1 = new DateTime(dateFormatter($project->start_date));
                if($allTask == $completedTask){
                    if($allTask == 0){
                        $projectDue = "No Task(s) Found";
                    }
                    else {
                        $dt2 = new DateTime($project->update_at);
                        $interval = $dt1->diff($dt2);

                        $projectDue = $interval->format('%a');

                        $projectDue = "Completed in $projectDue day(s)";
                    }
                }
                else {
                    $dt2 = new DateTime(dateFormatter($project->end_date));
                    $dt3 = new DateTime(date('d-m-Y'));
                    if(strtotime(dateFormatter($project->end_date)) > strtotime(date('d-m-Y'))){
                        $interval = $dt1->diff($dt3);
                        $newProjectDue = $interval->format('%a');

                        $realProjectDueDate = $projectDue - $newProjectDue;
                        $projectDue = "Project is due in $realProjectDueDate day(s)";
                    }
                    else if(strtotime(dateFormatter($project->end_date)) == strtotime(date('d-m-Y'))){
                        $projectDue = "Project will elapse today";
                    }
                    else {
                        $interval = $dt2->diff($dt3);
                        $newProjectDue = $interval->format('%a');

                        $projectDue = "Project elapsed $newProjectDue day(s) ago";
                    }   
                }
            @endphp
            <div class="progress">
                <div class="progress-bar bg-{{$color}}" style="width:{{ $progress }}%;"></div>
            </div>
            <div class="d-flex justify-content-between text-small">
                <div class="d-flex align-items-center">
                    <i class="material-icons">playlist_add_check</i>
                    <span>{{ $completionRate }}</span>
                </div>
                <span>
                    {{ $projectDue }}
                </span>
            </div>
        </div>
    </div>

    <div class="tab-pane fade show active" id="task" role="tabpanel" aria-labelledby="task-tab">
        <div class="content-list" data-filter-list="checklist">
            <div class="row content-list-head">
                <div class="col-auto">
                    <h3>Project Task List</h3>
                </div>
                @if (count($tasks) > 0)                    
                    <form class="col-md-auto">
                        <div class="input-group input-group-round">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="material-icons">filter_list</i>
                                </span>
                            </div>
                            <input type="search" class="form-control filter-list-input" placeholder="Filter Tasklist" aria-label="Filter Tasklist" aria-describedby="filter-checklist">
                        </div>
                    </form>
                </div>
                <div class="content-list-body">
                    <form class="checklist">
                        @foreach ($tasks as $task)
                            <div class="row">
                                <div class="form-group col">
                                    <span class="checklist-reorder">
                                        <i class="material-icons">reorder</i>
                                    </span>
                                    <div class="custom-control custom-checkbox col">
                                        <input type="checkbox" class="custom-control-input updateTaskChecker" data-task-id="{{$task->uuid}}" id="checklist-item-{{$task->id}}" {{ ($task->status == 0) ? '' : 'checked' }}>
                                        <label class="custom-control-label" for="checklist-item-{{$task->id}}"></label>
                                        <div>
                                            <input type="text" placeholder="Checklist item" value="{{ $task->name }}" data-filter-by="value" />
                                            <div class="checklist-strikethrough"></div>
                                        </div>
                                    </div>
                                    <a href="#" class="deleteTaskChecker" data-task-id="{{$task->uuid}}" id="delete-item-{{$task->id}}">
                                        <i class="material-icons text-danger">delete</i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </form>
                    <div class="drop-to-delete">
                        <div class="drag-to-delete-title">
                            <i class="material-icons">delete</i>
                        </div>
                    </div>
                </div>
                @else
                    </div>
                    <div class="col-md-12 text-center">
                        <p class="lead">No Task Added &#x1f635;</p>
                    </div>
                @endif
        </div>
    </div>
@endsection

@section('runJavascript')
    <script type="text/javascript">
        // Add Task to Project
        $('form').submit(function(event){
            event.preventDefault();

            formAction = $(this).attr('action');
            formID = $(this).attr('id');
            formMethod = $(this).attr('method');
            btnHandler = $("button[type=submit]", this).attr('id');
            dataToSend = $(this).serialize();

            var promise = postData(formAction, dataToSend, btnHandler);

            promise.then(data => {
                console.log(data)
                if(data.status == 200){
                    notify("success", data.response.message);
                    setTimeout(() => {
                        redirectWindow("");
                    }, 3000);
                }
                else if(data.status == 401){
                    notify("error", "An Error Occurred", data.response.message);
                    errorResetForm(formID, btnHandler);
                }
                else
                {
                    notify("error", "An Error Occurred", data.response);  
                    errorResetForm(formID, btnHandler);
                }
            }, 
            error => {
                errorResponse = error.responseJSON.errors;
                console.log(error);
                
                if("task_name" in errorResponse){                        
                    notify("warning", "Error", errorResponse.task_name);
                }

                errorResetForm(formID, btnHandler);
            });
        });

        // Update Task Status
        $('.updateTaskChecker').change(function(event){
            taskStatus = ( $(this)[0].checked ) ? 1 : 0;
            taskID = $(this).data('task-id');
            btnHandler = $(this).attr('id');
            csrfToken = $('meta[name=csrf-token]').attr('content');
            dataToSend = {'__taskStatus' : taskStatus, '__taskID' : taskID, '_method' : 'PUT', '_token' : csrfToken}
            formAction = "{{ route('task.update') }}";

            var promise = postData(formAction, dataToSend, btnHandler);

            promise.then(data => {
                console.log(data)
                if(data.status == 200){
                    notify("success", data.response.message);
                    $('#'+btnHandler+'').prop('disabled', false)
                    setTimeout(() => {
                        redirectWindow("");
                    }, 3000);
                }
                else
                {
                    notify("error", "An Error Occurred", data.response);  
                    $('#'+btnHandler+'').prop('disabled', false)
                    $('#'+btnHandler+'').prop('checked', !taskStatus);
                }
            }, 
            error => {
                errorResponse = error.responseJSON.errors;
                console.log(error);
                
                if("__taskID" in errorResponse){                        
                    notify("warning", "Error", errorResponse.__taskID);
                }
                else if("__taskStatus" in errorResponse){
                    notify("warning", "Error", errorResponse.__taskStatus);
                }

                $('#'+btnHandler+'').prop('disabled', false)
                $('#'+btnHandler+'').prop('checked', !taskStatus);
            });
        });

        // Delete Task
        $('.deleteTaskChecker').click(function(event){
            taskID = $(this).data('task-id');
            btnHandler = $(this).attr('id');
            csrfToken = $('meta[name=csrf-token]').attr('content');
            dataToSend = {'__taskID' : taskID, '_method' : 'DELETE', '_token' : csrfToken}
            formAction = "{{ route('task.delete') }}";

            var promise = postData(formAction, dataToSend, btnHandler);

            promise.then(data => {
                console.log(data)
                if(data.status == 200){
                    notify("success", data.response.message);
                    setTimeout(() => {
                        redirectWindow("");
                    }, 3000);
                }
                else
                {
                    notify("error", "An Error Occurred", data.response);  
                    $('#'+btnHandler+'').prop('disabled', false)
                    $('#'+btnHandler+'').prop('checked', !taskStatus);
                }
            }, 
            error => {
                errorResponse = error.responseJSON.errors;
                console.log(error);
                
                if("__taskID" in errorResponse){                        
                    notify("warning", "Error", errorResponse.__taskID);
                }

                $('#'+btnHandler+'').prop('disabled', false)
                $('#'+btnHandler+'').prop('checked', !taskStatus);
            });
        });
    </script>
@endsection