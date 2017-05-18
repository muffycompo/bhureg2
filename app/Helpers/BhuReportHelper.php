<?php

function resultsReport($input){
    if($input->has('level_id')) $levelId = $input->get('level_id');
    if($input->has('department_id') && (session('role') == 'Dean' or session('role') == 'Senate')){
        $deptId = $input->get('department_id');
    } else {
        $deptId = session('departments_department_id');
    }
    if(session('role') == 'HOD' or session('role') == 'Dean') $type = 'HOD';
    if(session('role') == 'Senate') $type = 'Dean';

    if($deptId == 'MED') { $deptId = 'MBBS'; }
    if($deptId == 'MIC') { $deptId = 'BIOS'; }
    $session = $input->get('session_id');
    $semester = $input->get('semester');

    $courses = [];

    // Get all Departmental Courses
    $departmentalCourses = DB::connection('mysql2')->table('department_courses')
                ->where('course_program', $deptId)
                ->where('session_session_id', $session)
                ->where('semester', $semester)
                ->where('course_level', $levelId)
                ->get();

    if($departmentalCourses){
        foreach ($departmentalCourses as $departmentalCourse) {
            if(lecturerHasFinalized($departmentalCourse->courses_course_id,$session, $semester)){
                $courses[] = $departmentalCourse;
            }
        }
    }

    return $courses;
}

function isCourseResultSubmitted($courseId, $session, $semester, $type){
    return DB::connection('mysql2')->table('course_registration')
                ->where('courses_course_id', $courseId)
                ->where('sessions_session_id', $session)
                ->where('semester', $semester)
//                ->where('approval_status', $type)
                ->exists();
}

function manageAdminCourseResult($courseId, $session, $semester){
    $courses = DB::connection('mysql2')->table('course_registration')
        ->where('sessions_session_id', $session)
        ->where('semester', $semester)
        ->where('courses_course_id', $courseId)
        ->orderBy('students_student_id','ASC')
        ->get();
    return $courses;
}

function isCourseResultApprovedByDeanSenate($courseId, $session, $semester){
    $type = session('role');
    if($type == 'Lecturer' or $type == 'HOD') return true;
    return DB::connection('mysql2')->table('course_registration')
        ->where('courses_course_id', $courseId)
        ->where('sessions_session_id', $session)
        ->where('semester', $semester)
        ->where('approval_status', $type)
        ->exists();
}

function manageAdminReportApproveCourseResult($courseId, $sessionId, $semesterId){
    $type = session('role');
    if($type == 'Lecturer' or $type == 'HOD') return true;
    return DB::connection('mysql2')->table('course_registration')
        ->where('courses_course_id', $courseId)
        ->where('sessions_session_id', $sessionId)
        ->where('semester', $semesterId)
        ->update(['approval_status' => $type]);
}

function manageAdminDetailedResultsReports($deptId, $sessionId, $semesterId, $levelId, $regCourses = false){
    if(session('role') == 'HOD' or session('role') == 'Dean') $type = 'HOD';
    if(session('role') == 'Senate') $type = 'Dean';

    if($deptId == 'MED') { $deptId = 'MBBS'; }
    if($deptId == 'MIC') { $deptId = 'BIOS'; }

    $courses = [];
    $studentsInLevels = [];

    $studentsInLevel = studentsInDepartmentLevel($deptId, $levelId);
    // Only Students that Registered
//    if($studentsInLevel){
//        foreach ($studentsInLevel as $studentIl) {
//            if(hasStudentRegisteredAnyCourseInSessionSemester($studentIl->regno, $sessionId, $semesterId)){
//                $studentsInLevels[] = $studentIl;
//            }
//        }
//    }

    if($regCourses != false){
        // Get Courses Registered for Session and Semester
//        if(count($studentsInLevels) > 0){
//            foreach ($studentsInLevels as $student) {
        if(count($studentsInLevel) > 0){
            foreach ($studentsInLevel as $student) {
                $sessionSemesterCourses = coursesRegisteredByStudentForSessionSemester($student->regno, $sessionId, $semesterId);
                if(count($sessionSemesterCourses) > 0) {
                    foreach ($sessionSemesterCourses as $sessionSemesterCourse) {
                        if(! in_array($sessionSemesterCourse->courses_course_id, $courses)) {
                            $courses[] = $sessionSemesterCourse->courses_course_id;
                        }
                    }
                }
            }
            return $courses;
        }
        return $courses;
    } else {
        // Get Students in level in department
//        return $studentsInLevels;
        return $studentsInLevel;
    }

}

