<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Bingham University Student Registration Portal</title>

    <!-- Styles -->
{!!  Html::style('/css/app.css') !!}
{!!  Html::style('/css/custom.css') !!}

</head>
<body>
<div id="app">
    <div class="container-fluid">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-center">
                    <strong>
                        BINGHAM UNIVERSITY<br>
                        {{ changeStringToUpperCase(expandFacultyFromDepartment($deptId)) }}<br>
                        DEPARTMENT OF {{ changeStringToUpperCase(expandDepartment($deptId)) }}<br>
                        {{ expandLevel($levelId) }} LEVEL (SUMMARY PAGE)<br>
                        {{ changeStringToUpperCase(expandSemester($semesterId)) }} SEMESTER {{ $sessionId }} ACADEMIC SESSION<br>
                        {{ departmentalDegreeTitle($deptId) }}<br>
                        {{-- */$approval = levelApprovalStatus($deptId, $sessionId, $semesterId, $levelId, true);/* --}}
                        {{ levelApprovalStatus($deptId, $sessionId, $semesterId, $levelId) }}
                    </strong>
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <p class="text-right hidden-print">
                            @if((is_null($approval) && session('role') == 'HOD') or ($approval == 'HOD' && (session('role') == 'Dean' or session('role') == 'Senate')) or ($approval == 'Dean' && (session('role') == 'Senate')) )
                                <a href="{{ route('admin.report_finalize_level_result',[encryptId($deptId), encryptId($sessionId), encryptId($semesterId), encryptId($levelId)]) }}" class="btn btn-success">Finalize Result</a>
                            @endif
                            <a href="javascript:;" onclick="window.print();" class="btn btn-primary"><span class="glyphicon glyphicon-print"></span> Print</a>
                            <a href="{{ route('admin.get_detailed_reports') }}" class="btn btn-danger">Back</a>
                        </p>
                        <div class="row">
                            <div class="col-md-8 col-xs-8">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-condensed">
                                        @if(count($registeredStudents) > 0)
                                            <tr>
                                                <th class="text-center" colspan="4">A<br>Courses</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">S/No</th>
                                                <th class="text-center">Course ID</th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Unit</th>
                                                @foreach($registeredStudents as $student)
                                                    {{-- */$maxWidth[] = sizeof($registeredStudents);/* --}}
                                                    @if(! hasStudentRegisteredAnyCourseInSessionSemester($student->regno, $sessionId, $semesterId))
                                                    {{-- */$notRegisteredArray[] = $student->regno;/* --}}
                                                    @endif
                                                @endforeach
                                            </tr>
                                            @if(count($headerCourses) > 0)

                                                @foreach($headerCourses as $course)
                                                    <tr class="text-center">
                                                        <td>{{ $sn++ }}</td>
                                                        <td>{{ $course }}</td>
                                                        <td>{{ courseTitleAndUnits($course) }}</td>
                                                        <td>{{ courseTitleAndUnits($course, true) }}</td>
                                                    </tr>
                                                @endforeach

                                            @else
                                                <tr>
                                                    <td colspan="4">No Courses to Display!</td>
                                                </tr>
                                            @endif
                                        @endif
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4 col-xs-4">
                                <table class="table table-bordered table-condensed">
                                    <tr>
                                        <th class="text-center" colspan="4">B<br>Keys</th>
                                    </tr>
                                    <tr>
                                        <td class="text-center">CUR</td>
                                        <td class="text-center">=</td>
                                        <td>CREDIT UNITS REGISTERED</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">CUE</td>
                                        <td class="text-center">=</td>
                                        <td>CREDIT UNITS EARNED</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">WGP</td>
                                        <td class="text-center">=</td>
                                        <td>WEIGHTED GRADE POINTS</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">GPA</td>
                                        <td class="text-center">=</td>
                                        <td>GRADE POINTS AVERAGE</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">TCE</td>
                                        <td class="text-center">=</td>
                                        <td>TOTAL CREDITS EARNED</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">TWGP</td>
                                        <td class="text-center">=</td>
                                        <td>TOTAL WEIGHTED GRADE POINTS</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">TCR</td>
                                        <td class="text-center">=</td>
                                        <td>TOTAL CREDITS REGISTERED</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">CGPA</td>
                                        <td class="text-center">=</td>
                                        <td>CUMULATIVE GRADE POINTS AVERAGE</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">X</td>
                                        <td class="text-center">=</td>
                                        <td>NOT REGISTERED</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-xs-4">
                                @if(count($headerCourses) > 0)
                                <table class="table table-condensed table-bordered">
                                    <tr>
                                        <th class="text-center" colspan="4">C<br>Summary</th>
                                    </tr>
                                    <tr>
                                        {{-- */$totalCandidates = count($registeredStudents);/* --}}
                                        <td>Total No. of Candidates</td>
                                        <td class="text-center">=</td>
                                        <td class="text-center">{{ count($registeredStudents) }}</td>
                                        <td class="text-center"></td>
                                    </tr>
                                    <tr>
                                        {{-- */$totalCandidatesExamined = count($registeredStudents) - count($notRegisteredArray);/* --}}
                                        {{-- */$totalCandidatesExaminedPercent = ($totalCandidatesExamined / $totalCandidates) * 100;/* --}}
                                        <td>Total No. of Candidates Examined</td>
                                        <td class="text-center">=</td>
                                        <td class="text-center">{{ $totalCandidatesExamined }}</td>
                                        <td class="text-center">{{ round($totalCandidatesExaminedPercent) }}%</td>
                                    </tr>
                                    <tr>
                                        {{-- */$carryOver = 0;/* --}}
                                        @if(count($registeredStudents) > 0)
                                            @foreach($registeredStudents as $student)
                                                {{-- */$NoOfCo = getSummaryCarryOversPass($student->regno, $sessionId);/* --}}
                                                {{-- */$carryOver = $carryOver + $NoOfCo;/* --}}
                                            @endforeach
                                        @endif
                                        {{-- */$pass = $totalCandidatesExamined - $carryOver;/* --}}
                                        {{-- */$passPercent = ($pass / $totalCandidatesExamined) * 100;/* --}}
                                        <td>Total No. of Candidates with Pass</td>
                                        <td class="text-center">=</td>
                                        <td class="text-center">{{ $pass }}</td>
                                        <td class="text-center">{{ round($passPercent) }}%</td>
                                    </tr>
                                    <tr>
                                        {{-- */$carryOverPercent = ($carryOver / $totalCandidatesExamined) * 100;/* --}}
                                        <td>Total No. of Candidates with Carry Over</td>
                                        <td class="text-center">=</td>
                                        <td class="text-center">{{ $carryOver }}</td>
                                        <td class="text-center">{{ round($carryOverPercent) }}%</td>
                                    </tr>
                                    <tr>
                                        {{-- */$notRegistered = count($notRegisteredArray);/* --}}
                                        {{-- */$notRegisteredPercent = ($notRegistered / $totalCandidates) * 100;/* --}}
                                        <td>Total No. of Candidates NOT Registered</td>
                                        <td class="text-center">=</td>
                                        <td class="text-center">{{ $notRegistered }}</td>
                                        <td class="text-center">{{ round($notRegisteredPercent) }}%</td>
                                    </tr>
                                </table>
                                @endif
                            </div>
                            <div class="col-md-12 col-xs-12"><hr></div>
                            <div class="col-md-12 col-xs-12">
                                <div class="row">
                                    <div class="col-md-6 col-xs-6">
                                        <p><span class="signature-title"><strong>Dean&apos;s Name, Signature/Date: </strong><span class = "signature-line"></span></span></p>
                                    </div>
                                    <div class="col-md-6 col-xs-6">
                                        <p><span class="signature-title"><strong>HOD&apos;s Name, Signature/Date: </strong><span class = "signature-line"></span></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</body>
</html>

