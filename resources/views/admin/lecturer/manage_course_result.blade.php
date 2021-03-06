@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">

            @include('bhu._partials._alert')
            @if(! isCourseResultFinalized($course_id,$session_id,$semester_id))
            <div class="panel panel-default">
                <div class="panel-heading">Bulk Result Upload</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-8 col-xs-8 col-md-offset-2">
                            {!! Form::open(['route' => 'admin.post_lecturer_manage_result_upload', 'files' => true, 'method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form']) !!}

                            <div class="form-group">
                                <label for="upload_result">Bulk Result Upload</label>
                                {!! Form::file('course_result',['id' => 'upload_result', 'class' => 'form-control']) !!}
                                {!! Form::hidden('course_id', $course_id) !!}
                                {!! Form::hidden('session_id', $session_id) !!}
                                {!! Form::hidden('semester_id', $semester_id) !!}
                                {!! Form::hidden('alternate_entry', $alt_entry) !!}
                                <p class="help-block">Make sure you have formatted your file as shown in this <a href="{{ route('admin.manage_download_csv_sample') }}">SAMPLE</a> CSV file.</p>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary pull-right">
                                    <span class="glyphicon glyphicon-upload"></span>
                                    Upload
                                </button>
                            </div>

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row hidden-print">
                        <div class="col-md-6"><p>Manage Course Result</p></div>
                        <div class="col-md-6 text-right">
                            @if(isCourseResultFinalized($course_id,$session_id,$semester_id))
                                <a href="javascript:;" onclick="window.print();" class="btn btn-primary"><span class="glyphicon glyphicon-print"></span> Print</a>
                            @endif
                            @if(session('role') == 'Lecturer')
                                <a href="{{ route('admin.lecturer_manage_results') }}" class="btn btn-danger"><span class="glyphicon glyphicon-backward"></span> Back</a>
                            @endif
                        </div>
                    </div>

                </div>

                <div class="panel-body">
                    <p>
                        <strong>Course: {{ $course_id }} - {{ courseTitleAndUnits($course_id) }}</strong>
                        @if(! isCourseResultFinalized($course_id,$session_id,$semester_id))
                            <span class="pull-right"><a href="{{ route('admin.lecturer_manage_result',[encryptId($course_id),encryptId($session_id),encryptId($semester_id),encryptId(1)]) }}">Single Score Result Entry</a></span>
                        @endif
                    </p>
                    <table class="table table-bordered table-condensed">
                        <tr>
                            <th class="text-center">S/N</th>
                            <th class="text-center">Matriculation #</th>
                            <th class="text-center">Name</th>
                            <th class="text-center" nowrap="nowrap">C.A (40%)</th>
                            <th class="text-center" nowrap="nowrap">Exam (60%)</th>
                            @if(isCourseResultFinalized($course_id,$session_id,$semester_id))
                                <th class="text-center">Total</th>
                                <th class="text-center">Grade</th>
                            @endif
                        </tr>
                        @if(count($courses) > 0)
                        {!! Form::open(['route' => 'admin.post_lecturer_manage_result','method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                            @foreach($courses as $course)
                            <tr class="text-center">
                                <td>{{ $sn++ }}</td>
                                <td nowrap="nowrap">{{ $course->students_student_id }}</td>
                                <td nowrap="nowrap">{{ studentNameFromMatriculationNo($course->students_student_id) }}</td>
                                @if(isCourseResultFinalized($course_id,$session_id,$semester_id))
                                <td>{{ $course->ca }}</td>
                                <td>{{ $course->exam }}</td>
                                <td>
                                    {{--*/ $total = $course->ca + $course->exam /*--}}
                                    {{ roundNumberUp($total) }}
                                </td>
                                <td>
                                    {{ expandGrade($total) }}
                                </td>
                                @else
                                    <td width="10%">
                                        <div class="row">
                                            <div class="col-md-10 col-xs-10 col-md-offset-1">
                                                {!! Form::input('text','ca[]', $course->ca <= 40 ? $course->ca : 0,['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </td>
                                    <td width="10%">
                                        <div class="row">
                                            <div class="col-md-10 col-xs-10 col-md-offset-1">
                                                {!! Form::input('text','exam[]', $course->exam <= 60 ? $course->exam : 0,['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </td>
                                    {!! Form::hidden('student_id[]', $course->students_student_id) !!}
                                    {!! Form::hidden('alternate_entry', $alt_entry) !!}
                                @endif
                            </tr>
                            @endforeach
                            @if(! isCourseResultFinalized($course_id,$session_id,$semester_id))
                                {!! Form::hidden('course_id', $course_id) !!}
                                {!! Form::hidden('session_id', $session_id) !!}
                                {!! Form::hidden('semester_id', $semester_id) !!}
                                <tr>
                                    <td colspan="5">
                                        <div class="form-group">
                                            <a href="javascript:;" data-toggle="modal" data-target="#finalizeResultModal" class="btn btn-danger">
                                                <span class="glyphicon glyphicon-send"></span>
                                                Finalize Submission
                                            </a>
                                            <button type="submit" class="btn btn-primary pull-right">
                                                <span class="glyphicon glyphicon-floppy-disk"></span>
                                                Save
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                <tr class="hidden-print">
                                    <td colspan="7">
                                        <p class="text-right">
                                            <a href="{{ route('admin.lecturer_export_result',[encryptId($course_id),encryptId($session_id),encryptId($semester_id),'csv']) }}" class="btn btn-primary">
                                                <span class="glyphicon glyphicon-download"></span>
                                                Export CSV
                                            </a>
                                            <a href="{{ route('admin.lecturer_export_result',[encryptId($course_id),encryptId($session_id),encryptId($semester_id),'xls']) }}" class="btn btn-success">
                                                <span class="glyphicon glyphicon-download"></span>
                                                Export Excel
                                            </a>
                                            <a href="{{ route('admin.lecturer_export_result',[encryptId($course_id),encryptId($session_id),encryptId($semester_id),'pdf']) }}" class="btn btn-danger">
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
                                <td colspan="5"><p class="text-center">No student has registered for this course.</p></td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
            @include('bhu._partials._finalize_submission_warning_modal')
        </div>
    </div>
</div>
@endsection