function studentRegisteredForAnyCourseInSession($regno, $sessionId){
    return DB::connection('mysql2')->table('course_registration')
            ->where('sessions_session_id', $sessionId)
            ->where('students_student_id', $regno)
            ->exists();
}

function studentRegisteredForCourseInSession($regno, $sessionId, $semester, $courseId){
    return DB::connection('mysql2')->table('course_registration')
            ->where('sessions_session_id', $sessionId)
            ->where('students_student_id', $regno)
            ->where('semester', $semester)
            ->where('courses_course_id', $courseId)
            ->exists();
}

function studentsInDepartmentLevel($deptId, $levelId){
    if($deptId == 'MED') { $deptId = 'MBBS'; }
    if($deptId == 'BIOS') { $deptId = 'MIC'; }

    return DB::connection('mysql')->table('studentbiodata')
        ->where('deptid', $deptId)
        ->where('levelid', $levelId)
        ->where('regno', 'LIKE', 'BHU/%')
        ->whereIn('grad_status', ['N', 'C'])
        ->orderBy('regno')
        ->get(['regno', 'firstname', 'surname']);
}

function coursesRegisteredByStudentForSessionSemester($studentId, $sessionId, $semesterId){
    //SELECT * FROM course_registration WHERE `semester` = 3 AND sessions_session_id = '2013/2014' AND students_student_id = 'BHU/13/01/03/0001'
    return DB::connection('mysql2')->table('course_registration')
        ->where('sessions_session_id', $sessionId)
        ->where('students_student_id', $studentId)
        ->where('semester', $semesterId)
        ->get(['courses_course_id']);
}

function studentsInDepartment($deptId){
    if($deptId == 'MED') { $deptId = 'MBBS'; }
    if($deptId == 'BIOS') { $deptId = 'MIC'; }

    return DB::connection('mysql')->table('studentbiodata')
        ->where('deptid', $deptId)
        ->orderBy('regno')
        ->get(['regno']);
}

function studentOfDepartment($regno, $deptId){
    if($deptId == 'MED') { $deptId = 'MBBS'; }
    if($deptId == 'BIOS') { $deptId = 'MIC'; }

    return DB::connection('mysql')->table('studentbiodata')
                        ->where('regno', $regno)
                        ->where('deptid', $deptId)
                        ->exists();

}

function headerCourses($deptId, $sessionId, $semesterId, $levelId){

    if($deptId == 'MED') { $deptId = 'MBBS'; }
    if($deptId == 'MIC') { $deptId = 'BIOS'; }

    // Departmental Courses
    $deptCourses = DB::connection('mysql2')->table('department_courses')
            ->where('course_program', $deptId)
            ->where('session_session_id', $sessionId);

            if($semesterId == 1 or $semesterId == 3){
                $deptCourses->where('semester', $semesterId);
            }

            $courses = $deptCourses->where('course_level', $levelId)
            ->get(['courses_course_id']);

    return $courses;
}

