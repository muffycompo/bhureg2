@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Admin Staff Dashboard</div>

                <div class="panel-body">
                    <p>Welcome to your Dashboard, as a <strong>{{ session('role') == 'Transcript' ? 'Transcript Officer' : session('role') }}</strong>, you can perform Administrative task!</p>
                    @if(session('role') == 'Lecturer' or session('role') == 'HOD' or session('role') == 'Transcript')
                        <p><strong>Department: {{ expandProgram(session('departments_department_id')) }}</strong></p>
                    @endif
                    <p><strong>Role: {{ session('role') }}</strong></p>
                    <p><strong>Academic Session: {{ currentAcademicSession() }}</strong></p>
                    <p><strong>Semester: {{ expandSemester(currentSemester()) }}</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
