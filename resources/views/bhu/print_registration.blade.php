@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-xs-12">

            @include('bhu._partials._alert')
            @if(! isCourseRegistrationEnabled())
             <div class="panel panel-default">
                <div class="panel-body">
                    <h3 class="text-danger text-capitalize">Course Registration has been Disabled, Please contact the Academic Office!</h3>
                </div>
            </div>
            @else
             <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-2 col-xs-2 panel-divider">
                                <p class="text-center">
                                    <img class="bhulogo" src="{{ prepareAppUrl() }}/images/bhu-logo.png" alt="Logo">
                                </p>
                            </div>
                            <div class="col-md-10 col-xs-10">
                                <div class="bhu-txt-margin">
                                    <span class="bhu-main-txt"><strong>BINGHAM UNIVERSITY</strong></span>
                                    <span class="bhu-slug-txt">Student's Course Registration Form</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="row row-margin-top">
                        <div class="col-md-8 col-xs-8">
                            <div class="panel panel-default  panel-custom-bg">
                                <div class="panel-heading">
                                    <strong>Student Registration Details</strong>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-bordered table-condensed">
                                        <tr>
                                            <td nowrap="nowrap">Matriculation Number</td>
                                            <td>{{ session('regno') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Name</td>
                                            <td>{{ changeStringToTitleCase(session('firstname')) . ' ' . changeStringToTitleCase(session('middlename')) . ' ' . changeStringToUpperCase(session('surname')) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Registration Level</td>
                                            <td>{{ expandLevel(session('levelid')) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Registration Session</td>
                                            <td>{{ currentAcademicSession() }}</td>
                                        </tr>
                                        <tr>
                                            <td>Semester</td>
                                            <td>{{ expandSemester(currentSemester()) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Program</td>
                                            <td>{{ expandProgram(session('deptid')) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-md-offset-1 col-xs-3 col-xs-offset-1">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <img src="{{ preparePassportImgPath(session('picture')) }}" class="passport-img" alt="Passport">
                                </div>
                            </div>
                            <a href="javascript:;" onclick="window.print();" class="btn btn-primary btn-block hidden-print"><span class="glyphicon glyphicon-print"></span> Print</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <div class="panel panel-default panel-custom-bg">
                                <div class="panel-heading text-center">
                                    <strong>Registered Courses for {{ currentAcademicSession() }} Session, {{ expandSemester(currentSemester()) }} Semester</strong>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-condensed table-bordered text-center panel-table-custom">
                                        <tr>
                                            <th class="text-center">S/No.</th>
                                            <th class="text-center">Course Code</th>
                                            <th class="text-center">Course Title</th>
                                            <th class="text-center">Course Units</th>
                                            <th class="text-center">Signature</th>
                                        </tr>
                                        @if(count($courses) > 0)
                                        @foreach($courses as $course)
                                            <tr>
                                                <td>{{ $sn++ }}</td>
                                                <td>{{ $course->courses_course_id }}</td>
                                                <td nowrap="nowrap">{{ changeStringToTitleCase(courseTitleAndUnits($course->courses_course_id)) }}</td>
                                                <td>{{ courseTitleAndUnits($course->courses_course_id,true) }}</td>
                                                <td nowrap="nowrap">&nbsp;</td>
                                                <?php $units = $units + courseTitleAndUnits($course->courses_course_id,true); ?>
                                            </tr>
                                        @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5">You have not registered your courses!</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                                <div class="panel-footer text-center">
                                    <strong>Total Credit Units = {{ $units }}</strong>
                                </div>
                            </div>
                            @if($units > maxRegistrationUnits())
                                <div class="alert alert-danger" role="alert">
                                    <p><strong>Total Credit Units of {{ $units }} EXCEEDS maximum allowed by the Department!!! Your registration is considered INVALID</strong></p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-xs-3">
                            <div class="panel remove-panel-border">
                                <div class="panel-body panel-xs-padding">
                                    <br>
                                    <span class="signature-date small"><strong>Sign & Date of Student</strong></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-4 pull-right">
                            <div class="panel remove-panel-border">
                                <div class="panel-body panel-xs-padding text-right">
                                    <br>
                                    <span class="signature-date small"><strong>Sign & Date of Level Coordinator</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-xs-4">
                            <div class="panel remove-panel-border">
                                <div class="panel-body panel-xs-padding">
                                    <br>
                                    <span class="signature-date small"><strong>Sign & Date of Head of Department</strong></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 col-xs-5 pull-right">
                            <div class="panel remove-panel-border">
                                <div class="panel-body panel-xs-padding-2 text-right">
                                    <br>
                                    <span class="signature-date small"><strong>Sign & Date of Provost/Dean of College Faculty</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-xs-4">
                            <div class="panel panel-default panel-custom small">
                                <div class="panel-heading">
                                    <strong>Copies to be sent to:</strong>
                                </div>
                                <div class="panel-body">
                                    <ol class="list-unstyled">
                                        <li>1. Department</li>
                                        <li>2. The College/Faculty</li>
                                        <li>3. Academic Office</li>
                                        <li>4. Last copy to be retained by Student</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-3 pull-right">
                            <div class="panel remove-panel-border">
                                <div class="panel-body panel-xs-padding text-right">
                                    <br>
                                    <span class="signature-date small"><strong>Sign & Date of Registrar</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
