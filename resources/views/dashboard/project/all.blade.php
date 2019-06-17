@extends('layouts.dashboard.main')

@section('title', 'Project')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Projects</li>
@endsection

@section('page-content')
    <div class="page-header">
        <div class="row content-list-head">
            <div class="col-auto">
                
            </div>
        </div>
    </div>
    <div class="tab-pane" id="projects" role="tabpanel" aria-labelledby="tasks-tab" data-filter-list="content-list-body">
        <div class="content-list">
            <div class="row content-list-head">
                <div class="col-auto">
                    <h5>Create New Project</h5>
                    <a class="btn btn-round btn-primary" href="{{ route('projects.create') }}">
                        <i class="material-icons">add</i>
                    </a>
                </div>
                <form class="col-md-auto">
                    <div class="input-group input-group-round">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="material-icons">filter_list</i>
                            </span>
                        </div>
                        <input type="search" class="form-control filter-list-input" placeholder="Filter Projects" aria-label="Filter Projects" aria-describedby="filter-projects">
                    </div>
                </form>
            </div>
            <div class="content-list-body row">
                @php
                    use App\Project;
                    use App\Task;
                @endphp
                @if(count($userProjects) > 0)
                    @foreach ($userProjects as $project)
                        
                        @php
                            $dt1 = new DateTime(dateFormatter($project->start_date));
                            $dt2 = new DateTime(dateFormatter($project->end_date));
                            
                            $tasks = Project::find($project->id)->tasks;
                            $allTask = count($tasks);
                            $completedTask = Task::where(['project_id' => $project->id, 'user_id' => Auth::user()->id, 'status' => '1'])->count();
                            if($completedTask == 0 && $allTask == 0){
                                $progress = 0;
                                $color = 'danger';
                                $completionRate = '-/-';
                            }
                            else
                            {
                                $color = ($allTask == $completedTask)?'success':'warning';
                                if($allTask == $completedTask){
                                    $dt2 = new DateTime($project->update_at);
                                    $color = 'success';
                                }
                                elseif (($allTask - $completedTask) > $completedTask) {
                                    $color = 'danger';
                                }
                                else {
                                    $color = 'warning';
                                }
                                $progress = ceil(($completedTask/$allTask)*100);
                                $completionRate = $completedTask ."/". $allTask;
                            }
                            $interval = $dt1->diff($dt2);

                            $projectDue = $interval->format('%a');
                            if($allTask == $completedTask){
                                if($allTask == 0){
                                    $projectDue = "No Task(s) Found";
                                }
                                else {
                                    $projectDue = "Completed in $projectDue day(s)";                                
                                }
                            }
                            else {
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
                        <div class="col-lg-6">
                            <div class="card card-project">
                                <div class="progress">
                                    <div class="progress-bar {{ 'bg-'.$color }}" role="progressbar" style="width: {{ $progress }}%" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="card-body">
                                    <div class="dropdown card-options">
                                        <button class="btn-options" type="button" id="project-dropdown-button-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="material-icons">more_vert</i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="{{ route("projects.edit", $project->uuid) }}">Edit</a>
                                            <a class="dropdown-item text-danger" href="{{ route("projects.delete", $project->uuid) }}">Delete</a>
                                        </div>
                                    </div>
                                    <div class="card-title">
                                        <a href="{{ route('projects.index', $project->uuid) }}">
                                            <h5 data-filter-by="text" class="text-primary">{{ $project->name }}</h5>
                                        </a>
                                    </div>
                                    <p data-filter-by="text">{{ substr($project->description, 0, 50) }}...</p>
                                    <div class="card-meta d-flex justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons mr-1">playlist_add_check</i>
                                            <span data-filter-by="text" class="text-small">{{ $completionRate }}</span>
                                        </div>
                                        <span class="text-small" data-filter-by="text">{{ $projectDue }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-md-12 text-center">
                        <p class="lead">No Project(s) Added &#x1f635;</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection