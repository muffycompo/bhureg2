@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">

            @include('bhu._partials._alert')

            <div class="panel panel-default">
                <div class="panel-heading">Edit Course: <strong>{{ $courseId . ' - ' . courseTitleAndUnits($courseId) }}</strong></div>

                <div class="panel-body">
                    <p class="text-right"><a href="{{ route('admin.hod_manage_courses') }}" class="btn btn-danger">
                        <span class="glyphicon glyphicon-backward"></span>
                        Back
                    </a></p>
                    @if(count($course) > 0)
                    <div class="row">
                        <div class="col-md-5 col-xs-5">
                            {!! Form::open(['route' => 'admin.post_hod_manage_edit_course', 'method' => 'POST', 'role' => 'form']) !!}

                            <div class="form-group">
                                <label for="course_type">Course Type</label>
                                {!! Form::select('course_type',['Core' => 'Core', 'Elective' => 'Elective'],$course->course_type,['class' => 'form-control', 'id' => 'course_type']) !!}
                            </div>

                            <div class="form-group">
                                <label for="semester">Semester</label>
                                {!! Form::select('semester',semesterDropdownOptions(),$course->semester,['class' => 'form-control', 'id' => 'semester']) !!}
                            </div>

                            <div class="form-group">
                                <label for="level">Level</label>
                                {!! Form::select('level',levelDropdownOptions(),$course->course_level,['class' => 'form-control', 'id' => 'level']) !!}
                            </div>
                            {!! Form::hidden('course_id', $course->courses_course_id) !!}
                            <button type="submit" class="btn btn-primary pull-right" style="margin-bottom: 10px;">
                                <span class="glyphicon glyphicon-floppy-save"></span>
                                Save
                            </button>

                            {!! Form::close() !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
