@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Search Course</div>
                <div class="panel-body">
                    <div class="row">
                        {!! Form::open(['route' => 'admin.post_hod_manage_find_courses', 'method' => 'POST', 'class' => 'form-inline', 'role' => 'form']) !!}

                        <div class="col-md-12 col-xs-12">
                            <div class="form-group">
                                <label for="department">Department</label><br>
                                {!! Form::select('department_id', departmentDropdownOptions(),old('department_id',request()->get('department_id')),['class' => 'form-control', 'id' => 'department']) !!}
                            </div>
                            <div class="form-group">
                                <label for="level">Level</label><br>
                                {!! Form::select('level_id', levelDropdownOptions(),old('level_id',request()->get('level_id')),['class' => 'form-control', 'id' => 'level']) !!}
                            </div>
                            <div class="form-group">
                                <label for="semester">Semester</label><br>
                                {!! Form::select('semester_id', semesterDropdownOptions(),old('semester_id',request()->get('semester_id')),['class' => 'form-control', 'id' => 'semester']) !!}
                            </div>
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="form-group">
                                <p style="margin-top: 10px;">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <span class="glyphicon glyphicon-search"></span>
                                        Show Courses
                                    </button>
                                </div>
                                </p>
                            </div>
                        </div>
                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
            @if(isset($courses))
            <div class="panel panel-default">
                <div class="panel-heading">Courses List</div>

                <div class="panel-body">

                    <table class="table table-bordered table-condensed">
                        <tr>
                            <th class="text-center">Code</th>
                            <th class="text-center">Title</th>
                            <th class="text-center">Unit</th>
                            <th class="text-center">Semester</th>
                            <th class="text-center">Level</th>
                            <th class="text-center">Action</th>
                        </tr>
                        @if(count($courses) > 0)
                            @foreach($courses as $course)
                                <tr class="text-center">
                                    <td>{{ $course->course_id }}</td>
                                    <td>{{ courseTitleAndUnits($course->course_id) }}</td>
                                    <td>{{ courseTitleAndUnits($course->course_id,true) }}</td>
                                    <td>{{ expandSemester($course->semester) }}</td>
                                    <td>{{ expandLevel($course->course_level) }}</td>
                                    <td>
                                        <a href="{{ route('admin.hod_manage_add_core_course',[encryptId($course->course_id), $course->semester, $course->course_level]) }}" data-toggle="tooltip" data-placement="top" title="Core" alt="Core" class="text-primary">
                                            <span class="glyphicon glyphicon-object-align-left"></span>
                                        </a>
                                        &nbsp;
                                        <a href="{{ route('admin.hod_manage_add_elective_course',[encryptId($course->course_id), $course->semester, $course->course_level]) }}" data-toggle="tooltip" data-placement="top" title="Elective" alt="Elective" class="text-danger">
                                            <span class="glyphicon glyphicon-object-align-right"></span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7">No record found, try a different search criteria!.</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
