<?php

function departmentalCourseDropdown($deptId = null, $semester = null, $session = null){
    $options = [];

    $courses = DB::connection('mysql2')->table('department_courses');

    if(!is_null($deptId)){
        if($deptId == 'MED') { $deptId = 'MBBS'; }
        if($deptId == 'MIC') { $deptId = 'BIOS'; }
        $courses->where('course_program',$deptId);
    }

    if(!is_null($semester)){ $courses->where('semester',$semester); }

    if(!is_null($session)){ $courses->where('session_session_id',$session); }

    $courses = $courses->get();

    foreach ($courses as $course){
        $options[$course->courses_course_id] = $course->course_title;
    }

    return $options;
}

function recommendedDepartmentalCourses($deptId, $levelId, $semester = null, $session = null){


    if($deptId == 'MIC') { $deptId = 'MCB'; }
    $courses = DB::connection('mysql2')->table('department_courses')
            ->where('course_program',$deptId)
            ->where('course_level',$levelId);

    if(!is_null($semester)){ $courses->where('semester',$semester); }

    if(!is_null($session)){ $courses->where('session_session_id',$session); }

    return $courses->orderBy('course_type')->get();


}

function carryOverCourses($regno, $semester = null){
    // Set fetch type to Associative Arrays
    DB::connection('mysql2')->setFetchMode(PDO::FETCH_ASSOC);

    $processCoursesScore = [];

    $results = [];

    $courses = DB::connection('mysql2')->table('course_registration')
            ->select(DB::raw('*,(ca + exam) AS total_score'))
            ->where('approval_status','Senate')
            ->where('students_student_id',$regno);
    if(!is_null($semester)) {
        is_array($semester) ? $courses->whereIn('semester', $semester) : $courses->where('semester',$semester);
    }

    if($courses = $courses->get()){
        // Rebuild Result Array
        foreach($courses as $course) {
            $skippedSession = studentSkippedSessions($course['students_student_id'], $course['sessions_session_id']);
            if(! $skippedSession){
                $courseId = $course['courses_course_id'];
                if (!isset($processCoursesScore[$courseId])) {
                    $processCoursesScore[$courseId] = $course;
                    $processCoursesScore[$courseId]['total_score'] = [];
                }
                $processCoursesScore[$courseId]['total_score'][] = $course['total_score'];
            }

        }

        // Check if a course has not been passed by Student
        // 45 Passmark for BHU/12 and above
        // Few Exceptions for Spill-Over students
        // A case of a student (BHU/11/02/02/0039) which have passed during 2013/2014 using Old standard

        foreach ($processCoursesScore as $score) {
            // 40 Passmark for BHU/11 and below
            if($score['sessions_session_id'] == '2009/2010' or
                $score['sessions_session_id'] == '2010/2011' or
                $score['sessions_session_id'] == '2011/2012' or
                ($score['sessions_session_id'] == '2013/2014') && str_contains($score['students_student_id'],'BHU/11')){
                if(max($score['total_score']) < 40){
                    $results[$score['courses_course_id']] = $score;
                }
            } else {
                if(max($score['total_score']) < 45){
                    $results[$score['courses_course_id']] = $score;
                }
            }

        }

    }

    // Reset fetch type to Object
    DB::connection('mysql2')->setFetchMode(PDO::FETCH_OBJ);

    return $results;
}

function courseTitleAndUnits($courseId,$units = false){
    $courses = DB::connection('mysql2')->table('courses')->where('course_id',$courseId)->first();
    return $units ? $courses->course_unit : $courses->course_title;
}

function isCourseRegistered($courseId, $session = null, $semester = null){
    $course = DB::connection('mysql2')->table('course_registration')
            ->where('students_student_id',session('regno'))
            ->where('courses_course_id',$courseId);

    if(!is_null($session)){
        $course->where('sessions_session_id',$session);
    }

    if(!is_null($semester)){
        is_array($semester) ? $course->whereIn('semester',$semester) : $course->where('semester',$semester);
    }

    return $course->exists();
}

function expandLevel($levelId){
    $levels = [
        '1' => '100',
        '2' => '200',
        '3' => '300',
        '4' => '400',
    ];
    try
    {
        return $levels[$levelId];
    }
    catch (ErrorException $e)
    {
    	return null;
    }
}

function expandProgram($deptId){
    if($deptId == 'MED' or $deptId == 'MBBS') return 'Medicine & Surgery';
    if($deptId == 'MIC') return 'Biological Sciences';
    $programs = DB::connection('mysql2')->table('departments')->where('department_id', $deptId)->first();
    return $programs->department_name;
}

