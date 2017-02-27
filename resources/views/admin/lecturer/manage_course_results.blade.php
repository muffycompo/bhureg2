@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Manage Assigned Courses</div>

                <div class="panel-body">
                    <p><strong>Semester: First | Session: 2016/2017</strong></p>
                    <table class="table table-bordered table-condensed">
                        <tr>
                            <th class="text-center">Course Code</th>
                            <th class="text-center">Course Title</th>
                            <th class="text-center">Course Unit</th>
                            <th class="text-center">Manage Result</th>
                        </tr>
                        @if(count($courses) > 0)
                            @foreach($courses as $course)
                            <tr class="text-center">
                                <td>{{ $course->courses_course_id }}</td>
                                <td>{{ courseTitleAndUnits($course->courses_course_id) }}</td>
                                <td>{{ courseTitleAndUnits($course->courses_course_id,true) }}</td>
                                <td>
                                    <a href="{{ route('admin.lecturer_manage_result',[encryptId($course->courses_course_id)]) }}" title="Manage Results" alt="Manage Results">
                                        <span class="glyphicon glyphicon-list-alt"></span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4"><p class="text-center">No course has been assigned to you.</p></td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
