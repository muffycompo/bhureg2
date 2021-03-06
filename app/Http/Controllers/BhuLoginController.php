<?php

namespace App\Http\Controllers;

use App\CourseRegistration;
use App\StudentResult;

class BhuLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(Auth::check()){
            return redirect()->route('dashboard');
        }
        return redirect('/login');
    }

    public function getRegister()
    {
        $sessionId = currentAcademicSession();
        $semesterId = currentSemester();
        $courses = recommendedDepartmentalCourses(session('deptid'),session('levelid'),$semesterId,$sessionId);

//        $carryovers = carryOverCourses(session('regno'),[1,4]);
        $carryovers = carryOverCoursesStudents(session('regno'), $sessionId, $semesterId);
        return view('bhu.course_registration')
                ->with(compact('courses'))
                ->with(compact('carryovers'))
                ->with('sn',1)
                ->with('current_nav','register_course')
                ->with('units',0);
    }

    public function dropCourse($courseId, CourseRegistration $registration)
    {
        $courseId = base64_decode($courseId);
        $dropCourse = $registration->dropCourse($courseId);

        if($dropCourse){
            return redirect()->back()->with([
                'flash_message' => $courseId . ' has been de-registered successfully!',
                'flash_type'    => 'success'
            ]);
        } else {
            return redirect()->back()->with([
                'flash_message' => $courseId . ' could not be de-registered!',
                'flash_type'    => 'danger'
            ]);
        }

    }

    public function printCourse($semester, CourseRegistration $registration){
        $studentId = session('regno');
        $sessionId = currentAcademicSession();
//        $semesterId = currentSemester();
        $semesterId = decryptId($semester);
        $nav = $semesterId == 1 ? 'print_form_fs' : 'print_form';
        $courses = $registration->registeredCourses($studentId, $sessionId, $semesterId);

        return view('bhu.print_registration')
                    ->with(compact('courses'))
                    ->with('sn',1)
                    ->with('current_nav',$nav)
                    ->with('semester_id',$semesterId)
                    ->with('units',0);

    }

    public function getResults(StudentResult $results)
    {
        $regno = session('regno');
        $sessionId = request()->has('session_id') ? request()->get('session_id') : currentAcademicSession();
        $semesterId = request()->has('semester') ? request()->get('semester') : currentSemester();
        $results = $results->studentSemesterResult($sessionId, $semesterId);
        $studentGPA = getStudentGPA($regno,$sessionId,$semesterId);
        $studentCGPA = getStudentCGPA($regno);
        return view('bhu.session_results')
                    ->with('sn',1)
                    ->with(compact('sessionId'))
                    ->with(compact('semesterId'))
                    ->with(compact('results'))
                    ->with(compact('studentGPA'))
                    ->with(compact('studentCGPA'))
                    ->with('current_nav','student_results');
    }
    
    public function postRegister(CourseRegistration $registration)
    {
        $registration->register(request()->only('course_id'),'2016/2017');

        return redirect()->back()->with([
            'flash_message' => 'Course(s) registered successfully!',
            'flash_type'    => 'success'
        ]);
    }
}