function expandCourseType($courseId){
    $programs = DB::connection('mysql2')->table('department_courses')->where('courses_course_id', $courseId)->first();
    return $programs->course_type;
}

function changeStringToTitleCase($string){
    return ucwords(strtolower($string));
}

function changeStringToUpperCase($string){
    return strtoupper(strtolower($string));
}

function prepareAppUrl(){
    return env('APP_URL','http://binghamuni.edu.ng');
}

function preparePassportImgPath($dbPath){
    if(!empty($dbPath)){
        return 'http://binghamuni.edu.ng' . str_replace('..','',$dbPath);
    } else {
        return prepareAppUrl() . '/images/passport_placeholder.png';
    }
}

function encryptId($id){
    return base64_encode($id);
}

function decryptId($id){
    return base64_decode($id);
}

function studentNameFromMatriculationNo($student_id){
    $student = DB::connection('mysql')->table('studentbiodata')
                    ->where('regno', $student_id)
                    ->select(['firstname','surname'])
                    ->first();
    return $student ? ucwords(strtolower($student->firstname . ' ' . $student->surname)) : '';
}

function lecturerAssignedCourses($userId, $session = '2016/2017'){
    $courses = DB::connection('mysql2')->table('courses_lecturers')
                    ->where('sessions_session_id', $session)
                    ->where('users_user_id', $userId)
                    ->get();
    return $courses;
};

function lecturerManageCourseResult($courseId, $session = '2016/2017', $semester = 1){
    $courses = DB::connection('mysql2')->table('course_registration')
                    ->where('sessions_session_id', $session)
                    ->where('semester', $semester)
                    ->where('courses_course_id', $courseId)
                    ->orderBy('students_student_id','ASC')
                    ->get();
    return $courses;
};

function isCourseResultFinalized($courseId){
    $session = currentAcademicSession();
    $semester = currentSemester();
    $role = session('role');
    $approval = checkApprovalStatus($role,$courseId,$session, $semester);
    if($role == 'Lecturer' && $approval) return false;
    if($role == 'HOD' && $approval) return false;
    if($role == 'Dean' && $approval) return false;
    if($role == 'Senate' && $approval) return false;
    return true;
}

function finalizeCourseResult($userId, $courseId, $session, $type){
    $semester = currentSemester();
    DB::connection('mysql2')->table('courses_lecturers')
                    ->where('users_user_id', $userId)
                    ->where('semester', $semester)
                    ->where('sessions_session_id', $session)
                    ->where('courses_course_id', $courseId)
                    ->where('final_submission', 1)
                    ->update(['final_submission' => 1]);

    return updateApprovalStatus($type,$courseId, $semester, $session);
}

function hodDepartmentCourses($deptId, $session){
    if($deptId == 'MED') { $deptId = 'MBBS'; }
    if($deptId == 'MIC') { $deptId = 'BIOS'; }
    $courses = DB::connection('mysql2')->table('department_courses')
        ->where('course_program', $deptId)
        ->where('session_session_id', $session)
        ->orderBy('semester','ASC')
        ->orderBy('course_level','ASC')
        ->get();
    return $courses;
}

function hodAddDepartmentCourses($deptId, $level, $semester){
    if($deptId == 'MED') { $deptId = 'MBBS'; }
    if($deptId == 'MIC') { $deptId = 'BIOS'; }
    $courses = DB::connection('mysql2')->table('department_courses')
        ->where('course_program', $deptId)
        ->where('session_session_id', $session)
        ->orderBy('semester','ASC')
        ->orderBy('course_level','ASC')
        ->get();
    return $courses;
}

function departmentDropdownOptions(){
    $options = [];

    $departments = DB::connection('mysql2')->table('departments')->get(['department_id','department_name']);

    foreach ($departments as $department){
        if($department->department_id == 'ALL' || $department->department_id == 'HSMS_Result'
            || $department->department_id == 'MED_Result'|| $department->department_id == 'SCIT_Result') continue;

        $options[$department->department_id] = $department->department_name;
    }

    return $options;
}

function levelDropdownOptions(){
    return [
        '1' => '100',
        '2' => '200',
        '3' => '300',
        '4' => '400',
    ];
}

function semesterDropdownOptions(){
    return [
        '1' => 'First',
        '3' => 'Second',
        '4' => 'Summer',
    ];
}

function lecturersDropdownOptions(){
    $options = [];
    // Could be optional: Experimental
    $deptId = session('departments_department_id');
    $lecturers = DB::connection('mysql2')->table('users')
                    ->where('departments_department_id', $deptId)
                    ->where('role', 'Lecturer')
                    ->orderBy('first_name')
                    ->get();
    foreach ($lecturers as $lecturer){
        $options[$lecturer->user_id] = changeStringToTitleCase($lecturer->first_name) . ' ' . changeStringToTitleCase($lecturer->last_name);
    }

    return $options;
}

