@extends('layouts.dashboard.main')

@section('title')
    Dashboard
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('page-content')
    <div class="page-header">
        <h1>Hello, {{ Auth::user()->fullname }} !</h1>
        <p class="lead">Plan, Complete and Track your Projects</p>
        <div class="row content-list-head">
            <div class="col-auto">
                <h5>Add Project</h5>
                <a class="btn btn-round btn-primary" href="{{ route('projects.create') }}">
                    <i class="material-icons">add</i>
                </a>
            </div>
        </div>
    </div>

    @php
        use App\Project;
        use App\Task;
        $completed = [];
        $ongoing = [];
        $backlog = [];
    @endphp
    @if(count($userProjects) > 0)
        @foreach ($userProjects as $project)
            @php
                if($project->status == 0)
                {
                    array_push($backlog, $project);
                }
                else if($project->status == 1) {
                    array_push($ongoing, $project);
                }
                else if($project->status == 2) {
                    array_push($completed, $project);
                }
            @endphp  
        @endforeach

        <ul class="nav nav-tabs nav-fill">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#completed" role="tab" aria-controls="completed" aria-selected="true">Completed : {{ count($completed) }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#ongoing" role="tab" aria-controls="ongoing" aria-selected="false">Ongoing : {{ count($ongoing) }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#backlog" role="tab" aria-controls="backlog" aria-selected="false">Backlog : {{ count($backlog) }}</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="completed" role="tabpanel" aria-labelledby="completed-tab" data-filter-list="completed-card-list-body">
                <div class="row content-list-head">
                    <div class="col-auto">
                        <h3>Completed Projects</h3>
                    </div>
                    <form class="col-md-auto">
                        <div class="input-group input-group-round">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="material-icons">filter_list</i>
                                </span>
                            </div>
                            <input type="search" class="form-control filter-list-input" placeholder="Filter Completed" aria-label="Filter Completed" aria-describedby="filter-tasks">
                        </div>
                    </form>
                </div>
                <div class="content-list-body">
                    <div class="card-list">
                        <div class="completed-card-list-body">
                            @if (count($completed) > 0)
                                @foreach ($completed as $project)
                                    @php
                                        $dt1 = new DateTime(dateFormatter($project->start_date));
                                        $dt2 = new DateTime($project->updated_at);
                                        $interval = $dt1->diff($dt2);
                            
                                        $projectDue = $interval->format('%a');
                            
                                        $tasks = Project::find($project->id)->tasks;
                                        $allTask = count($tasks);
                                        $completionRate = $allTask ."/". $allTask;
                                    @endphp
                                    <div class="card card-task">
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="card-body">
                                            <div class="card-title">
                                                <a href="{{ route('projects.index', $project->uuid) }}">
                                                    <h6 data-filter-by="text" class="text-primary">{{ $project->name }}</h6>
                                                </a>
                                                <span class="text-small">Project completed in {{ $projectDue }} days</span>
                                            </div>
                                            <div class="card-meta">
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons">playlist_add_check</i>
                                                    <span>{{ $completionRate }}</span>
                                                </div>
                                                <div class="dropdown card-options">
                                                    <button class="btn-options" type="button" id="task-dropdown-button-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="material-icons">more_vert</i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item text-danger" href="#">Archive Project</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-md-12 text-center">
                                    <p class="lead">You don't have any completed project &#x1f635;</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="ongoing" role="tabpanel" aria-labelledby="ongoing-tab" data-filter-list="ongoing-card-list-body">
                <div class="row content-list-head">
                    <div class="col-auto">
                        <h3>Ongoing Projects</h3>
                    </div>
                    <form class="col-md-auto">
                        <div class="input-group input-group-round">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="material-icons">filter_list</i>
                                </span>
                            </div>
                            <input type="search" class="form-control filter-list-input" placeholder="Filter Ongoing" aria-label="Filter Ongoing" aria-describedby="filter-tasks">
                        </div>
                    </form>
                </div>
                <div class="content-list-body">
                    <div class="card-list">
                        <div class="ongoing-card-list-body">
                            @if (count($ongoing) > 0)
                                @foreach ($ongoing as $project)
                                    @php
                                        $dt1 = new DateTime(dateFormatter($project->start_date));
                                        $dt2 = new DateTime(dateFormatter($project->end_date));
                                        $interval = $dt1->diff($dt2);

                                        $projectDue = $interval->format('%a');

                                        $tasks = Project::find($project->id)->tasks;
                                        $allTask = count($tasks);
                                        $completedTask = Task::where(['project_id' => $project->id, 'user_id' => Auth::user()->id, 'status' => '1'])->count();
                                        
                                        $color = ($allTask == $completedTask)?'success':'warning';
                                        if (($allTask - $completedTask) > $completedTask) {
                                            $color = 'danger';
                                        }
                                        else {
                                            $color = 'warning';
                                        }
                                        $progress = ceil(($completedTask/$allTask)*100);
                                        $completionRate = $completedTask ."/". $allTask;

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

                                    @endphp
                                    <div class="card card-task">
                                        <div class="progress">
                                            <div class="progress-bar {{ 'bg-'.$color }}" role="progressbar" style="width: {{ $progress }}%" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="card-body">
                                            <div class="card-title">
                                                {{-- <img src="{{ asset("/storage/project_cover/$project->display_image") }}" alt="Hello World"> --}}
                                                <a href="{{ route('projects.index', $project->uuid) }}">
                                                    <h6 data-filter-by="text" class="text-primary">{{ $project->name }}</h6>
                                                </a>
                                                <span class="text-small">{{ $projectDue }}</span>
                                            </div>
                                            <div class="card-meta">
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons">playlist_add_check</i>
                                                    <span>{{ $completionRate }}</span>
                                                </div>
                                                <div class="dropdown card-options">
                                                    <button class="btn-options" type="button" id="task-dropdown-button-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="material-icons">more_vert</i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item" href="#">Mark as Completed</a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger" href="#">Archive Project</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-md-12 text-center">
                                    <p class="lead">You don't have any Ongoing project &#x1f635;</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="backlog" role="tabpanel" aria-labelledby="backlog-tab" data-filter-list="backlog-card-list-body">
                <div class="content-list">
                    <div class="row content-list-head">
                        <div class="col-auto">
                            <h3>Backlog Projects</h3>
                        </div>
                        <form class="col-md-auto">
                            <div class="input-group input-group-round">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="material-icons">filter_list</i>
                                    </span>
                                </div>
                                <input type="search" class="form-control filter-list-input" placeholder="Filter Backlog" aria-label="Filter Backlog" aria-describedby="filter-tasks">
                            </div>
                        </form>
                    </div>
                    <div class="content-list-body">
                        <div class="card-list">
                            <div class="backlog-card-list-body">
                                @if (count($backlog) > 0)
                                    @foreach ($backlog as $project)
                                        @php
                                            $dt1 = new DateTime(dateFormatter($project->start_date));
                                            $dt2 = new DateTime(dateFormatter($project->end_date));
                                            $interval = $dt1->diff($dt2);
    
                                            $projectDue = $interval->format('%a');
    
                                            $tasks = Project::find($project->id)->tasks;
                                            $allTask = count($tasks);
                                            $completedTask = Task::where(['project_id' => $project->id, 'user_id' => Auth::user()->id, 'status' => '1'])->count();
                                            if($completedTask == 0 && $allTask == 0){
                                                $progress = 0;
                                                $completionRate = '-/-';
                                            }
                                            else
                                            {
                                                $progress = ceil(($completedTask/$allTask)*100);
                                                $completionRate = $completedTask ."/". $allTask;
                                            }

                                            $dt3 = new DateTime(date('d-m-Y'));

                                            if(strtotime(dateFormatter($project->end_date)) > strtotime(date('d-m-Y'))){
                                                $interval = $dt1->diff($dt3);
                                                $newProjectDue = $interval->format('%a');

                                                $realProjectDueDate = $projectDue - $newProjectDue;
                                                $projectDue = "Project will end in $realProjectDueDate day(s)";
                                            }
                                            else if(strtotime(dateFormatter($project->end_date)) == strtotime(date('d-m-Y'))){
                                                $projectDue = "Project will elapse today";
                                            }
                                            else {
                                                $interval = $dt2->diff($dt3);
                                                $newProjectDue = $interval->format('%a');
                                                
                                                $projectDue = "Project duration elapsed $newProjectDue day(s) ago";
                                            }
                                        @endphp
                                        <div class="card card-task">
                                            <div class="progress">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $progress }}%" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="card-body">
                                                <div class="card-title">
                                                    <a href="{{ route('projects.index', $project->uuid) }}">
                                                        <h6 data-filter-by="text" class="text-primary">{{ $project->name }}</h6>
                                                    </a>
                                                    <span class="text-small">{{ $projectDue }}</span>
                                                </div>
                                                <div class="card-meta">
                                                    <div class="d-flex align-items-center">
                                                        <i class="material-icons">playlist_add_check</i>
                                                        <span>{{ $completionRate }}</span>
                                                    </div>
                                                    <div class="dropdown card-options">
                                                        <button class="btn-options" type="button" id="task-dropdown-button-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="material-icons">more_vert</i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="#">Mark as Completed</a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item text-danger" href="#">Archive Project</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-md-12 text-center">
                                        <p class="lead">You don't have any project in your backlog &#x1f635;</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-md-12 text-center">
            <p class="lead">No Project(s) Added &#x1f635;</p>
        </div>
    @endif

@endsection