@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">

            @include('bhu._partials._alert')

            <div class="panel panel-default">
                <div class="panel-heading">Manage Result Adjustments</div>

                <div class="panel-body">
                    <table class="table table-bordered table-condensed">
                        <tr>
                            <th class="text-center">Code</th>
                            <th class="text-center">Title</th>
                            <th class="text-center">Unit</th>
                            <th class="text-center">Lecturer Name</th>
                            <th class="text-center">Semester</th>
                            <th class="text-center">Action</th>
                        </tr>
                        @if(count($adjustments) > 0)
                            @foreach($adjustments as $adjustment)
                                <tr class="text-center">
                                    <td>{{ $adjustment->courses_course_id }}</td>
                                    <td>{{ courseTitleAndUnits($adjustment->courses_course_id) }}</td>
                                    <td>{{ courseTitleAndUnits($adjustment->courses_course_id,true) }}</td>
                                    <td><strong>{{ changeStringToTitleCase($adjustment->first_name) . ' ' . changeStringToTitleCase($adjustment->last_name) }}</strong></td>
                                    <td>{{ expandSemester($adjustment->semester) }}</td>
                                    <td>
                                        {{--<a href="javascript:;" data-toggle="modal" data-target="#hodUnassignCourseModal" class="text-danger">--}}
                                        <a href="{{ route('admin.hod_manage_result_adjustment',[encryptId($adjustment->courses_course_id),encryptId($adjustment->user_id),$adjustment->semester]) }}" class="text-info" data-toggle="tooltip" data-placement="top" alt="Adjust Result" title="Adjust Result">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="text-center">
                                <td colspan="6">
                                    {!! $adjustments->render() !!}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="6">No Result(s) has been submitted for Adjustment.</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
