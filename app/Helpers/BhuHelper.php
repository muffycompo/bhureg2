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

function isOldGradable($studentId){
    if(str_contains($studentId,'BHU/SCI') or str_contains($studentId,'BHU/SMS') or str_contains($studentId,'BHU/HUM') or str_contains($studentId,'BHU/MED')){
        return true;
    }

    $parts = explode('/',$studentId);
    return $parts[1] < 13 ? true : false;
}

function carryOverCoursesRemark($regno, $sessionId){
    // Set fetch type to Associative Arrays
    DB::connection('mysql2')->setFetchMode(PDO::FETCH_ASSOC);

    $processCoursesScore = [];

    $results = [];

    $courses = DB::connection('mysql2')->table('course_registration')
            ->select(DB::raw('*,(ca + exam) AS total_score'))
        // Temporary: Muffy
            ->whereIn('approval_status',['Lecturer', 'HOD', 'Dean', 'Senate'])

//            ->where('approval_status','Senate')
//            ->where('sessions_session_id',$sessionId)
            ->where('students_student_id',$regno);
//            ->whereIn('semester',[1,3]); // Only First and Second Semester Carry Overs
//            ->where('semester',$semester);
//    if(!is_null($semester)) {
//        is_array($semester) ? $courses->whereIn('semester', $semester) : $courses->where('semester',$semester);
//    }

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
            if(isOldGradable($score['students_student_id'])){
                if(max($score['total_score']) < 40 && in_array($score['courses_course_id'],$results) == false){
                    $results[$score['courses_course_id']] = $score;
                    // Only Get Core Courses
//                    if(isCourseCore($score['courses_course_id'],$sessionId)){
//
//                    }
                }
            } else {
                if(max($score['total_score']) < 45 && in_array($score['courses_course_id'],$results) == false){
                    $results[$score['courses_course_id']] = $score;
//                    if(isCourseCore($score['courses_course_id'],$sessionId)){
//
//                    }
                }
            }

        }

    }

    // Reset fetch type to Object
    DB::connection('mysql2')->setFetchMode(PDO::FETCH_OBJ);

    return $results;
}

function carryOverCourses($regno, $sessionId, $semester){
    // Set fetch type to Associative Arrays
    DB::connection('mysql2')->setFetchMode(PDO::FETCH_ASSOC);

    $processCoursesScore = [];
    $results = [];

    $courses = DB::connection('mysql2')->table('course_registration')
            ->select(DB::raw('*,(ca + exam) AS total_score'))
            ->where('approval_status','Senate')
//            ->where('sessions_session_id',$sessionId)
            ->where('students_student_id',$regno)
            ->where('semester',$semester);
//    if(!is_null($semester)) {
//        is_array($semester) ? $courses->whereIn('semester', $semester) : $courses->where('semester',$semester);
//    }

    if($courses = $courses->get()){
//        dd($courses);
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
            if(isOldGradable($score['students_student_id'])){
                if(max($score['total_score']) < 40 && in_array($score['courses_course_id'],$results) == false){
                    $results[$score['courses_course_id']] = $score;
                }
            } else {
                if(max($score['total_score']) < 45 && in_array($score['courses_course_id'],$results) == false){
                    $results[$score['courses_course_id']] = $score;
                }
            }

        }

    }

    // Reset fetch type to Object
    DB::connection('mysql2')->setFetchMode(PDO::FETCH_OBJ);

    return $results;
}

function carryOverCoursesStudents($regno, $sessionId, $semester){
    // Set fetch type to Associative Arrays
    DB::connection('mysql2')->setFetchMode(PDO::FETCH_ASSOC);

    $processCoursesScore = [];
    $results = [];

    $courses = DB::connection('mysql2')->table('course_registration')
            ->select(DB::raw('*,(ca + exam) AS total_score'))
            ->where('approval_status','Senate')
//            ->where('sessions_session_id',$sessionId)
            ->where('students_student_id',$regno);
//            ->whereIn('semester',[$semester,4]);
//    if(!is_null($semester)) {
//        is_array($semester) ? $courses->whereIn('semester', $semester) : $courses->where('semester',$semester);
//    }

    if($courses = $courses->get()){
//        dd($courses);
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
            if(isOldGradable($score['students_student_id'])){
                    if(max($score['total_score']) < 40 && in_array($score['courses_course_id'],$results) == false){
                        $results[$score['courses_course_id']] = $score;
                    }
            } else {
                if(max($score['total_score']) < 45 && in_array($score['courses_course_id'],$results) == false){
                    $results[$score['courses_course_id']] = $score;
                }
            }

        }
    }

    // Reset Set fetch type to Objects
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

