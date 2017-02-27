@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row">
        <div class="col-md-10 col-md-offset-1">

            @include('bhu._partials._alert')

            <div class="panel panel-default">
                <div class="panel-heading"><strong>Registration Session: 2016/2017 | Department: {{ expandProgram(session('deptid')) }} | Level: {{ expandLevel(session('levelid')) }}</strong></div>

                <div class="panel-body">

                    <table class="table table-bordered">
                        {!!  Form::open(['route' => 'post.register', 'method' => 'POST']) !!}
                        <tr>
                            <th class="text-center">S/No.</th>
                            <th class="text-center">Course Code</th>
                            <th class="text-center">Course Title</th>
                            <th class="text-center">Course Units</th>
                            <th class="text-center">Course Type</th>
                            <th class="text-center">Action</th>
                        </tr>

                        @if(count($courses) > 0)
                            @foreach($courses as $course)
                            <tr class="text-center">
                                <td>{{ $sn++ }}</td>
                                <td>{{ $course->courses_course_id }}</td>
                                <td nowrap="nowrap">{{ courseTitleAndUnits($course->courses_course_id) }}</td>
                                <td>{{ courseTitleAndUnits($course->courses_course_id,true) }}</td>
                                <td>{{ $course->course_type }}</td>
                                @if(isCourseRegistered($course->courses_course_id,'2016/2017'))
                                    <td>
                                        <?php $units = $units + (int) courseTitleAndUnits($course->courses_course_id,true); ?>
                                        {!! Form::hidden('course_id[]',$course->courses_course_id) !!}
                                        <a href="{{ route('get.drop_course',[base64_encode($course->courses_course_id)]) }}" class="text-danger" style="cursor: pointer;" title="Drop Course" alt="Drop Course">
                                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                        </a>
                                    </td>
                                @else
                                    <td>{!! Form::checkbox('course_id[]',$course->courses_course_id)  !!}</td>
                                @endif

                            </tr>
                            @endforeach
                        @else
                            <tr  class="text-center">
                                <td colspan="6"><p class="text-danger">No courses have been approved for registration!</p></td>
                            </tr>
                        @endif
                        @if(count($carryovers) > 0)
                            @foreach($carryovers as $carryover)
                                <tr class="text-center">
                                    <?php $units = $units + (int) courseTitleAndUnits($carryover['courses_course_id'],true); ?>
                                    <td>{{ $sn++ }}</td>
                                    <td>{{ $carryover['courses_course_id'] }}</td>
                                    <td nowrap="nowrap">{{ courseTitleAndUnits($carryover['courses_course_id']) }}</td>
                                    <td>{{ courseTitleAndUnits($carryover['courses_course_id'],true) }}</td>
                                    <td>{{ expandCourseType($carryover['courses_course_id']) }}</td>
                                    <td>
                                        <strong>CO</strong>
                                        {!! Form::hidden('course_id[]',$carryover['courses_course_id']) !!}
                                    </td>

                                </tr>
                            @endforeach
                        @endif
                        @if(count($courses) > 0 or count($carryovers) > 0)
                            <tr  class="text-center">
                                <td colspan="3" class="text-right"><strong>Total Credit Units (Registered)</strong></td>
                                {{--<td><strong>Total Credit Units = {{ $units }}</strong></td>--}}
                                <td><strong>{{ $units }}</strong></td>
                                <td colspan="2">
                                    <button type="submit" name="submit" class="btn btn-primary pull-right">
                                        <span class="glyphicon glyphicon-ok"></span>
                                        Register
                                    </button>
                                    {{--{!! Form::submit('Register',['class' => 'btn btn-primary pull-right']) !!}--}}
                                </td>
                            </tr>
                        @endif
                        {!! Form::close() !!}
                    </table>
                    @if($units > 30)
                        <div class="alert alert-danger" role="alert">
                            <p><strong>Total Credit Units of {{ $units }} EXCEEDS maximum allowed by the Department!!!</strong> Your registration is considered INVALID</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
