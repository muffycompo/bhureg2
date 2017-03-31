@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">

            @include('bhu._partials._alert')

            <div class="panel panel-default">
                <div class="panel-heading">Assign Course(s)</div>

                <div class="panel-body">
                    <p class="text-right"><a href="{{ route('admin.hod_manage_lecturers') }}" class="btn btn-danger">
                        <span class="glyphicon glyphicon-backward"></span>
                        Back
                    </a></p>

                    {!! Form::open(['route' => 'admin.post_hod_manage_assign_course', 'method' => 'POST', 'role' => 'form']) !!}
                    <div class="row">
                        <div class="col-md-6 col-xs-6">

                            <div class="form-group">
                                <label for="user_id">Lecturer</label>
                                {!! Form::select('user_id',lecturersDropdownOptions(),null,['class' => 'form-control', 'id' => 'user_id']) !!}
                                <p class="help-block"><em>{{ expandProgram(session('departments_department_id')) }} Lecturers Only!</em></p>
                                @if ($errors->has('user_id'))
                                    <span class="text-danger">{{ $errors->first('user_id') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <div class="form-group">
                                <label for="semester">Semester</label>
                                {!! Form::select('semester',semesterDropdownOptions(),null,['class' => 'form-control', 'id' => 'semester']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <table class="table table-condensed table-bordered">
                                <tr>
                                    <th class="text-center">Code</th>
                                    <th class="text-center">Title</th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Action</th>
                                </tr>
                                @if(count($courses) > 0)
                                    @foreach($courses as $course)
                                        <tr class="text-center">
                                            <td>{{ $course->courses_course_id }}</td>
                                            <td>{{ courseTitleAndUnits($course->courses_course_id) }}</td>
                                            <td>{{ courseTitleAndUnits($course->courses_course_id, true) }}</td>
                                            <td>{{ $course->course_type }}</td>
                                            <td>
                                                {!! Form::checkbox('course_id[]',$course->courses_course_id) !!}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5">No Courses to Assign, add Courses to your Department to Assign.</td>
                                    </tr>
                                @endif

                            </table>

                            <button type="submit" class="btn btn-primary pull-right" style="margin-bottom: 10px;">
                                <span class="glyphicon glyphicon-plus-sign"></span>
                                Assign Course
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
