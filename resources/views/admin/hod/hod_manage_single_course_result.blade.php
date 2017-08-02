@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">

            @include('bhu._partials._alert')

            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row hidden-print">
                        <div class="col-md-6"><p>Adjusting Result For: <strong>{{ $course_id }}</strong></p></div>
                        <div class="col-md-6 text-right">
                            @if(isCourseResultFinalizedByHod($course_id,$session_id,$semester_id))
                                <a href="javascript:;" onclick="window.print();" class="btn btn-primary"><span class="glyphicon glyphicon-print"></span> Print</a>
                            @endif
                            {{--<a href="{{ route('admin.hod_manage_result_adjustments') }}" class="btn btn-danger"><span class="glyphicon glyphicon-backward"></span> Back</a>--}}
                        </div>
                    </div>

                </div>

                <div class="panel-body">
                    @if(! isCourseResultFinalizedByHod($course_id,$session_id,$semester_id))
                    <div class="alert alert-info hidden-print">
                        <p><strong><span class="glyphicon glyphicon-warning-sign"></span>You can ONLY make adjustments to {{ $course_id }} Result ONCE!</strong></p>
                    </div>
                    @endif
                    <p class="clearfix"><strong>Course: {{ $course_id }} - {{ courseTitleAndUnits($course_id) }}</strong>
                        <a href="{{ route('admin.report_manage_result', [encryptId($course_id), encryptId($session_id), encryptId($semester_id)]) }}" class="btn btn-danger pull-right hidden-print">
                            <span class="glyphicon glyphicon-backward"></span>
                            Back
                        </a>
                    </p>
                    <table class="table table-bordered table-condensed">
                        <tr>
                            <th class="text-center">S/N</th>
                            <th class="text-center">Matriculation #</th>
                            <th class="text-center">Name</th>
                            <th class="text-center" nowrap="nowrap">Exam (100%)</th>
                            @if(isCourseResultFinalizedByHod($course_id,$session_id,$semester_id))
                                <th class="text-center">Total</th>
                                <th class="text-center">Grade</th>
                            @endif
                        </tr>
                        @if(count($courses) > 0)
                        {!! Form::open(['route' => 'admin.post_hod_manage_course_result','method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                            @foreach($courses as $course)
                            <tr class="text-center">
                                <td>{{ $sn++ }}</td>
                                <td nowrap="nowrap">{{ $course->students_student_id }}</td>
                                <td nowrap="nowrap">{{ studentNameFromMatriculationNo($course->students_student_id) }}</td>
                                @if(isCourseResultFinalizedByHod($course_id,$session_id,$semester_id))
                                <td>{{ $course->exam }}</td>
                                <td>
                                    {{ $course->exam }}
                                </td>
                                <td>
                                    {{ expandGrade($course->exam) }}
                                </td>
                                @else
                                    <td width="10%">
                                        <div class="row">
                                            <div class="col-md-10 col-xs-10 col-md-offset-1">
                                                {!! Form::input('text','exam[]', $course->exam,['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </td>
                                    {!! Form::hidden('student_id[]', $course->students_student_id) !!}
                                    {!! Form::hidden('alternate_entry', $alternate_entry) !!}
                                @endif
                            </tr>
                            @endforeach
                            @if(! isCourseResultFinalizedByHod($course_id,$session_id,$semester_id))
                                {!! Form::hidden('course_id', $course_id) !!}
                                {!! Form::hidden('lecturer_id', $user_id) !!}
                                {!! Form::hidden('semester_id', $semester_id) !!}
                                <tr>
                                    <td colspan="5">
                                        <div class="form-group pull-right">
                                            <a href="{{ route('admin.report_manage_result', [encryptId($course_id), encryptId($session_id), encryptId($semester_id)]) }}" class="btn btn-danger">
                                                <span class="glyphicon glyphicon-backward"></span>
                                                Back
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <span class="glyphicon glyphicon-floppy-disk"></span>
                                                Adjust & Finalize Result
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                <tr class="hidden-print">
                                    <td colspan="7">
                                        <p class="text-right">
                                            <a href="{{ route('admin.lecturer_export_result',[encryptId($course_id),'csv']) }}" class="btn btn-primary">
                                                <span class="glyphicon glyphicon-download"></span>
                                                Export CSV
                                            </a>
                                            <a href="{{ route('admin.lecturer_export_result',[encryptId($course_id),'xls']) }}" class="btn btn-success">
                                                <span class="glyphicon glyphicon-download"></span>
                                                Export Excel
                                            </a>
                                            <a href="{{ route('admin.lecturer_export_result',[encryptId($course_id),'pdf']) }}" class="btn btn-danger">
                                                <span class="glyphicon glyphicon-download"></span>
                                                Export PDF
                                            </a>
                                        </p>
                                    </td>
                                </tr>
                            @endif
                        {!! Form::close() !!}
                        @else
                            <tr>
                                <td colspan="7"><p class="text-center">No student has registered for this course.</p></td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
