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
                        {{ expandLevel($levelId) }} LEVEL (DETAILED RESULT PAGE) FOR CATEGORY A STUDENTS<br>
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
                                @if(count($registeredCourses) > 0)
                                    <tr>
                                        <th>S/No</th>
                                        <th>Name</th>
                                        <th>Matric. No</th>
                                        @foreach($registeredCourses as $courses)
                                            {{-- */$maxWidth[] = sizeof($courses);/* --}}
                                            @foreach($courses as $course)
                                                @if(! array_key_exists($course->courses_course_id,$headerCourses))
                                                    {{-- */$headerCourses[$course->courses_course_id] = $course;/* --}}
                                                @endif
                                            @endforeach
                                        @endforeach
                                        @if(count($headerCourses) > 0)
                                            @foreach($headerCourses as $course_id => $value)
                                                <th class="text-center">
                                                    {{ $course_id }}<br>
                                                    [{{ courseTitleAndUnits($course_id, true) }}]<br>
                                                    [S-G-P]
                                                </th>
                                            @endforeach
                                        @endif

                                    </tr>
                                    @foreach($registeredCourses as $regno => $courses)
                                    <tr>
                                        <td>{{ $sn++ }}</td>
                                        <td nowrap="nowrap">{{ expandStudentName($regno) }}</td>
                                        <td>
                                            {{ $regno }}
                                        </td>
                                        @foreach($headerCourses as $course_id => $value)
                                            <td class="text-center">{{ courseResultStringFormat($regno, $course_id, $sessionId, $semesterId) }}</td>
                                        @endforeach
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

