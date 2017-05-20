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
                        {{ expandLevel($levelId) }} LEVEL (DETAILED RESULT PAGE)<br>
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
                                @if(($approval == 'HOD' && (session('role') == 'Dean' or session('role') == 'Senate')) or ($approval == 'Dean' && (session('role') == 'Senate')) )
                                    <a href="{{ route('admin.report_finalize_level_result',[encryptId($deptId), encryptId($sessionId), encryptId($semesterId), encryptId($levelId)]) }}" class="btn btn-success">Finalize Result</a>
                                @endif
                                <a href="javascript:;" onclick="window.print();" class="btn btn-primary"><span class="glyphicon glyphicon-print"></span> Print</a>
                                <a href="{{ route('admin.get_detailed_reports') }}" class="btn btn-danger">Back</a>
                            </p>
                            <table class="table table-bordered table-condensed">
                                @if(count($registeredStudents) > 0)
                                    <tr>
                                        <th>S/No</th>
                                        <th>Name</th>
                                        <th>Matric. No</th>
                                        @if(count($headerCourses) > 0)
                                            @foreach($headerCourses as $course)
                                                <th class="text-center">
                                                    {{ $course }}<br>
                                                    [{{ courseTitleAndUnits($course, true) }}]<br>
                                                    [S-G-P]
                                                </th>
                                            @endforeach
                                        @endif

                                    </tr>
                                    @foreach($registeredStudents as $student)
                                    <tr>
                                        <td>{{ $sn++ }}</td>
{{--                                        <td nowrap="nowrap">{{ expandStudentName($student) }}</td>--}}
                                        <td nowrap="nowrap">{{ changeStringToUpperCase($student->surname) . ', ' . changeStringToTitleCase($student->firstname) }}</td>
                                        <td>
                                            {{ $student->regno }}
                                        </td>
                                        @foreach($headerCourses as $course)
                                            <td class="text-center">{{ courseResultStringFormat($student->regno, $course, $sessionId, $semesterId) }}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td><p>No Result(s) to display, kindly click <strong><a href="{{ route('admin.get_detailed_reports') }}">Back</a></strong> and check your selection criteria!</p></td>
                                    </tr>
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

