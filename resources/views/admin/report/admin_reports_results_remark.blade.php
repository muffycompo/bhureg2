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
                        {{ expandLevel($levelId) }} LEVEL (GRADE POINT AND REMARK PAGE)<br>
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
                        <div class="table-responsive">
                            <p class="text-right hidden-print">
                                @if((is_null($approval) && session('role') == 'HOD') or ($approval == 'HOD' && (session('role') == 'Dean' or session('role') == 'Senate')) or ($approval == 'Dean' && (session('role') == 'Senate')) )
                                    <a href="{{ route('admin.report_finalize_level_result',[encryptId($deptId), encryptId($sessionId), encryptId($semesterId), encryptId($levelId)]) }}" class="btn btn-success">Finalize Result</a>
                                @endif
                                <a href="javascript:;" onclick="window.print();" class="btn btn-primary"><span class="glyphicon glyphicon-print"></span> Print</a>
                                <a href="{{ route('admin.get_detailed_reports') }}" class="btn btn-danger">Back</a>
                            </p>
                            <table class="table table-bordered table-condensed">
                                @if(count($registeredStudents) > 0)
                                    <tr class="text-center">
                                        <th>S/No</th>
                                        <th>Name</th>
                                        <th>Matric. No</th>
                                        <th>CUR</th>
                                        <th>CUE</th>
                                        <th>WGP</th>
                                        <th>GPA</th>
                                        <th>PTUR</th>
                                        <th>PTUE</th>
                                        <th>PTWGP</th>
                                        <th>PGPA</th>
                                        <th>TUR</th>
                                        <th>TUE</th>
                                        <th>TWGP</th>
                                        <th>CGPA</th>
                                        <th>Remark</th>
                                    </tr>
                                    @foreach($registeredStudents as $student)
                                    <tr>
                                        <td>{{ $sn++ }}</td>
                                        <td nowrap="nowrap">{{ changeStringToUpperCase($student->surname) . ', ' . changeStringToTitleCase($student->firstname) }}</td>
                                        <td>{{ $student->regno }}</td>
                                        <td class="text-center">
                                            {{-- */$cur = studentCurrentUnitsRegistered($student->regno,$sessionId,$semesterId);/* --}}
                                            {{ $cur }}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$cue = studentCurrentUnitsEarned($student->regno,$sessionId,$semesterId);/* --}}
                                            {{ $cue }}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$wgp = studentCurrentWGP($student->regno,$sessionId,$semesterId);/* --}}
                                            {{ $wgp }}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$gpa = studentCurrentGPA($student->regno,$sessionId,$semesterId);/* --}}
                                            {{ number_format($gpa,2) }}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$ptur = studentPreviousTotalUnitRegistered($student->regno,$sessionId,$semesterId);/* --}}
                                            {{ $ptur }}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$ptue = studentPreviousTotalUnitsEarned($student->regno,$sessionId,$semesterId);/* --}}
                                            {{ $ptue }}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$ptwgp = studentPreviousTotalWGP($student->regno,$sessionId,$semesterId);/* --}}
                                            {{ $ptwgp }}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$pgpa = studentPreviousTotalGPA($ptwgp, $ptur);/* --}}
                                            {{ number_format($pgpa,2) }}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$tur = $cur + $ptur;/* --}}
                                            {{ $tur }}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$tue = $cue + $ptue;/* --}}
                                            {{ $tue }}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$twgp = $wgp + $ptwgp;/* --}}
                                            {{ $twgp }}
                                            {{--{{ studentTotalWGP($student->regno) }}--}}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$cgpa = studentTotalCGPA($twgp, $tur);/* --}}
                                            {{ number_format($cgpa,2) }}
{{--                                            {{ studentCGPA($student->regno) }}--}}
                                        </td>
                                        <td>
                                            {{ getRemarkCarryOvers($student->regno, $sessionId, $semesterId) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</body>
</html>

