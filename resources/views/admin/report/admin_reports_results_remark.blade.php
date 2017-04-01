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
                        DEPARTMENT OF {{ changeStringToUpperCase(expandProgram($deptId)) }}<br>
                        {{ expandLevel($levelId) }} LEVEL (GRADE POINT AND REMARK PAGE) FOR CATEGORY A STUDENTS<br>
                        {{ changeStringToUpperCase(expandSemester($semesterId)) }} SEMESTER {{ $sessionId }} ACADEMIC SESSION<br>
                        {{ departmentalDegreeTitle($deptId) }}<br>
                        {{--APPROVAL LEVEL: {{ changeStringToUpperCase(session('role')) }}--}}
                    </strong>
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <div class="table-responsive">
                            <p class="text-right hidden-print">
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
                                    @foreach($registeredStudents as $registeredStudent)
                                    <tr>
                                        <td>{{ $sn++ }}</td>
                                        <td nowrap="nowrap">{{ expandStudentName($registeredStudent->regno) }}</td>
                                        <td>{{ $registeredStudent->regno }}</td>
                                        <td class="text-center">
                                            {{-- */$cur = getCurrentUnits($registeredStudent->regno, true);/* --}}
                                            {{ $cur }}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$cue = getCurrentUnits($registeredStudent->regno, false, true);/* --}}
                                            {{ $cue }}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$wgp = getWeightedGradePoint($registeredStudent->regno);/* --}}
                                            {{ $wgp }}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$gpa = getGradePointAverage($wgp, $cur);/* --}}
                                            {{ $gpa }}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$ptur = getPreviousTotalUnit($registeredStudent->regno, true);/* --}}
                                            {{ $ptur }}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$ptue = getPreviousTotalUnit($registeredStudent->regno, false, true);/* --}}
                                            {{ $ptue }}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$ptwgp = getWeightedGradePoint($registeredStudent->regno, true);/* --}}
                                            {{ $ptwgp }}
                                        </td>
                                        <td class="text-center">
                                            {{-- */$pgpa = getGradePointAverage($ptwgp, $ptur);/* --}}
                                            {{ $pgpa }}
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
                                        </td>
                                        <td class="text-center">
                                            {{-- */$cgpa = getGradePointAverage($twgp, $tur);/* --}}
                                            {{ $cgpa }}
                                        </td>
                                        <td>
                                            {{ getRemarkCarryOvers($registeredStudent->regno) }}
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