function hasDoneCourseRegistration($studentId, $sessionId, $semesterId){
    $course = DB::connection('mysql2')->table('course_registration')
        ->where('students_student_id',$studentId)
        ->where('sessions_session_id',$sessionId)
        ->where('semester',$semesterId)
        ->get();

    return count($course) > 0 ? true : false;
}

function expandLevel($levelId){
    $levels = [
        '1' => '100',
        '2' => '200',
        '3' => '300',
        '4' => '400',
        '5' => '500',
        '6' => '600',
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
    if($deptId == 'MIC' or $deptId == 'BIOS') return 'Microbiology';
    if($deptId == 'BST') return 'BST Unit';
    if($deptId == 'GST') return 'General Studies';
    if($deptId == 'LIB') return 'Library Information System Unit';
    if($deptId == 'MTH') return 'Mathematics';
    if($deptId == 'EPS') return 'Enterpreneurship Studies Unit';
    if($deptId == 'ZOO') return 'Academic Transcript Unit';
//    $programs = DB::connection('mysql2')->table('programs')->where('department_id', $deptId)->first();
    $programs = DB::connection('mysql2')->table('programs')->where('program_id', $deptId)->first();
//    return $programs->department_name;
    return $programs->program_name;
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
                    ->select(['firstname', 'middlename', 'surname'])
                    ->first();
    $middlename = isset($student->middlename) ? ' ' . $student->middlename . ' ' : ' ';
    return $student ? ucwords(strtolower($student->firstname . $middlename . $student->surname)) : '';
}

function lecturerAssignedCourses($userId, $session = '2016/2017'){
    $courses = DB::connection('mysql2')->table('courses_lecturers')
                    ->where('sessions_session_id', $session)
                    ->where('users_user_id', $userId)
                    ->get();
    return $courses;
};

function lecturerManageCourseResult($courseId, $session, $semester){
    $courses = DB::connection('mysql2')->table('course_registration')
                    ->where('sessions_session_id', $session)
                    ->where('semester', $semester)
                    ->where('courses_course_id', $courseId)
                    ->orderBy('students_student_id','ASC')
                    ->get();
    return $courses;
};

function isCourseResultFinalized($courseId, $session = null, $semester = null){
    $session = is_null($session) ? currentAcademicSession() : $session;
    $semester = is_null($semester) ? currentSemester() : $semester;
//    $role = session('role');

    $finalize = DB::connection('mysql2')->table('course_registration')
                        ->where('courses_course_id',$courseId)
                        ->where('sessions_session_id',$session)
                        ->where('semester',$semester)
                        ->whereIn('approval_status',['Lecturer','HOD','Dean','Senate'])
                        ->exists();
    return $finalize;
}

function isCourseResultFinalizedByHod($courseId, $session = null, $semester = null){
    $session = is_null($session) ? currentAcademicSession() : $session;
    $semester = is_null($semester) ? currentSemester() : $semester;
    $userId = lecturerIdFromCourse($courseId, $session);

    $finalizeCourses = DB::connection('mysql2')->table('course_registration')
                        ->where('courses_course_id',$courseId)
                        ->where('sessions_session_id',$session)
                        ->where('semester',$semester)
                        ->where('approval_status','HOD')
                        ->exists();

    $finalizeHodCourses = DB::connection('mysql2')->table('courses_lecturers')
                        ->where('courses_course_id',$courseId)
                        ->where('sessions_session_id',$session)
                        ->where('users_user_id',$userId)
                        ->where('final_submission', 1)
                        ->exists();

    return $finalizeCourses && $finalizeHodCourses ? true : false;
}

function finalizeCourseResult($courseId, $session, $semester, $type){
    $userId = session('role') == 'Lecturer' ? session('user_id') : lecturerIdFromCourse($courseId,$session);
    if($type == 'HOD'){
        DB::connection('mysql2')->table('courses_lecturers')
            ->where('users_user_id', $userId)
            ->where('sessions_session_id', $session)
            ->where('courses_course_id', $courseId)
            ->update(['final_submission' => 1, 'semester' => $semester]);
    }

    if($type != 'Lecturer'){
        return updateApprovalStatus($type,$courseId, $semester, $session, $userId);
    }

    return updateApprovalStatus($type,$courseId, $semester, $session);
}

function levelApprovalStatus($deptId, $session, $semester, $level, $onlyApproval = false){
    $role = session('role');
    if($deptId == 'BIOS') { $deptId = 'MCB'; }
    if($deptId == 'MED') { $deptId = 'MBBS'; }

    if($role == 'HOD'){
        if($onlyApproval){ return 'HOD'; }
        return 'APPROVAL LEVEL: HOD';
    } else {
        $approval = DB::connection('mysql2')->table('session_courses_approvals')
            ->where('program', $deptId)
            ->where('session_id', $session)
            ->where('semester_id', $semester)
            ->where('level_approved', $level)
            ->first(['approval']);
        if($onlyApproval){ return $approval ? $approval->approval : null; }
        return $approval ? 'APPROVAL LEVEL: ' . changeStringToUpperCase($approval->approval) : 'APPROVAL LEVEL: NOT APPROVED';
    }
}

function finalizeResultReport($deptId, $session, $semester, $level){
    $role = session('role');

    if($deptId == 'BIOS') { $deptId = 'MCB'; }
    if($deptId == 'MED') { $deptId = 'MBBS'; }

    $check = DB::connection('mysql2')->table('session_courses_approvals')
                    ->where('program', $deptId)
                    ->where('session_id', $session)
                    ->where('semester_id', $semester)
                    ->where('level_approved', $level)
                    ->exists();

    if($check){
        DB::connection('mysql2')->table('session_courses_approvals')
                    ->where('program', $deptId)
                    ->where('session_id', $session)
                    ->where('semester_id', $semester)
                    ->where('level_approved', $level)
                    ->update(['approval' => $role]);
    } else {
        DB::connection('mysql2')->table('session_courses_approvals')
                    ->where('program', $deptId)
                    ->where('session_id', $session)
                    ->where('semester_id', $semester)
                    ->where('level_approved', $level)
                    ->insert([
                        'approval' => $role,
                        'program' => $deptId,
                        'session_id' => $session,
                        'semester_id' => $semester,
                        'level_approved' => $level,
                    ]);
    }

    // Update Approval for Session and Semester
    $registeredStudents = manageAdminDetailedResultsReports($deptId, $session, $semester, $level);
    if($registeredStudents){
        $whereStudents = [];
        foreach ($registeredStudents as $registeredStudent) {
            if(isRegistered($registeredStudent->regno, $session, $semester)){
                $whereStudents[] = $registeredStudent->regno;
            }
        }
        DB::connection('mysql2')->table('course_registration')
            ->where('sessions_session_id', $session)
            ->where('semester', $semester)
            ->whereIn('students_student_id', $whereStudents)
            ->update(['approval_status' => $role]);

    }
}

function isRegistered($studentId, $sessionId, $semesterId){
//    select * FROM course_registration WHERE sessions_session_id = '2016/2017' AND semester = 1 AND students_student_id = 'BHU/15/04/05/0089'
    return DB::connection('mysql2')->table('course_registration')
                        ->where('students_student_id', $studentId)
                        ->where('sessions_session_id', $sessionId)
                        ->where('semester', $semesterId)
                        ->exists();
}

function hodDepartmentCourses($deptId, $session){
    if($deptId == 'MED') { $deptId = 'MBBS'; }
    if($deptId == 'MIC' or $deptId == 'BIOS') { $deptId = 'MCB'; }
    $courses = DB::connection('mysql2')->table('department_courses')
        ->where('course_program', $deptId)
        ->where('session_session_id', $session)
        ->orderBy('course_level','ASC')
        ->orderBy('semester','ASC')
        ->orderBy('session_session_id','DESC')
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

function departmentDropdownOptions()
{
    $options = [];

    if (session('role') == 'Dean'){
        $deanDeptId = session('departments_department_id');
        $likeDeptId = substr($deanDeptId,0,3);
        $departments = DB::connection('mysql2')->table('departments')
            ->where('faculty_faculty_id','LIKE', $likeDeptId . '%')
            ->get(['department_id', 'department_name']);
    } else {
        $departments = DB::connection('mysql2')->table('departments')->get(['department_id', 'department_name']);
    }
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
        '5' => '500',
        '6' => '600',
    ];
}

function semesterDropdownOptions(){
    return [
        '1' => 'First',
        '2' => 'Sandwich',
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
    if($deptId == 'MIC' or $deptId == 'BIOS') { $deptId = 'MCB'; }
    $courses = DB::connection('mysql2')->table('department_courses')
        ->where('course_program', $deptId)
        ->where('session_session_id', $session)
        ->orderBy('courses_course_id')
        ->get();
    return $courses;
}

//function searchCourses($deptId, $session, $semester, $levelId){
function searchCourses($deptId, $semester, $levelId){
//    $searchCourses = [];
    $courses = DB::connection('mysql2')->table('courses')
                    ->where('departments_department_id', $deptId)
                    ->where('semester', $semester)
                    ->where('course_level', $levelId)
                    ->get();
    return $courses;

}

function lecturerHasFinalized($courseId, $session, $semester){
    return DB::connection('mysql2')->table('course_registration')
                ->where('sessions_session_id', $session)
                ->where('courses_course_id', $courseId)
                ->where('semester', $semester)
//                ->where('approval_status', 'Lecturer')
                ->whereIn('approval_status', ['Lecturer','HOD','Dean','Senate'])
                ->exists();
}

function expandGrade($score, $oldGrade = false, $point = false){
    if($score >= 70	&& $score <= 100){
        if($point) return '5';
        return 'A';
    }
    elseif ($score >= 60 && $score <= 69.99){
        if($point) return '4';
        return 'B';
    }
    elseif ($score >= 50 && $score <= 59.99){
        if($point) return '3';
        return 'C';
    }
    elseif ($score >= 45 && $score <= 49.99){
        if($point) return '2';
        return 'D';
    }
    elseif ($score >= 40 && $score <= 44.99){
        if($oldGrade){
            return 'E';
        }
        if($oldGrade && $point) return '1';
        if($point) return '0';
        return 'F';
    }
    elseif ($score >= 0 && $score <= 39.99){
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
        '2' => 'Sandwich',
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
         $update = DB::connection('mysql2')->table('department_courses')
                ->where('course_program', formatDeptId($deptIdFromAdmin))
                ->where('semester', $semesterId)
                ->where('courses_course_id', $courseId)
                ->where('session_session_id', $session)
                ->where('course_level', $levelId)
                ->update(['course_type' => changeStringToTitleCase($courseType)]);
         return true;
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

function hodDepartmentCourse($courseId, $semesterId, $levelId, $session){
    $deptId = session('departments_department_id');
    $course = DB::connection('mysql2')->table('department_courses')
                ->where('course_program', formatDeptId($deptId))
                ->where('courses_course_id', $courseId)
                ->where('course_level', $levelId)
                ->where('semester', $semesterId)
                ->where('session_session_id', $session)
                ->first();
    return $course ? $course : [];
}

function hodEditDepartmentCourse($courseId, $semesterId, $levelId, $courseType, $session){
    $deptId = session('departments_department_id');
    $exists = DB::connection('mysql2')->table('department_courses')
            ->where('courses_course_id', $courseId)
            ->where('course_program', formatDeptId($deptId))
            ->where('course_level', $levelId)
            ->where('session_session_id', $session)
            ->exists();
    return DB::connection('mysql2')->table('department_courses')
                ->where('courses_course_id', $courseId)
                ->where('course_program', formatDeptId($deptId))
                ->where('course_level', $levelId)
                ->where('semester', $semesterId)
                ->where('session_session_id', $session)
                ->update([
                    'semester' => $semesterId,
                    'course_level' => $levelId,
                    'course_type' => changeStringToTitleCase($courseType)
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

function hodDepartmentLecturers($deptId){
    $lecturers = DB::connection('mysql2')->table('users')
                    ->where('departments_department_id', $deptId)
                    ->whereNotIn('role', ['Dean','HOD','Senate'])
                    ->orderBy('first_name')
                    ->get(['user_id','first_name','last_name','departments_department_id']);
    return json_encode($lecturers);

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

function updateSessionApprovalStatus($levelId, $semester, $session){
    $role = session('role');
    $deptId = session('departments_department_id');

    if($deptId == 'MED') { $deptId = 'MBBS'; }
    if($deptId == 'BIOS') { $deptId = 'MCB'; }

    $sessionApproval = DB::connection('mysql2')->table('session_courses_approvals')
        ->where('session_id', $session)
        ->where('semester_id', $semester)
        ->where('program', $deptId)
        ->where('approval', $role)
        ->where('level_approved', $levelId)
        ->exits();
    if($sessionApproval){
        return DB::connection('mysql2')->table('session_courses_approvals')
            ->where('session_id', $session)
            ->where('semester_id', $semester)
            ->where('program', $deptId)
            ->where('level_approved', $levelId)
            ->update(['approval' => $role ]);
    } else {
        return DB::connection('mysql2')->table('session_courses_approvals')
            ->insert([
                'session_id' => $session,
                'semester_id' => $semester,
                'program' => $deptId,
                'level_approved' => $levelId,
                'approval' => $role
            ]);
    }
}

function updateApprovalStatus($type, $courseId, $semester, $session, $userId = null){
    //$type = session('role');

    $updateApprovalStatus = DB::connection('mysql2')->table('course_registration')
        ->where('semester', $semester)
        ->where('sessions_session_id', $session)
        ->where('courses_course_id', $courseId)
        ->update(['approval_status' => $type]);

    return $updateApprovalStatus ? true : false;
}

function updateApprovalStatusReport($deptId, $semester, $session, $level){
        $type = session('role');

        if($deptId == 'BIOS') { $deptId = 'MCB'; }
        if($deptId == 'MED') { $deptId = 'MBBS'; }

        $sessionApproval = DB::connection('mysql2')->table('session_courses_approvals')
            ->where('semester_id', $semester)
            ->where('session_id', $session)
            ->where('program', $deptId)
            ->where('level_approved', $level)
            ->exists();
        if($sessionApproval){
            DB::connection('mysql2')->table('session_courses_approvals')
                ->where('semester_id', $semester)
                ->where('session_id', $session)
                ->where('program', $deptId)
                ->where('level_approved', $level)
                ->update(['approval' => $type]);
        } else {
            DB::connection('mysql2')->table('session_courses_approvals')->insert([
                    'semester_id' => $semester,
                    'session_id' => $session,
                    'program' => $deptId,
                    'level_approved' => $level,
                    'approval' => $type,
                ]);
        }
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

function resultSessionDropdownOptions(){
    $regno = session('regno');
    $sessionOptions = [];
    $sessions = DB::connection('mysql2')->table('course_registration')
                    ->where('students_student_id', $regno)
                    ->where('approval_status', 'Senate')
                    ->orderBy('sessions_session_id','DESC')
                    ->groupBy('sessions_session_id')
                    ->lists('sessions_session_id');
    foreach ($sessions as $session) {
        $sessionOptions[$session] = $session;
    }

    return $sessionOptions;
}

function getClassOfDegree($cgpa){

    if($cgpa >= 4.5){
        return 'First Class';
    } elseif ($cgpa >= 3.5 && $cgpa <=4.49){
        return 'Second Class Upper Division';
    } elseif ($cgpa >= 2.5 && $cgpa <=3.49){
        return 'Second Class Lower Division';
    } elseif ($cgpa >= 1.5 && $cgpa <=2.49){
        return 'Third Class';
    } else {
        return 'Pass';
    }

    return '';
}

function lecturerIdFromCourse($courseId, $sessionId){
    $lecturer = DB::connection('mysql2')->table('courses_lecturers')
                    ->where('courses_course_id', $courseId)
                    ->where('sessions_session_id', $sessionId)
                    ->first(['users_user_id']);
    return $lecturer ? $lecturer->users_user_id : null;
}

function departmentFromLecturerId($lecturerId){
    $lecturer = DB::connection('mysql2')->table('users')
        ->where('user_id', $lecturerId)
        ->first(['departments_department_id']);
    return $lecturer ? $lecturer->departments_department_id : null;
}

function levelFromSessionCourses($courseId, $session, $semester, $program){
    if($program == 'BIOS') { $program = 'MCB'; }
    if($program == 'MED') { $program = 'MBBS'; }

    $courseLevel = DB::connection('mysql2')->table('department_courses')
        ->where('courses_course_id', $courseId)
        ->where('session_session_id', $session)
        ->where('semester', $semester)
        ->where('course_program', $program)
        ->first(['course_level']);
    return $courseLevel ? $courseLevel->course_level : null;
}

function updateBiodataForStudents(){
    // Get Student records to update from dreamz.students table
    $dreamsStudents = DB::connection('mysql2')->table('students')
                                ->get(['student_id','current_level', 'graduate_status','student_program']);
    if($dreamsStudents){
        foreach ($dreamsStudents as $dreamsStudent) {
            $deptId = $dreamsStudent->student_program;

            if($deptId == 'MCB') { $deptId = 'MIC'; }
            if($deptId == 'MBBS_CLI') { $deptId = 'MBBS'; }
            if($deptId == 'PSY_MBBS') { $deptId = 'PHS'; }
            if($deptId == 'ANA_MBBS') { $deptId = 'ANA'; }

            // Update Student records in bingham.studentbiodata table
            DB::connection('mysql')->table('studentbiodata')
                                ->where('regno', $dreamsStudent->student_id)
                                ->update([
                                    'levelid' => $dreamsStudent->current_level,
                                    'grad_status' => $dreamsStudent->graduate_status,
                                    'deptid' => $deptId,
                                ]);
        }
    }

}

function isCourseForCurrentSemester($courseId){
    $semesterId = currentSemester();
    $sessionId = currentAcademicSession();
    $lecturerId = session('user_id');

    $course = DB::connection('mysql2')->table('course_registration')
                    ->where('sessions_session_id', $sessionId)
                    ->where('semester', $semesterId)
                    ->where('courses_course_id', $courseId)
                    ->exists();
    $assignment = DB::connection('mysql2')->table('courses_lecturers')
                    ->where('users_user_id', $lecturerId)
                    ->where('sessions_session_id', $sessionId)
                    ->where('courses_course_id', $courseId)
                    ->exists();
    return $course && $assignment ? true : false;
}

function semesterFromCourseId($courseId, $sessionId){
//    $deptId = session('departments_department_id');
    $userId = session('user_id');
//    if($deptId == 'MED') { $deptId = 'MBBS'; }
//    if($deptId == 'BIOS' or $deptId == 'MIC') { $deptId = 'MCB'; }

//    $semester = DB::connection('mysql2')->table('department_courses')
//                    ->where('session_session_id', $sessionId)
//                    ->where('courses_course_id', $courseId)
//                    ->where('course_program', $deptId)
//                    ->first(['semester']);

    $semester = DB::connection('mysql2')->table('courses_lecturers')
                    ->where('sessions_session_id', $sessionId)
                    ->where('courses_course_id', $courseId)
                    ->where('users_user_id', $userId)
                    ->first(['semester']);
    return $semester ? $semester->semester : currentSemester();
}

function studentSemesterFromCourseId($courseId,$sessionId){
//    DB::connection('mysql2')->setFetchMode(PDO::FETCH_OBJ);
    $deptId = formatDeptId(session('deptid'));
    $semester = DB::connection('mysql2')->table('department_courses')
        ->where('session_session_id', $sessionId)
        ->where('courses_course_id', $courseId)
        ->where('course_program', $deptId)
        ->first(['semester']);
    return $semester ? $semester->semester : currentSemester();
}

function isCourseRegistrationEnabled(){
    $registrationStatus = DB::connection('mysql2')->table('utilities')
                    ->where('utility_id', 'registration_status')
                    ->first(['utility_int_value']);
    if($registrationStatus){
        return $registrationStatus->utility_int_value == 1 ? true : false;
    } else {
        return false;
    }
}

function isCourseFromHodDepartment($courseId){
    $deptId = session('departments_department_id');
    return DB::connection('mysql2')->table('courses')
        ->where('departments_department_id', $deptId)
        ->where('course_id', $courseId)
        ->exists();
}

function getStudentGPA($studentId, $session, $semester){

    $totalUnits = 0;
    $qualityPoints = 0;

    $studentResults = DB::connection('mysql2')->table('course_registration')
            ->where('students_student_id', $studentId)
            ->where('sessions_session_id', $session)
            ->where('semester', $semester)
            ->where('approval_status', 'Senate')
            ->get();

    if(count($studentResults) > 0){
        foreach ($studentResults as $studentResult) {
            $totalUnits = $totalUnits + (int) courseTitleAndUnits($studentResult->courses_course_id,true);
            $score = $studentResult->ca + $studentResult->exam;

            if(isOldGradable($studentId)){

                // Old Grading System Applies
                $qualityPoints = $qualityPoints + ((int) courseTitleAndUnits($studentResult->courses_course_id,true) * (int) expandGrade($score,true,true));

            } else {
                // New Grading System Applies
                $qualityPoints = $qualityPoints + ((int) courseTitleAndUnits($studentResult->courses_course_id,true) * (int) expandGrade($score,false,true));
            }
        }
    }

    if($qualityPoints > 0 && $totalUnits > 0){
        $gpa = $qualityPoints / $totalUnits;
        return round($gpa,2);
    }
    return 'N/A';
}

function getStudentCGPA($studentId){

    $totalUnits = 0;
    $qualityPoints = 0;

    $studentResults = DB::connection('mysql2')->table('course_registration')
            ->where('students_student_id', $studentId)
            ->where('approval_status', 'Senate')
            ->get();

    if(count($studentResults) > 0){
        foreach ($studentResults as $studentResult) {
            $totalUnits = $totalUnits + (int) courseTitleAndUnits($studentResult->courses_course_id,true);
            $score = $studentResult->ca + $studentResult->exam;

            if(isOldGradable($studentId)){

                // Old Grading System Applies
                $qualityPoints = $qualityPoints + ((int) courseTitleAndUnits($studentResult->courses_course_id,true) * (int) expandGrade($score,true,true));

            } else {
                // New Grading System Applies
                $qualityPoints = $qualityPoints + ((int) courseTitleAndUnits($studentResult->courses_course_id,true) * (int) expandGrade($score,false,true));
            }
        }
    }

    if($qualityPoints > 0 && $totalUnits > 0){
        $cgpa = $qualityPoints / $totalUnits;
        return round($cgpa,2);
    }
    return 'N/A';
}

function getStudentLevel($studentId){
    $level = DB::connection('mysql')->table('studentbiodata')
        ->where('regno', $studentId)
        ->first(['levelid']);
    return $level ? $level->levelid : null;
}

function isAltEntryForResult($lecturerId, $courseId, $sessionId, $value = false){
    return false;
    $altEntry = DB::connection('mysql2')->table('courses_lecturers')
        ->where('users_user_id', $lecturerId)
        ->where('courses_course_id', $courseId)
        ->where('sessions_session_id', $sessionId)
        ->first(['alternate_result_entry']);
    if($value){
        return $altEntry->alternate_result_entry;
    }
    return $altEntry && ($altEntry->alternate_result_entry == 1)? true : false;
}

function updateAltEntryForResult($lecturerId, $courseId, $sessionId, $altEntry){
    return;
    DB::connection('mysql2')->table('courses_lecturers')
        ->where('users_user_id', $lecturerId)
        ->where('courses_course_id', $courseId)
        ->where('sessions_session_id', $sessionId)
        ->update(['alternate_result_entry' => is_null($altEntry) ? 0 : $altEntry]);
}

function getStudentResultsTranscript($studentId){
    $results = [];
    $sessions = getStudentRegistrationSessions($studentId);

    if(count($sessions) > 0){
        $transcriptResults = DB::connection('mysql2')->table('course_registration')
            ->where('students_student_id', $studentId)
            ->whereIn('approval_status', ['Lecturer','HOD','Dean','Senate'])
            ->orderBy('sessions_session_id')
            ->get();

        foreach ($sessions as $session) {
            foreach ($transcriptResults as $transcriptResult) {
                if($transcriptResult->sessions_session_id == $session->sessions_session_id){
                    $results[$session->sessions_session_id][] = $transcriptResult;
                }
            }
        }
    }

    return $results;
}

function getStudentRegistrationSessions($studentId){
    $sessions = DB::connection('mysql2')->table('course_registration')
        ->where('students_student_id', $studentId)
        ->whereIn('approval_status', ['Lecturer','HOD','Dean','Senate'])
        ->groupBy('sessions_session_id')
        ->get(['sessions_session_id']);

    return $sessions;
}

function expandTranscriptGradeRemark($score, $studentId = null){
    if(! is_null($studentId)){
        if(isOldGradable($studentId)){
            $grade = expandGrade($score,true);
        } else {
            $grade = expandGrade($score);
        }

        if(
            $grade == 'A' ||
            $grade == 'B' ||
            $grade == 'C' ||
            $grade == 'D' ||
            $grade == 'E'
        ){
            return 'P';
        } else {
            return 'Rpt';
        }
    }
}

function transcriptStartDate($studentId){
    return transcriptDate($studentId);
}

function transcriptEndDate($studentId){
    return transcriptDate($studentId, 'DESC');
}

function transcriptDate($studentId, $order = 'ASC') {
    $session = DB::connection('mysql2')->table('course_registration')
        ->where('students_student_id', $studentId)
//        ->whereIn('approval_status', ['Lecturer','HOD','Dean','Senate'])
        ->orderBy('sessions_session_id',$order)
        ->first(['sessions_session_id']);

    return $session ? $session->sessions_session_id : '';
}

function getStudentBiodata($studentId){
    return DB::connection('mysql')->table('studentbiodata')
        ->where('regno', $studentId)
        ->first();
}

function expandGender($genderId){
    if(! is_null($genderId)){
        return $genderId == 'M' ? 'Male' : 'Female';
    }
    return '';
}

function graduateStatus($graduationStatus, $levelId = null){
    if(! is_null($levelId)){
        if(str_contains($levelId, 'Graduate')){
            return 'Graduated';
        }
    }

    if($graduationStatus == 'G'){
        return 'Graduated';
    } elseif ($graduationStatus == 'C' or $graduationStatus == 'C'){
        return 'Current Student';
    } else {
        return '';
    }
}

function expandProgramSpecialization($deptId){
    if($deptId == 'MED' or $deptId == 'MBBS') return 'Medicine & Surgery';
    if($deptId == 'MIC' or $deptId == 'BIOS') return 'B.Sc Microbiology';

    $program = DB::connection('mysql2')->table('programs')->where('program_id', $deptId)->first();
    return $program->degree . ' ' . $program->program_name;
}

function studentHasRegisteredForAnyCourse($studentId){
    return DB::connection('mysql2')->table('course_registration')
                ->where('students_student_id', $studentId)
                ->count(['courses_course_id']);
}

function roundNumberUp($number){
    return round($number,0,PHP_ROUND_HALF_UP);
}