function manageAdminDetailedResultsReport($deptId, $sessionId, $semesterId, $levelId){
    // Get Students in Level in Department
    if($deptId == 'MED') { $deptId = 'MBBS'; }
    if($deptId == 'BIOS') { $deptId = 'MIC'; }

    $registeredStudents = [];
    $registeredCourses = [];

    $students = DB::connection('mysql')->table('studentbiodata')
                    ->where('deptid', $deptId)
                    ->where('levelid', $levelId)
                    ->orderBy('surname')
                    ->orderBy('firstname')
                    ->orderBy('regno')
                    ->get(['regno','deptid','levelid']);

    // Check students with complete registration
    if($students){
        foreach($students as $student){
            if(studentRegisteredForTheSemesterSession($student->regno,$sessionId,$semesterId)){
                $registeredStudents[] = $student;
            }
        }
    }

    // Get Courses Registered by student
    if(count($registeredStudents) > 0){
        foreach($registeredStudents as $registeredStudent){
            $regCourses = studentRegisteredForTheSemesterSession($registeredStudent->regno,$sessionId,$semesterId,true);
            $registeredCourses[$registeredStudent->regno] = $regCourses;
        }
    }

    return $registeredCourses;

}

function manageAdminRemarkResultsReport($deptId, $sessionId, $semesterId, $levelId){
    // Get Students in Level in Department
    if($deptId == 'MED') { $deptId = 'MBBS'; }
    if($deptId == 'BIOS') { $deptId = 'MIC'; }

    $registeredStudents = [];
    $students = DB::connection('mysql')->table('studentbiodata')
                    ->where('deptid', $deptId)
                    ->where('levelid', $levelId)
                    ->orderBy('surname')
                    ->orderBy('firstname')
                    ->orderBy('regno')
                    ->get(['regno','deptid','levelid']);

    // Check students with complete registration
    if($students){
        foreach($students as $student){
            if(studentRegisteredForAnyCourse($student->regno, $sessionId, $semesterId)){
                $registeredStudents[] = $student;
            }
        }
    }

    return $registeredStudents;

}

function studentRegisteredForTheSemesterSession($studentId, $sessionId, $semesterId, $returnRegisteredCourses = false){
    $role = session('role');
    if($returnRegisteredCourses){
        return DB::connection('mysql2')->table('course_registration')
            ->where('students_student_id', $studentId)
            ->where('sessions_session_id', $sessionId)
            ->where('semester', $semesterId)
//            ->where('approval_status', $role)
            ->get(['students_student_id','ca','exam','courses_course_id']);
    }
    return DB::connection('mysql2')->table('course_registration')
                            ->where('students_student_id', $studentId)
                            ->where('sessions_session_id', $sessionId)
                            ->where('semester', $semesterId)
                            ->exists();
}

function studentRegisteredForAnyCourse($studentId, $sessionId, $semesterId){
    return DB::connection('mysql2')->table('course_registration')
        ->where('students_student_id', $studentId)
        ->where('sessions_session_id', $sessionId)
        ->where('semester', $semesterId)
        ->exists();
}

function expandStudentName($studentId){
    $name = DB::connection('mysql')->table('studentbiodata')
        ->where('regno', $studentId)
        ->first(['surname','firstname']);

    return $name ? changeStringToUpperCase($name->surname) . ', ' . changeStringToTitleCase($name->firstname) : false;
}

function courseResultStringFormat($studentId, $courseId, $sessionId, $semesterId){
    $courseResult = DB::connection('mysql2')->table('course_registration')
                            ->where('students_student_id', $studentId)
                            ->where('sessions_session_id', $sessionId)
                            ->where('courses_course_id', $courseId)
                            ->where('semester', $semesterId)
                            ->first();
    if($courseResult){
        $totalScore = $courseResult->ca + $courseResult->exam;
        $grade = expandGrade($totalScore);
        $gradePoint = expandGrade($totalScore,false,true);

        return round($totalScore) . '-'. $grade . '-' . $gradePoint;
    }
    return 'X';
}

function departmentalDegreeTitle($deptId){
    if($deptId == 'MBBS') { $deptId = 'MED'; }
    if($deptId == 'MIC') { $deptId = 'BIOS'; }
    $degreeTitle = DB::connection('mysql2')->table('programs')
                        ->where('dept', $deptId)
                        ->first();
    return $degreeTitle ? changeStringToUpperCase($degreeTitle->degree . ' ' . expandProgram($deptId)) . ' PROGRAM' : '';
}

