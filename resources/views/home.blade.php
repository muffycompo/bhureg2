@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    <p>Welcome <strong>{{ changeStringToUpperCase(session('firstname') . ' ' . session('surname')) }}</strong> to your Dashboard, you can Register and view your Results!</p>
                    <p><strong>Department: {{ expandProgram(session('deptid')) }}</strong></p>
                    <p><strong>Current Level: {{ expandLevel(session('levelid')) }}</strong></p>
                    <p><strong>Academic Session: {{ currentAcademicSession() }}</strong></p>
                    <p><strong>Semester: {{ expandSemester(currentSemester()) }}</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