function departmentalCourses($deptId, $session){
    if($deptId == 'MED') { $deptId = 'MBBS'; }
    if($deptId == 'MIC') { $deptId = 'BIOS'; }
    $courses = DB::connection('mysql2')->table('department_courses')
        ->where('course_program', $deptId)
        ->where('session_session_id', $session)
        ->orderBy('courses_course_id')
        ->get();
    return $courses;
}

function searchCourses($deptId, $semester, $levelId){
    $courses = DB::connection('mysql2')->table('courses')
                    ->where('departments_department_id', $deptId)
                    ->where('semester', $semester)
                    ->where('course_level', $levelId)
                    ->get();
    return $courses;
}

function expandGrade($score, $oldGrade = false, $point = false){
    if($score >= 70	&& $score <= 100){
        if($point) return '5';
        return 'A';
    }
    elseif ($score >= 60 && $score <= 69){
        if($point) return '4';
        return 'B';
    }
    elseif ($score >= 50 && $score <= 59){
        if($point) return '3';
        return 'C';
    }
    elseif ($score >= 45 && $score <= 49){
        if($point) return '2';
        return 'D';
    }
    elseif ($score >= 40 && $score <= 44){
        if($oldGrade){
            return 'E';
        }
        if($oldGrade && $point) return '1';
        if($point) return '0';
        return 'F';
    }
    elseif ($score >= 0 && $score <= 39){
        if($point) return '0';
        return 'F';
    }
    else {
        if($point) return '0';
        return 'F';
    }
}

function expandSemester($semesterId){
    $semester = [
        '1' => 'First',
        '3' => 'Second',
        '4' => 'Summer',
    ];
    return array_key_exists($semesterId,$semester)? $semester[$semesterId] : '';
}

function expandLecturerName($userId){
    $lecturer = DB::connection('mysql2')->table('users')
        ->where('user_id', $userId)
        ->select(['first_name','last_name'])
        ->first();
    return changeStringToTitleCase($lecturer->first_name) . ' ' . changeStringToTitleCase($lecturer->last_name);
}

function formatDeptId($deptId){
    if($deptId == 'MED') return 'MBBS';
    if($deptId == 'BIOS') return 'MCB';
    return $deptId;
}

function addCourseToDepartment($courseId, $semesterId, $levelId, $session, $courseType){
    $deptIdFromAdmin = session('departments_department_id');
    if(DB::connection('mysql2')->table('department_courses')
        ->where('course_program', formatDeptId($deptIdFromAdmin))
        ->where('semester', $semesterId)
        ->where('session_session_id', $session)
        ->where('courses_course_id', $courseId)
        ->where('course_level', $levelId)
        ->exists()){
            // Update
         return DB::connection('mysql2')->table('department_courses')
                ->where('course_program', formatDeptId($deptIdFromAdmin))
                ->where('semester', $semesterId)
                ->where('courses_course_id', $courseId)
                ->where('session_session_id', $session)
                ->where('course_level', $levelId)
                ->update(['course_type' => changeStringToTitleCase($courseType)]);
    } else {
        // Add
        return DB::connection('mysql2')->table('department_courses')
                ->insert([
                    'course_program' => formatDeptId($deptIdFromAdmin),
                    'semester'  => $semesterId,
                    'course_level' => $levelId,
                    'courses_course_id' => $courseId,
                    'session_session_id' => $session,
                    'course_type' => changeStringToTitleCase($courseType)
                ]);
    }
}

function hodDepartmentCourse($courseId, $semesterId, $levelId){
    $course = DB::connection('mysql2')->table('department_courses')
                ->where('courses_course_id', $courseId)
                ->where('course_level', $levelId)
                ->where('semester', $semesterId)
                ->first();
    return $course ? $course : [];
}

function hodEditDepartmentCourse($courseId, $semesterId, $levelId, $courseType, $session){
    return DB::connection('mysql2')->table('department_courses')
                ->where('courses_course_id', $courseId)
                ->where('session_session_id', $session)
                ->update([
                    'semester' => $semesterId,
                    'course_level' => $levelId,
                    'course_type' => $courseType
                ]);
}

function hodDeleteCourse($courseId, $deptId, $session){
    return DB::connection('mysql2')->table('department_courses')
        ->where('courses_course_id', $courseId)
        ->where('session_session_id', $session)
        ->where('course_program', formatDeptId($deptId))
        ->delete();
}