function expandFacultyFromDepartment($deptId){
    $faculty = DB::connection('mysql2')->table('departments')
                    ->where('department_id', $deptId)
                    ->first(['faculty_faculty_id']);
    return $faculty ? expandFaculty($faculty->faculty_faculty_id) : '';
}

function expandFaculty($facultyId){
    $faculty = DB::connection('mysql2')->table('faculties')
                        ->where('faculty_id', $facultyId)
                        ->first(['faculty_name']);
    return $faculty ? $faculty->faculty_name : '';
}

function expandDepartment($deptId){
    $department = DB::connection('mysql2')->table('departments')
                        ->where('department_id', $deptId)
                        ->first(['department_name']);
    return $department ? $department->department_name : '';
}

function getPreviousTotalUnit($studentId, $sessionId, $semesterId, $registered = false, $earned = false){
    $totalUnits = 0;
//    if($earned){
//        $courses = DB::connection('mysql2')->table('course_registration')
//            ->where('students_student_id', $studentId)
//            ->where('sessions_session_id','!=',currentAcademicSession())
//            ->where('approval_status','=','Senate')
//            ->get();
//    } else {
//        $courses = DB::connection('mysql2')->table('course_registration')
//            ->where('students_student_id', $studentId)
//            ->where('sessions_session_id','!=',currentAcademicSession())
//            ->get();
//    }
    $currentSessionYear = getYearFromSessionString($sessionId); // Muffy
    $sessionList = [];
    $whereInSessionList = [];

    $sessions = getStudentSessionList($studentId);
    if(count($sessions) > 0){
        foreach ($sessions as $session) {
            $sessionList[] = getYearFromSessionString($session->sessions_session_id);
        }
    }

    if(count($sessionList) > 0){
        foreach ($sessionList as $sessionYear) {
            if($sessionYear <= $currentSessionYear){
                $whereInSessionList[] = $sessionYear;
            }
        }
    }

    $coursesStudents = DB::connection('mysql2')->table('course_registration')
        ->where('students_student_id', $studentId);
    if(count($whereInSessionList) > 0){
        foreach ($whereInSessionList as $whereSession) {
            $coursesStudents->where('sessions_session_id', 'like', $whereSession .'%');
        }
    }

//    $courses = $coursesStudents->where('semester',$semesterId)->get();
    $courses = $coursesStudents->where('semester', '<',$semesterId)->get();

    // Total Credit Registered
    if($registered){
        if(count($courses) > 0){
            foreach ($courses as $course) {
                $totalUnits = $totalUnits + courseTitleAndUnits($course->courses_course_id,true);
            }
        }

        return $totalUnits;
    }

    // Total Credit Earned
    if($earned){
        $coursesPassed = [];
        if(count($courses) > 0){
            foreach ($courses as $course) {
                if($course->sessions_session_id == '2009/2010' or
                    $course->sessions_session_id == '2010/2011' or
                    $course->sessions_session_id == '2011/2012' or
                    $course->sessions_session_id == '2012/2013' or
                    ($course->sessions_session_id == '2013/2014' && str_contains($course->students_student_id,'BHU/11'))){

                    // Old Grading System Applies
                    $score = $course->exam + $course->ca;
                    if($score >= 40) { $coursesPassed[] = $course; }
                } else {
                    // New Grading System Applies
                    $score = $course->exam + $course->ca;
                    if($score >= 45) { $coursesPassed[] = $course; }
                }
            }

            if(count($coursesPassed) > 0){
                foreach ($coursesPassed as $coursesPass) {
                    $totalUnits = $totalUnits + courseTitleAndUnits($coursesPass->courses_course_id,true);
                }
            }

            return $totalUnits;
        }
    }
    return $totalUnits;
}

