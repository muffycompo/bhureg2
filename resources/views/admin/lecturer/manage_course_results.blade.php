@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Manage Assigned Courses</div>

                <div class="panel-body">
                    <p><strong>Current Semester: {{ expandSemester(currentSemester()) }} | Current Session: {{ currentAcademicSession() }}</strong></p>
                    <table class="table table-bordered table-condensed">
                        <tr>
                            <th class="text-center">Course Code</th>
                            <th class="text-center">Course Title</th>
                            <th class="text-center">Course Unit</th>
                            <th class="text-center">Semester</th>
                            <th class="text-center">Manage Result</th>
                        </tr>
                        @if(count($courses) > 0)
                            @foreach($courses as $course)
                            <tr class="text-center">
                                <td>{{ $course->courses_course_id }}</td>
                                <td>{{ courseTitleAndUnits($course->courses_course_id) }}</td>
                                <td>{{ courseTitleAndUnits($course->courses_course_id,true) }}</td>
                                <td>{{ expandSemester(semesterFromCourseId($course->courses_course_id, $course->sessions_session_id)) }}</td>
                                <td>
                                    {{-- */$semester = semesterFromCourseId($course->courses_course_id, $course->sessions_session_id);/* --}}
                                    {{--@if(isCourseForCurrentSemester($course->courses_course_id))--}}
                                        <a href="{{ route('admin.lecturer_manage_result',[encryptId($course->courses_course_id), encryptId($course->sessions_session_id), encryptId($semester)]) }}" data-toggle="tooltip" data-placement="top" title="Manage Results" alt="Manage Results">
                                            <span class="glyphicon glyphicon-list-alt"></span>
                                        </a>
                                    {{--@endif--}}
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5"><p class="text-center">No course has been assigned to you.</p></td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