function hodUnassignCourse($userId, $courseId, $session){
    return DB::connection('mysql2')->table('courses_lecturers')
        ->where('courses_course_id', $courseId)
        ->where('sessions_session_id', $session)
        ->where('users_user_id', $userId)
        ->delete();
}

function assignedLecturerCourses($deptId){
    return DB::connection('mysql2')->table('courses_lecturers')
        ->join('users','courses_lecturers.users_user_id','=','users.user_id')
        ->where('departments_department_id',formatDeptId($deptId))
        ->orderBy('courses_lecturers.sessions_session_id','DESC')
        ->orderBy('users.user_id')
        ->select([
            'users.user_id',
            'users.first_name',
            'users.last_name',
            'courses_lecturers.courses_course_id',
            'courses_lecturers.semester',
            'courses_lecturers.sessions_session_id',
        ])
        ->paginate(25);
}

function hodAssignCourseToLecturer($userId, $semester, $session, $courses){
    if(count($courses) > 0){
        $insertArray = [];
        foreach ($courses as $course){
            if(isCourseAssignedToLecturer($userId, $semester, $session, $course)) continue;
            $insertArray[] = [
                'users_user_id' => $userId,
                'sessions_session_id' => $session,
                'courses_course_id' => $course,
                'semester' => $semester
            ];
        }
        return DB::connection('mysql2')->table('courses_lecturers')->insert($insertArray);
    } else {
        return false;
    }
}

function isCourseAssignedToLecturer($userId, $semester, $session, $course){
    return DB::connection('mysql2')->table('courses_lecturers')
                ->where('users_user_id', $userId)
                ->where('semester', $semester)
                ->where('sessions_session_id', $session)
                ->where('courses_course_id', $course)
                ->exists();
}

function hodLecturerResultSubmission($deptId, $session){
    $semester = currentSemester();
    return DB::connection('mysql2')->table('users')
                ->join('courses_lecturers','users.user_id','=','courses_lecturers.users_user_id')
                ->join('course_registration','course_registration.courses_course_id','=','courses_lecturers.courses_course_id')
                ->where('course_registration.approval_status','Lecturer')
                ->where('courses_lecturers.sessions_session_id',$session)
                ->where('course_registration.semester',$semester)
                ->where('users.departments_department_id',$deptId)
                ->select([
                    'users.user_id',
                    'users.first_name',
                    'users.last_name',
                    'course_registration.semester',
                    'courses_lecturers.courses_course_id'
                ])
                ->groupBy('course_registration.courses_course_id')
                ->orderBy('users.user_id')
                ->paginate(25);
}

//function currentAcademicSession(){
//    return '2016/2017';
//}

function currentAcademicSession(){
    $session = DB::connection('mysql2')->table('sessions')
        ->orderBy('session_id','DESC')
        ->first(['session_id']);
    return $session ? $session->session_id : '';
}
//
//function currentSemester(){
//    return 1;
//}

function currentSemester(){
    $semester = DB::connection('mysql2')->table('utilities')
        ->where('utility_id','current_semester')
        ->first(['utility_int_value']);
    return $semester ? $semester->utility_int_value : 1;
}

function updateApprovalStatus($type, $courseId, $semester, $session){
    $updateApprovalStatus = DB::connection('mysql2')->table('course_registration')
        ->where('semester', $semester)
        ->where('sessions_session_id', $session)
        ->where('courses_course_id', $courseId)
        ->update(['approval_status' => $type]);

    return $updateApprovalStatus ? true : false;
}

function checkApprovalStatus($type, $courseId,$session,$semester){
    $approvalStatus = DB::connection('mysql2')->table('course_registration')
        ->where('sessions_session_id', $session)
        ->where('semester', $semester)
        ->where('courses_course_id', $courseId);

    switch ($type) {
        case 'Lecturer':
            $approvalStatus->whereNull('approval_status');
            break;
        case 'HOD':
            $approvalStatus->where('approval_status', 'Lecturer');
            break;
        case 'Dean':
            $approvalStatus->where('approval_status','HOD');
            break;
        case 'Senate':
            $approvalStatus->where('approval_status','Dean');
            break;
        default:
            return false;
    }
    return $approvalStatus->exists();

}

function studentSkippedSessions($studentId, $sessionId){
    return DB::connection('mysql2')->table('session_skiplist')
                ->where('student_id', $studentId)
                ->where('session_id', $sessionId)
                ->first();
}

function maxRegistrationUnits(){
    $units = DB::connection('mysql2')->table('utilities')
                ->where('utility_id','max_credit_units')
                ->first(['utility_int_value']);
    return $units ? $units->utility_int_value : 30;
}