function getCurrentUnits($studentId, $registered = false, $earned = false, $sessionId = null, $semesterId = null){
    $totalUnits = 0;

    $sessionId = ! is_null($sessionId)? $sessionId : currentAcademicSession();
    $semesterId = ! is_null($semesterId)? $semesterId : currentSemester();

    $courses = DB::connection('mysql2')->table('course_registration')
        ->where('students_student_id', $studentId)
        ->where('sessions_session_id','=', $sessionId)
        ->where('semester','=', $semesterId)
        ->get();

    // Total Credit Registered
    if($registered){
        if(count($courses) > 0){
            foreach ($courses as $course) {
                $totalUnits = $totalUnits + courseTitleAndUnits($course->courses_course_id,true);
            }
        }

        return $totalUnits;
    }

    // Total Credit Earned
    if($earned){
        $coursesPassed = [];
        if($courses){
            foreach ($courses as $course) {
                if($course->sessions_session_id == '2009/2010' or
                    $course->sessions_session_id == '2010/2011' or
                    $course->sessions_session_id == '2011/2012' or
                    $course->sessions_session_id == '2012/2013' or
                    $course->sessions_session_id == '2013/2014' && str_contains($course->students_student_id,'BHU/11')){

                    // Old Grading System Applies
                    $score = $course->exam + $course->ca;
                    if($score >= 40) { $coursesPassed[] = $course; }
                } else {
                    // New Grading System Applies
                    $score = $course->exam + $course->ca;
                    if($score >= 45) { $coursesPassed[] = $course; }
                }
            }

            if(count($coursesPassed) > 0){
                foreach ($coursesPassed as $coursesPass) {
                    $totalUnits = $totalUnits + courseTitleAndUnits($coursesPass->courses_course_id,true);
                }
            }
        }
        return $totalUnits;
    }
    return $totalUnits;
}

function getWeightedGradePoint($studentId, $previousTotal = false, $sessionId = null, $semesterId = null){
    $coursesScorePoints = 0;
    $sessionId = ! is_null($sessionId) ? $sessionId : currentAcademicSession();
    $semesterId = ! is_null($semesterId) ? $semesterId : currentSemester();

    $currentSessionYear = getYearFromSessionString($sessionId); // Muffy
    $sessionList = [];
    $whereInSessionList = [];

    $sessions = getStudentSessionList($studentId);
    if(count($sessions) > 0){
        foreach ($sessions as $session) {
            $sessionList[] = getYearFromSessionString($session->sessions_session_id);
        }
    }

    if(count($sessionList) > 0){
        foreach ($sessionList as $sessionYear) {
            if($sessionYear <= $currentSessionYear){
                $whereInSessionList[] = $sessionYear;
            }
        }
    }

    if($previousTotal){
        $coursesStudents = DB::connection('mysql2')->table('course_registration')
            ->where('students_student_id', $studentId);
        if(count($whereInSessionList) > 0){
            foreach ($whereInSessionList as $whereSession) {
                $coursesStudents->where('sessions_session_id', 'like', $whereSession .'%');
            }
        }
        $courses = $coursesStudents->where('semester', '<', $semesterId)->get();

    } else {
        $courses = DB::connection('mysql2')->table('course_registration')
            ->where('students_student_id', $studentId)
            ->where('sessions_session_id','=', $sessionId)
            ->where('semester','=', $semesterId)
            ->get();
    }


    if(count($courses) > 0){
        foreach ($courses as $course) {
            $score = $course->exam + $course->ca;
            $unitPoints  = courseTitleAndUnits($course->courses_course_id,true);
            if($course->sessions_session_id == '2009/2010' or
                $course->sessions_session_id == '2010/2011' or
                $course->sessions_session_id == '2011/2012' or
                $course->sessions_session_id == '2012/2013' or
                $course->sessions_session_id == '2013/2014' && str_contains($course->students_student_id,'BHU/11')){

                // Old Grading System Applies
                $points = expandGrade($score,true,true);
//                if(! is_null($course->approval_status)) { $coursesScorePoints = (int) $coursesScorePoints + ( (int) $points * (int) $unitPoints ); }
                $coursesScorePoints = (int) $coursesScorePoints + ( (int) $points * (int) $unitPoints );
            } else {
                // New Grading System Applies
                $points = expandGrade($score,false,true);
//                if(! is_null($course->approval_status)) { $coursesScorePoints = (int) $coursesScorePoints + ( (int) $points * (int) $unitPoints ); }
                $coursesScorePoints = (int) $coursesScorePoints + ( (int) $points * (int) $unitPoints );
            }
        }
    }

    return $coursesScorePoints;
}

