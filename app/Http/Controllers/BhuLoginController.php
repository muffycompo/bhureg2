<?php

namespace App\Http\Controllers;

use App\CourseRegistration;

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
        $courses = recommendedDepartmentalCourses(session('deptid'),session('levelid'),1,'2016/2017');
        $carryovers = carryOverCourses(session('regno'),[1,4]);
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

    public function printCourse(CourseRegistration $registration){
        $courses = $registration->registeredCourses(session('regno'),'2016/2017');
//        dd($courses);
        return view('bhu.print_registration')
                    ->with(compact('courses'))
                    ->with('sn',1)
                    ->with('current_nav','print_form')
                    ->with('units',0);

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
