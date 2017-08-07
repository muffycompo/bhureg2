@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">

            @include('bhu._partials._alert')

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row" style="margin-bottom: 1.2em;">
                                <div class="col-md-10 col-md-offset-1 text-center clearfix">
                                    <img src="{{ asset('images/bhu-logo.png') }}" alt="Bingham University Logo" class="pull-left" style="height: 110px; margin-top: 15px;">
                                    <p class="pull-right">
                                        <h3><strong>BINGHAM UNIVERSITY</strong></h3>
                                        <h4>
                                            OFFICE OF THE REGISTRAR<br>
                                            ACADEMIC DIVISION
                                        </h4>
                                        <h6>
                                            <strong>E-mail: examsandrecords@binghamuni.edu.ng</strong>
                                        </h6>
                                    </p>
                                    <hr style="margin-bottom: 1em;">
                                </div>
                            </div>
                            {{--<div class="row">--}}
                                {{--.col-md-1--}}
                            {{--</div>--}}
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-10 col-md-offset-1">
                                    <h4 class="text-center"><strong style="text-decoration: underline;">ACADEMIC TRANSCRIPT</strong></h4>
                                    @if(count($student) > 0)
                                        <table class="table-responsive table-condensed" style="font-size: 0.92em;">
                                        <tr>
                                            <td nowrap="nowrap">Name (Surname Last):</td>
                                            <td nowrap="nowrap"><span class="text-uppercase"><strong>{{ $student->firstname . ' ' . $student->middlename . ' ' . $student->surname }}</strong></span></td>
                                            <td nowrap="nowrap">Reg No.:</td>
                                            <td nowrap="nowrap"><span class="text-uppercase"><strong>{{ $student->regno }}</strong></span></td>
                                        </tr>
                                        <tr>
                                            <td nowrap="nowrap">Sex (Male/Female):</td>
                                            <td nowrap="nowrap"><span class="text-uppercase"><strong>{{ expandGender($student->sexid) }}</strong></span></td>
                                            <td nowrap="nowrap">Date Left:</td>
                                            <td nowrap="nowrap"><span class="text-uppercase"><strong>{{ transcriptEndDate($student->regno) }}</strong></span></td>
                                        </tr>
                                        <tr>
                                            <td nowrap="nowrap">Date in This University (Entered):</td>
                                            <td nowrap="nowrap"><span class="text-uppercase"><strong>{{ transcriptStartDate($student->regno) }}</strong></span></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td nowrap="nowrap">Reason for Leaving:</td>
                                            <td nowrap="nowrap"><span class="text-uppercase"><strong>{{ graduateStatus($student->grad_status,$student->levelid) }}</strong></span></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td nowrap="nowrap">Faculty:</td>
                                            <td nowrap="nowrap"><span class="text-uppercase"><strong>{{ expandFacultyFromDepartment($student->deptid) }}</strong></span></td>
                                            <td nowrap="nowrap">Department:</td>
                                            <td nowrap="nowrap"><span class="text-uppercase"><strong>{{ expandDepartment($student->deptid) }}</strong></span></td>
                                        </tr>
                                        <tr>
                                            <td nowrap="nowrap">Course of Study/Specialization:</td>
                                            <td nowrap="nowrap"><span class="text-uppercase"><strong>{{ expandProgramSpecialization($student->deptid) }}</strong></span></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </table>
                                    @endif
                                    @if(count($transcriptResults) > 0)
                                        @foreach($transcriptResults as $session => $studentResults)
                                            <table class="table table-responsive table-condensed table-bordered" style="font-size: 0.92em;">
                                                <tr>
                                                    <th class="text-center">YEAR OF STUDY</th>
                                                    <th class="text-center">COURSE CODE</th>
                                                    <th class="text-center">COURSE TITLE</th>
                                                    <th class="text-center">CREDIT UNIT</th>
                                                    <th class="text-center">GRADE POINT</th>
                                                    <th class="text-center">REMARKS</th>
                                                </tr>
                                                @foreach($studentResults as $result)
                                                    <tr class="text-center">
                                                        <td>{{ $session }}</td>
                                                        <td>{{ $result->courses_course_id }}</td>
                                                        <td>{{ courseTitleAndUnits($result->courses_course_id) }}</td>
                                                        {{-- */$unitPoints = courseTitleAndUnits($result->courses_course_id,true);/* --}}
                                                        {{-- */$units = $units + $unitPoints;/* --}}
                                                        <td>{{ $unitPoints }}</td>
                                                        {{-- */$score = $result->ca + $result->exam;/* --}}
                                                        @if(isOldGradable($result->students_student_id))
                                                            {{-- */$gradeUnitPoints = expandGrade($score,true,true);/* --}}
                                                            {{-- */$grade_point = $grade_point + $gradeUnitPoints;/* --}}
                                                            <td>{{ $gradeUnitPoints }}</td>
                                                        @else
                                                            {{-- */$gradeUnitPoints = expandGrade($score,false,true);/* --}}
                                                            {{-- */$grade_point = $grade_point + $gradeUnitPoints;/* --}}
                                                            <td>{{ $gradeUnitPoints }}</td>
                                                        @endif
                                                        @if($gradeUnitPoints != 0)
                                                            {{-- */$units_earned = $units_earned + $unitPoints;/* --}}
                                                        @endif
                                                        <td>{{ expandTranscriptGradeRemark($score, $result->students_student_id) }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                            <table class="table-condensed table-responsive" style="font-weight: bold; margin-bottom: 10px; margin-top: 0;">
                                                <tr>
                                                    <td width="25%" nowrap="nowrap">TOTAL CREDIT REGISTERED:</td>
                                                    <td width="25%">{{ $units }}</td>
                                                    <td width="25%" nowrap="nowrap">TOTAL CREDIT EARNED:</td>
                                                    <td width="25%">{{ $units_earned }}</td>
                                                </tr>
                                                <tr>
                                                    <td width="25%" nowrap="nowrap">TOTAL GRADE POINT:</td>
                                                    <td width="25%">{{ $grade_point }}</td>
                                                    <td width="25%" nowrap="nowrap">GPA:</td>
                                                    <td width="25%">{{ number_format(getGradePointAverage($grade_point,$units),2) }}</td>
                                                </tr>
                                            </table>
                                            {{-- */$grade_point = 0;/* --}}
                                            {{-- */$units_earned = 0;/* --}}
                                            {{-- */$units = 0;/* --}}
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