function getGradePointAverage($upper, $lower){
    $avg = 0;
    if($upper > 0 && $lower > 0){
        $avg = $upper / $lower;
        return round($avg, 2);
    }
    return $avg;
}

function getRemarkCarryOvers($studentId, $sessionId){
    $results = carryOverCoursesRemark($studentId,$sessionId);
    if(count($results) > 0){
        $carryOverStr = '';
        foreach ($results as $code => $result) {
            $carryOverStr .= $code . ', ';
        }
        return rtrim($carryOverStr, ', ');
    } else {
        return 'Pass';
    }
}
function getSummaryCarryOversPass($studentId, $sessionId){
//    $results = carryOverCourses($studentId);
    $results = carryOverCoursesRemark($studentId,$sessionId);
    return (count($results) > 0)? 1 : 0;

}

function sessionsDropDownOptions(){
    $options = [];
    $sessions = DB::connection('mysql2')->table('sessions')
                    ->orderBy('session_id','DESC')
                    ->lists('session_id');

    foreach ($sessions as $session){
        $options[$session] = $session;
    }

    return $options;
}

function matricNumberFormatFromAcademicSession($deptId, $sessionId){
    $sessionYear = explode('/',$sessionId);
    $sessionYearTwoDigit = substr($sessionYear[0],2,2);

    $matricNumberFormatArray = [
        'ACC' => 'BHU/' . $sessionYearTwoDigit . '/03/01/',
        'ANA' => 'BHU/' . $sessionYearTwoDigit . '/01/03/',
        'BCH' => 'BHU/' . $sessionYearTwoDigit . '/04/02/',
        'BIO' => 'BHU/' . $sessionYearTwoDigit . '/04/01/',
        'BOT' => 'BHU/' . $sessionYearTwoDigit . '/04/03/',
        'BUS' => 'BHU/' . $sessionYearTwoDigit . '/03/02/',
        'CHM' => 'BHU/' . $sessionYearTwoDigit . '/04/04/',
        'CMP' => 'BHU/' . $sessionYearTwoDigit . '/04/05/',
        'ECO' => 'BHU/' . $sessionYearTwoDigit . '/03/03/',
        'ENG' => 'BHU/' . $sessionYearTwoDigit . '/02/01/',
        'MAC' => 'BHU/' . $sessionYearTwoDigit . '/02/02/',
        'MTH' => 'BHU/' . $sessionYearTwoDigit . '/04/06/',
        'MBBS' => 'BHU/' . $sessionYearTwoDigit . '/01/01/',
        'BIOS' => 'BHU/' . $sessionYearTwoDigit . '/04/07/',
        'PHS' => 'BHU/' . $sessionYearTwoDigit . '/01/02/',
        'PHY' => 'BHU/' . $sessionYearTwoDigit . '/04/07/',
        'POL' => 'BHU/' . $sessionYearTwoDigit . '/03/04/',
        'SOC' => 'BHU/' . $sessionYearTwoDigit . '/03/05/',
        'STAT' => 'BHU/' . $sessionYearTwoDigit . '/04/09/',
        'ZOO' => 'BHU/' . $sessionYearTwoDigit . '/04/10/',
    ];

    return $matricNumberFormatArray[$deptId];
}

function getStudentSessionList($studentId) {
    $lists = DB::connection('mysql2')->table('course_registration')
                    ->where('students_student_id', $studentId)
                    ->distinct()
                    ->get(['sessions_session_id']);
    return $lists;
}

function getYearFromSessionString($sessionId){
    $session = explode('/',$sessionId);
    return $session[0];
}

function hasStudentRegisteredAnyCourseInSessionSemester($studentId, $sessionId, $semesterId){
    return DB::connection('mysql2')->table('course_registration')
                        ->where('students_student_id', $studentId)
                        ->where('sessions_session_id', $sessionId)
                        ->where('semester', $semesterId)
                        ->exists();
}