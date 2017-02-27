@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">

            @include('bhu._partials._alert')

            <div class="panel panel-default">
                <div class="panel-heading">Manage Lecturers</div>

                <div class="panel-body">
                    <p class="text-right"><a href="{{ route('admin.get_hod_manage_assign_course') }}" class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span>
                            Assign Course
                    </a></p>
                    <table class="table table-bordered table-condensed">
                        <tr>
                            <th class="text-center">Course Code</th>
                            <th class="text-center">Course Title</th>
                            <th class="text-center">Lecturer Name</th>
                            <th class="text-center">Semester</th>
                            <th class="text-center">Session</th>
                            <th class="text-center">Action</th>
                        </tr>
                        @if(count($assignedCourses) > 0)
                            @foreach($assignedCourses as $assignedCourse)
                                <tr class="text-center">
                                    <td>{{ $assignedCourse->courses_course_id }}</td>
                                    <td>{{ courseTitleAndUnits($assignedCourse->courses_course_id) }}</td>
                                    <td><strong>{{ changeStringToTitleCase($assignedCourse->first_name) . ' ' . changeStringToTitleCase($assignedCourse->last_name) }}</strong></td>
                                    <td>{{ expandSemester($assignedCourse->semester) }}</td>
                                    <td>{{ $assignedCourse->sessions_session_id }}</td>
                                    <td>
                                        {{--*/ $sn += 1 /*--}}
                                        <a href="javascript:;" data-toggle="modal" data-target="#hodUnassignCourseModal{{ $sn }}" class="text-danger">
                                            <span class="glyphicon glyphicon-remove"></span>
                                        </a>

                                        @include('bhu._partials._hod_unassign_course_warning_modal')

                                    </td>
                                </tr>
                            @endforeach
                            <tr class="text-center">
                                <td colspan="6">
                                    {!! $assignedCourses->render() !!}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="6">You have not assigned any Course to a Lecturer!.</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
