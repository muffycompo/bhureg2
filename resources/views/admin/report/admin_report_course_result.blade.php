@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">

            @include('bhu._partials._alert')

            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row hidden-print">
                        <div class="col-md-6"><p>Manage Results</p></div>
                        <div class="col-md-6 text-right">
                            @if(! isCourseResultFinalizedByHod($course_id))
                                <a href="{{ route('admin.lecturer_finalize_result',[encryptId($course_id),encryptId($session_id),encryptId($semester_id)]) }}" class="btn btn-success">
                                    <span class="glyphicon glyphicon-check"></span> Approve Result
                                </a>
                                {{-- */$lecturerId = lecturerIdFromCourse($course_id, $session_id);/* --}}
                                @if(! is_null($lecturerId))
                                    <a href="{{ route('admin.hod_manage_result_adjustment',[encryptId($course_id),encryptId($lecturerId),$semester_id]) }}" class="btn btn-danger">
                                        <span class="glyphicon glyphicon-edit"></span> Adjust Result
                                    </a>
                                @endif
                            @endif
                            <a href="javascript:;" onclick="window.print();" class="btn btn-primary"><span class="glyphicon glyphicon-print"></span> Print</a>
                            {{--@if(request()->segment(2) == 'manage_find_submission')--}}
                                {{--<a href="{{ redirect()->back()->getTargetUrl() }}" class="btn btn-danger"><span class="glyphicon glyphicon-backward"></span> Back</a>--}}
                            {{--@else--}}
                                {{--<a href="{{ route('admin.get_reports') }}" class="btn btn-danger"><span class="glyphicon glyphicon-backward"></span> Back</a>--}}
                            {{--@endif--}}
                        </div>
                    </div>

                </div>

                <div class="panel-body">
                    <p><strong>Course: {{ $course_id }} - {{ courseTitleAndUnits($course_id) }}
                            {{--@if(! isCourseResultApprovedByDeanSenate($course_id, $session_id, $semester_id))--}}
                                {{--<span class="text-danger">-> [RESULT NOT APPROVED BY {{ changeStringToUpperCase(session('role')) }}]</span>--}}
                            {{--@else--}}
                                {{--<span class="text-success">-> [RESULT APPROVED]</span>--}}
                            {{--@endif--}}
                        </strong></p>
                    <table class="table table-bordered table-condensed">
                        <tr>
                            <th class="text-center">S/N</th>
                            <th class="text-center">Matriculation #</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">C.A (40%)</th>
                            <th class="text-center">Exam (60%)</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Grade</th>
                        </tr>
                        @if(count($courses) > 0)
                            @foreach($courses as $course)
                                <tr class="text-center">
                                    <td>{{ $sn++ }}</td>
                                    <td nowrap="nowrap">{{ $course->students_student_id }}</td>
                                    <td nowrap="nowrap">{{ studentNameFromMatriculationNo($course->students_student_id) }}</td>
                                    <td>{{ $course->ca }}</td>
                                    <td>{{ $course->exam }}</td>
                                    <td>
                                        {{--*/ $total = $course->ca + $course->exam /*--}}
                                        {{ $total }}
                                    </td>
                                    <td>
                                        {{ expandGrade($total) }}
                                    </td>
                                </tr>
                            @endforeach

                            <tr class="hidden-print">
                                <td colspan="7">
                                    <p class="text-right">
                                        @if(! isCourseResultApprovedByDeanSenate($course_id, $session_id, $semester_id))
                                            <a href="javascript:;" class="btn btn-primary" data-toggle="modal" data-target="#approveResultModal">
                                                <span class="glyphicon glyphicon-check"></span>
                                                Approve Result
                                            </a>
                                        @endif
                                    </p>
                                </td>
                            </tr>

                        @else
                            <tr>
                                <td colspan="7"><p class="text-center">No student has registered for this course.</p></td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            @include('bhu._partials._approve_result_warning_modal')

        </div>
    </div>
</div>
@endsection
