@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">

            @include('bhu._partials._alert')

            <div class="panel panel-default">
                <div class="panel-heading">Manage Detailed Reports</div>

                <div class="panel-body">
                    {!! Form::open(['route' => 'admin.post_find_detailed_report', 'method' => 'GET', 'role' => 'form']) !!}
                    <div class="row">
                        @if(session('role') == 'Dean' or session('role') == 'Senate')
                        <div class="col-md-3 col-xs-3">
                            <div class="form-group">
                                <label for="department_id">Department</label>
                                {!! Form::select('department_id',['CMP' => 'Computer Science'],$deptId,['class' => 'form-control', 'id' => 'department_id']) !!}
                            </div>
                        </div>
                         @endif

                        <div class="col-md-3 col-xs-3">
                            <div class="form-group">
                                <label for="session_id">Session</label>
                                {!! Form::select('session_id',sessionsDropDownOptions(),$sessionId,['class' => 'form-control', 'id' => 'session_id']) !!}
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-3">
                            <div class="form-group">
                                <label for="semester">Semester</label>
                                {!! Form::select('semester',semesterDropdownOptions(),$semesterId,['class' => 'form-control', 'id' => 'semester']) !!}
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-3">
                            <div class="form-group">
                                <label for="level_id">Level</label>
                                {!! Form::select('level_id',levelDropdownOptions(),$levelId,['class' => 'form-control', 'id' => 'level_id']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <button type="submit" name="report" value="detailed_result" class="btn btn-info" style="margin-bottom: 10px;">
                                <span class="glyphicon glyphicon-paperclip"></span>
                                Detailed Result Page
                            </button>
                            <button type="submit" name="report" value="detailed_summary" class="btn btn-primary" style="margin-bottom: 10px;">
                                <span class="glyphicon glyphicon-stats"></span>
                                Summary Page
                            </button>
                            <button type="submit" name="report" value="detailed_remark" class="btn btn-danger" style="margin-bottom: 10px;">
                                <span class="glyphicon glyphicon-list"></span>
                                Remark Page
                            </button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
