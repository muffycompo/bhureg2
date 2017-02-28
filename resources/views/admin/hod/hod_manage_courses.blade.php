@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">

            @include('bhu._partials._alert')

            <div class="panel panel-default">
                <div class="panel-heading">Manage Courses</div>

                <div class="panel-body">
                    <p class="text-right"><a href="{{ route('admin.hod_manage_add_course') }}" class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span>
                            Add Course
                    </a></p>
                    <table class="table table-bordered table-condensed">
                        <tr>
                            <th class="text-center">Code</th>
                            <th class="text-center">Title</th>
                            <th class="text-center">Unit</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Semester</th>
                            <th class="text-center">Level</th>
                            <th class="text-center">Action</th>
                        </tr>
                        @if(count($courses) > 0)
                            @foreach($courses as $course)
                                <tr class="text-center">
                                    <td>{{ $course->courses_course_id }}</td>
                                    <td>{{ courseTitleAndUnits($course->courses_course_id) }}</td>
                                    <td>{{ courseTitleAndUnits($course->courses_course_id,true) }}</td>
                                    <td>{{ $course->course_type }}</td>
                                    <td>{{ expandSemester($course->semester) }}</td>
                                    <td>{{ expandLevel($course->course_level) }}</td>
                                    <td>
                                        <a href="{{ route('admin.hod_manage_edit_course',[encryptId($course->courses_course_id),$course->semester,$course->course_level]) }}" data-toggle="tooltip" data-placement="top" title="Edit" alt="Edit" class="text-info">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        {{--*/ $sn += 1 /*--}}
                                        <span data-toggle="modal" data-target="#hodDeleteCourseModal{{ $sn }}">
                                            &nbsp;<a href="javascript:;" data-toggle="tooltip" data-placement="top" class="text-danger" title="Delete" Alt="Delete">
                                                <span class="glyphicon glyphicon-remove"></span>
                                            </a>
                                        </span>

                                        @include('bhu._partials._hod_delete_course_warning_modal')
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7">No Courses added to your Department! Please add courses to Manage.</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
