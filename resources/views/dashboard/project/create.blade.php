@extends('layouts.dashboard.main')

@section('title', 'Create Project')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('projects') }}">Projects</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create Project</li>
@endsection

@section('page-content')
    <div class="row pt-2">
        <div class="col-md-2"></div>
        <div class="col-lg-8 col-md-8 ">
            <form method="POST" action="{{ route('projects.save') }}" enctype="multipart/form-data" id="project-add-form">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">New Project</h5>
                        </div>
                        <div class="modal-body bg-white">
                            <div class="tab-pane fade show active" id="task-add-details" role="tabpanel" aria-labelledby="task-add-details-tab">
                                <h6>General Details</h6>
                                @csrf
                                <div class="form-group row align-items-center">
                                    <label class="col-3">Name</label>
                                    <input class="form-control col" type="text" placeholder="Project Name" required name="project_name" />
                                </div>
                                <div class="form-group row">
                                    <label class="col-3">Description</label>
                                    <textarea class="form-control col" rows="3" placeholder="Project description" required name="project_description"></textarea>
                                </div>
                                <div class="form-group">
                                    <input type="file" required name="project_cover_image" class="file" accept="image/*">
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control input-lg" disabled placeholder="Project Cover Image">
                                        <span class="input-group-btn">
                                            <button class="browse btn btn-primary input-lg" type="button">Select Image</button>
                                        </span>
                                    </div>
                                </div>
                                <hr>
                                <h6>Timeline</h6>
                                <div class="form-group row align-items-center">
                                    <label class="col-3">Start Date</label>
                                    <input class="form-control col" type="text" id="start-date" placeholder="Project start" required name="project_start" />
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-3">Due Date</label>
                                    <input class="form-control col" type="text" id="due-date" placeholder="Project due" required name="project_due" />
                                </div>
                                <div class="alert alert-warning text-small" role="alert">
                                    <span>You can change Project due dates at any time.</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button role="button" class="btn btn-primary" id="saveProject" type="submit">Save Project</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('runJavascript')
    <script src="{{ asset('assets/vendor/datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script type="text/javascript">
        var datePicker = function() {
            $('#start-date, #due-date').datepicker({
                'format': 'dd/mm/yyyy',
                'autoclose': true
            });
        };
        datePicker();

        $('#project-add-form').submit(function(event){
            event.preventDefault();
            dataToSend = new FormData($("#project-add-form")[0]);
            formAction = $(this).attr('action');
            formMethod = $(this).attr('method');
            btnHandler = $("button[type=submit]",this).attr('id'); 
            $.ajax({
                url : formAction,
                method : formMethod,
                data : dataToSend,
                dataType : "JSON",
                contentType : false,
                processData : false,
                cache : false,
                traditional : true,
                beforeSend : function(){
                    $('#'+btnHandler+'').html("Processing");
                    $('#'+btnHandler+'').prop('disabled', true);
                },
                success : function(data){
                    console.log(data);
                    if(data.status == 200){
                        notify("success", data.response.message);
                        setTimeout(() => {
                            window.location.href = data.response.redirectTo;                        
                        }, 3000);
                    }
                    else
                    {
                        notify("error", "An Error Occurred", data.response);  
                        $('#'+btnHandler+'').html("Try Again");
                        $('#'+btnHandler+'').prop('disabled', false); 
                    }
                },
                error : function(error){
                    errorResponse = error.responseJSON.errors;
                    console.log(error);
                    var inputKeys = ['project_name', 'project_description', 'project_start', 'project_due', 'project_cover_image'];

                    inputKeys.forEach(inputKey => {
                        if(`${inputKey}` in errorResponse){
                            switch (`${inputKey}`) {
                                case 'project_name':
                                    notify("warning", "Error", errorResponse.project_name);
                                    break;
                                case 'project_description':
                                    notify("warning", "Error", errorResponse.project_description);
                                    break;
                                case 'project_start':
                                    notify("warning", "Error", errorResponse.project_start);
                                    break;
                                case 'project_due':
                                    notify("warning", "Error", errorResponse.project_due);
                                    break;
                                case 'project_cover_image':
                                    notify("warning", "Error", errorResponse.project_cover_image);
                                    break;
                                default:
                                    break;
                            }
                        }
                    });

                    $('#'+btnHandler+'').html("Try Again");
                    $('#'+btnHandler+'').prop('disabled', false);
                }
            });
        })
    </script>
@endsection