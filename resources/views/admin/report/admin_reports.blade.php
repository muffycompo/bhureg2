@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">

            @include('bhu._partials._alert')

            <div class="panel panel-default">
                <div class="panel-heading">Manage Result Approval</div>

                <div class="panel-body">
                    {!! Form::open(['route' => 'admin.post_find_submission', 'method' => 'GET', 'role' => 'form']) !!}
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
                            <button type="submit" class="btn btn-primary" style="margin-bottom: 10px;">
                                <span class="glyphicon glyphicon-search"></span>
                                Find Submissions
                            </button>
                        </div>
                    </div>
                    {!! Form::close() !!}

                    @if(isset($courses))
                        <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <table class="table table-condensed table-bordered">
                                <tr>
                                    <th class="text-center">Code</th>
                                    <th class="text-center">Title</th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-center">Session</th>
                                    <th class="text-center">Semester</th>
                                    <th class="text-center">Level</th>
                                    <th class="text-center">Action</th>
                                </tr>
                                @if(count($courses) > 0)
                                    @foreach($courses as $course)
                                        <tr class="text-center">
                                            <td>{{ $course->courses_course_id }}</td>
                                            <td>{{ courseTitleAndUnits($course->courses_course_id) }}</td>
                                            <td>{{ courseTitleAndUnits($course->courses_course_id, true) }}</td>
                                            <td>{{ $course->session_session_id }}</td>
                                            <td>{{ expandSemester($course->semester) }}</td>
                                            <td>{{ expandLevel($course->course_level) }}</td>
                                            <td>
                                                <a href="{{ route('admin.report_manage_result', [encryptId($course->courses_course_id), encryptId($course->session_session_id), encryptId($course->semester)]) }}" data-toggle="tooltip" data-placement="top" title="View Results" class="text-info">
                                                    <span class="glyphicon glyphicon-list-alt"></span>
                                                </a>
                                                {{--&nbsp--}}
                                                {{--<a href="#" data-toggle="tooltip" data-placement="top" title="View Summary" class="text-primary">--}}
                                                    {{--<span class="glyphicon glyphicon-th"></span>--}}
                                                {{--</a>&nbsp;--}}
                                                {{--<a href="#" data-toggle="tooltip" data-placement="top" title="View Remarks" class="text-danger">--}}
                                                    {{--<span class="glyphicon glyphicon-list"></span>--}}
                                                {{--</a>--}}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="text-center">
                                        <td colspan="7">No Result(s) Submission found!</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
