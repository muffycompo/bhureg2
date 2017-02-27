<?php

namespace App\Http\Controllers;

use App\BhuAdmin;
use App\CourseRegistration;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Excel;

class BhuAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.admin', ['except' => ['getLogin','postLogin']]);
    }

    public function getLogin()
    {
        return view('admin.admin_login');
    }

    public function getDashboard()
    {
        return view('admin.dashboard')
                    ->with('current_nav','dashboard');
    }

    public function getLogout()
    {
        session()->flush();
        return redirect()->route('admin.get_login');
    }

    // LECTURER AREA
    public function lecturerManageCourseResults()
    {
        $user_id = session('user_id');
        $courses = lecturerAssignedCourses($user_id, currentAcademicSession());
        return view('admin.lecturer.manage_course_results')
                    ->with('current_nav','manage_results')
                    ->with(compact('courses'));
    }

    public function lecturerManageCourseResult($courseId)
    {
        $courseId = decryptId($courseId);
        $courses = lecturerManageCourseResult($courseId, currentAcademicSession(), currentSemester());
        return view('admin.lecturer.manage_course_result')
                    ->with(compact('courses'))
                    ->with('course_id', $courseId)
                    ->with('current_nav','manage_results')
                    ->with('sn',1);
    }

    public function postLecturerManageCourseResult(Request $request, CourseRegistration $courseRegistration)
    {
        $cas = $request->only(['ca']);
        $exams = $request->only(['exam']);
        $students = $request->only(['student_id']);
        $courseId = $request->get('course_id');

        $courseRegistration->saveCourseResult($cas, $exams, $students, $courseId, currentAcademicSession(), currentSemester());

        return redirect()->back()->with([
            'flash_message' => 'Result for ' . $courseId . ' has been saved Successfully!',
            'flash_type'    => 'success'
        ]);
    }

    public function postLecturerManageCourseResultUpload(Request $request, CourseRegistration $registration)
    {
        $courseId = $request->get('course_id');
        $upload = $registration->uploadCourseResult($request, $courseId, currentAcademicSession(), currentSemester());
        if($upload != false){
            return redirect()->back()->with([
                'flash_message' => 'Result Uploaded Successfully!',
                'flash_type'    => 'success'
            ]);
        } else {
            return redirect()->back()->with([
                'flash_message' => 'You did not upload any CSV file!',
                'flash_type'    => 'danger'
            ]);
        }
    }

    public function lecturerFinalizeCourseResult($courseId)
    {
        $courseId = decryptId($courseId);
        if(finalizeCourseResult($courseId, currentAcademicSession())){
            return redirect()->back();
        } else {
            return redirect()->back()->with([
                'flash_message' => 'We could not finalize your submission, please try again later!',
                'flash_type'    => 'danger'
            ]);
        }
    }

    public function lecturerExportCourseResult($courseId, $ext, CourseRegistration $courseRegistration)
    {
        $courseId = decryptId($courseId);
        $courses = lecturerManageCourseResult($courseId, currentAcademicSession(), currentSemester());

        $courseRegistration->exportCourseResult($courses, $courseId, $ext);

    }

    // END LECTURER AREA

    // HOD AREA
    public function hodManageCourses()
    {
        $deptId = session('departments_department_id');
        $courses = hodDepartmentCourses($deptId, currentAcademicSession());
        return view('admin.hod.hod_manage_courses')
                    ->with('sn',1)
                    ->with('current_nav','manage_courses')
                    ->with(compact('courses'));
    }

    public function getHodAddCourse()
    {
        return view('admin.hod.hod_add_course')
            ->with('current_nav','manage_courses')
            ->with(compact('courses'));
    }

    public function hodManageAddCoreElectiveCourse($courseId, $semesterId, $levelId, Request $request)
    {
        $courseType = $request->segment(3);
        $courseId = decryptId($courseId);

        $addCourse = addCourseToDepartment($courseId,$semesterId,$levelId,currentAcademicSession(), $courseType);

        if($addCourse){
            return redirect()->route('admin.hod_manage_courses')->with([
                'flash_message' => $courseId . ' has been added to your Department!',
                'flash_type'    => 'success'
            ]);
        } else {
            return redirect()->route('admin.hod_manage_courses')->with([
                'flash_message' => 'We could not add ' . $courseId . ' to your Department, please try again later!',
                'flash_type'    => 'danger'
            ]);
        }
    }

    public function getHodEditCourse($courseId, $semesterId, $levelId)
    {
        $courseId = decryptId($courseId);
        $course = hodDepartmentCourse($courseId, $semesterId, $levelId);
        return view('admin.hod.hod_edit_course')
                    ->with('courseId', $courseId)
                    ->with('current_nav','manage_courses')
                    ->with(compact('course'));
    }

    public function getHodDeleteCourse($courseId)
    {
        $courseId = decryptId($courseId);
        $deptId = session('departments_department_id');

        $deleteCourse = hodDeleteCourse($courseId,$deptId,currentAcademicSession());

        if($deleteCourse){
            return redirect()->route('admin.hod_manage_courses')->with([
                'flash_message' => $courseId . ' has been deleted successfully!',
                'flash_type'    => 'success'
            ]);
        } else {
            return redirect()->route(admin.hod_manage_courses)->with([
                    'flash_message' => 'We could not delete ' . $courseId . ', please try again later!',
                    'flash_type'    => 'danger'
            ]);
        }

    }

    public function hodManageUnassignCourse($userId, $courseId, $sessionId)
    {
        $userId = decryptId($userId);
        $courseId = decryptId($courseId);
        $sessionId = decryptId($sessionId);

        $unassignCourse = hodUnassignCourse($userId, $courseId, $sessionId);

        if($unassignCourse){
            return redirect()->back()->with([
                'flash_message' => $courseId . ' has been Unassign successfully!',
                'flash_type'    => 'success'
            ]);
        } else {
            return redirect()->back()->with([
                    'flash_message' => 'We could not Unassign ' . $courseId . ', please try again later!',
                    'flash_type'    => 'danger'
            ]);
        }

    }

    public function getHodManageAssignCourse()
    {
        $deptId = session('departments_department_id');
        $session = currentAcademicSession();
        $courses = departmentalCourses($deptId, $session);
        return view('admin.hod.hod_assign_course')
                    ->with('current_nav','manage_lecturers')
                    ->with(compact('courses'));
    }
    
    public function hodManageLecturers()
    {
        $deptId = session('departments_department_id');
        $assignedCourses = assignedLecturerCourses($deptId);
        return view('admin.hod.hod_manage_lecturers')
                ->with('sn',1)
                ->with('current_nav','manage_lecturers')
                ->with(compact('assignedCourses'));
    }

    public function hodManageResultAdjustments()
    {
        $deptId = session('departments_department_id');
        $session = currentAcademicSession();
        $adjustments = hodLecturerResultSubmission($deptId, $session);
        return view('admin.hod.hod_manage_result_adjustment')
                    ->with('current_nav','manage_adjustments')
                    ->with(compact('adjustments'));
    }

    public function hodManageCourseResult($courseId, $userId, $semesterId)
    {
        $courseId = decryptId($courseId);
        $userId = decryptId($userId);

        $courses = lecturerManageCourseResult($courseId, currentAcademicSession(), $semesterId);
        return view('admin.hod.hod_manage_course_result')
            ->with(compact('courses'))
            ->with('course_id', $courseId)
            ->with('user_id', $userId)
            ->with('current_nav','manage_adjustments')
            ->with('sn',1);
    }

    public function downloadSampleCsv()
    {
        $sampleFilePath = public_path('uploads') . '/RESULT_UPLOAD_SAMPLE.csv';
        return response()->download($sampleFilePath);
    }

    public function postHodManageFindCourses(Request $request)
    {
        $deptId = $request->get('department_id');
        $semester = $request->get('semester_id');
        $levelId = $request->get('level_id');
        $courses = searchCourses($deptId, $semester, $levelId);
        return view('admin.hod.hod_add_course')
            ->with('current_nav','manage_courses')
            ->with(compact('courses'));
    }

    public function postHodEditCourse(Request $request)
    {
        $courseId = $request->get('course_id');
        $semesterId = $request->get('semester');
        $levelId = $request->get('level');
        $courseType = $request->get('course_type');

        $editCourse = hodEditDepartmentCourse($courseId, $semesterId, $levelId, $courseType, currentAcademicSession());

        if($editCourse){
            return redirect()->route('admin.hod_manage_courses')->with([
                'flash_message' => $courseId . ' has been edited successfully!',
                'flash_type'    => 'success'
            ]);
        } else {
            return redirect()->back()
                    ->withInput()
                    ->with([
                    'flash_message' => 'We could not edit ' . $courseId . ', please try again later!',
                    'flash_type'    => 'danger'
                    ]);
        }
    }

    public function postHodManageAssignCourse(Request $request)
    {
        $userId = $request->get('user_id');
        $semester = $request->get('semester');
        $session = currentAcademicSession();
        $courses = $request->get('course_id');

        $assignedCourse = hodAssignCourseToLecturer($userId, $semester, $session, $courses);

        if($assignedCourse){
            return redirect()->route('admin.hod_manage_lecturers')->with([
                'flash_message' => 'Course(s) have been Assigned to ' . expandLecturerName($userId) . ' successfully!',
                'flash_type'    => 'success'
            ]);
        } else {
            return redirect()->back()->with([
                'flash_message' => 'We could not Assigned the Course(s) to ' . expandLecturerName($userId) . '!',
                'flash_type'    => 'danger'
            ]);
        }
    }

    public function postHodManageCourseResult(Request $request, CourseRegistration $courseRegistration)
    {
        $cas = $request->only(['ca']);
        $exams = $request->only(['exam']);
        $students = $request->only(['student_id']);
        $courseId = $request->get('course_id');
        $lecturerId = $request->get('lecturer_id');

        $courseRegistration->saveCourseResultHod($lecturerId, $cas, $exams, $students, $courseId, currentAcademicSession(), currentSemester());

        return redirect()->back()->with([
            'flash_message' => 'Result for ' . $courseId . ' has been saved Successfully!',
            'flash_type'    => 'success'
        ]);
    }

    // END HOD AREA

    public function postLogin(Request $request, BhuAdmin $admin)
    {
        $user = $admin->LoginAdminUser($request->only(['username','password']));
        if($user == false) {
            return redirect()->back()
                ->withInput()
                ->with('admin_error','Username/Password combination is Invalid');
        } else {
            return redirect()->route('admin.dashboard');
        }
    }

}
