@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">

            @include('bhu._partials._alert')

            <div class="row">
                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5>Student Semester Results</h5>
                        </div>
                        <div class="panel-body">
                            <table class="table table-bordered table-condensed">
                                <tr>
                                    <td nowrap="nowrap">Matriculation Number</td>
                                    <td>{{ session('regno') }}</td>
                                </tr>
                                <tr>
                                    <td>Name</td>

                                    <td>{{ expandStudentName(session('regno')) }}</td>
{{--                                    <td>{{ changeStringToTitleCase(session('firstname')) . ' ' . changeStringToUpperCase(session('surname')) }}</td>--}}
                                </tr>
                                <tr>
                                    <td>Level</td>
                                    <td>{{ expandLevel(session('levelid')) }}</td>
                                </tr>
                                <tr>
                                    <td>Session</td>
                                    <td>{{ $sessionId }}</td>
                                </tr>
                                <tr>
                                    <td>Semester</td>
                                    <td>{{ expandSemester($semesterId) }}</td>
                                </tr>
                                <tr>
                                    <td>Department</td>
                                    <td>{{ expandProgram(session('deptid')) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 hidden-print">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5>Search Results</h5>
                        </div>

                        <div class="panel-body">
                            {!! Form::open(['route' => 'get.results', 'method' => 'GET', 'role' => 'form']) !!}
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label for="session_id">Session</label>
                                            {!! Form::select('session_id',resultSessionDropdownOptions(),$sessionId,['class' => 'form-control', 'id' => 'session_id']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="semester">Semester</label>
                                            {!! Form::select('semester',semesterDropdownOptions(),$semesterId,['class' => 'form-control', 'id' => 'semester']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary" style="margin-bottom: 10px;">
                                            <span class="glyphicon glyphicon-search"></span>
                                            View Results
                                        </button>
                                    </div>
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($results) && count($results) > 0)
            <div class="panel panel-default">
                <div class="panel-body" style="margin-top: 10px;">

                        <div class="row">
                            @if(count($results) > 0)
                                <div class="col-md-2 col-xs-4 col-md-offset-10 hidden-print">
                                    <p>
                                        <a href="javascript:;" onclick="window.print();" class="btn btn-success btn-block"><span class="glyphicon glyphicon-print"></span> Print</a>
                                    </p>
                                </div>
                            @endif
                            <div class="col-md-12 col-xs-12">
                                <table class="table table-condensed table-bordered">
                                    <tr>
                                        <th class="text-center">S/N</th>
                                        <th class="text-center">Code</th>
                                        <th class="text-center">Title</th>
                                        <th class="text-center">Unit</th>
                                        <th class="text-center">Score</th>
                                        <th class="text-center">Grade</th>
                                    </tr>
                                    @if(count($results) > 0)
                                        @foreach($results as $result)
                                            <tr class="text-center">
                                                <td>{{ $sn++ }}</td>
                                                <td>{{ $result->courses_course_id }}</td>
                                                <td>{{ courseTitleAndUnits($result->courses_course_id) }}</td>
                                                <td>{{ courseTitleAndUnits($result->courses_course_id, true) }}</td>
                                                {{-- */$total = $result->ca + $result->exam;/* --}}
                                                <td>{{ $total }}</td>
                                                <td>{{ expandGrade($total) }}</td>

                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="text-center">
                                            <td colspan="6">Your <strong>{{ expandSemester($semesterId) }}</strong> semester Result(s) has not been approved for the <strong>{{ $sessionId }}</strong> Academic Session!</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body" style="margin-top: 10px;">
                    <table class="table table-bordered table-condensed">
                        <tr class="text-center">
                            <td><strong>GPA: {{ number_format($studentGPA,2) }}</strong></td>
                            <td><strong>Current CGPA: {{ number_format($studentCGPA,2) }}</strong></td>
                            <td><strong>Class of Degree: <em>{{ getClassOfDegree($studentCGPA) }}</em></strong></td>
                        </tr>
                        <tr class="hidden-print">
                            <td colspan="3">
                                <div class="alert alert-danger" style="margin-bottom: 0;">
                                    <p>
                                        <span class="glyphicon glyphicon-warning-sign"></span> <strong>NOTE</strong><br>
                                        <h6>The Grade Point Average (GPA) and Cumulative Grade Point Average (CGPA) shown above is not an accurate representation of All your results. Only Results that have <strong>SENATE</strong> approval are displayed and used to calculate your GPA and CGPA. Your detailed results can be obtained from your Current Department.</h6>
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            @elseif(request()->has('session_id') && request()->has('semester'))
                <div class="panel panel-default">
                    <div class="panel-body" style="margin-top: 10px;">

                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <table class="table table-condensed table-bordered">
                                    <tr class="text-center">
                                        <td colspan="6">Your <strong>{{ expandSemester($semesterId) }}</strong> semester Result(s) has not been approved for the <strong>{{ $sessionId }}</strong> Academic Session!</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
