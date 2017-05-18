<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class BhuAdminReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.admin');
    }

    public function manageAdminReports()
    {
        $deptId = null;
        $semesterId = null;
        $levelId = null;
        $sessionId = null;
        return view('admin.report.admin_reports')
                ->with('current_nav','manage_reports')
                ->with(compact('deptId'))
                ->with(compact('semesterId'))
                ->with(compact('sessionId'))
                ->with(compact('deptId'))
                ->with(compact('levelId'));
    }

    public function manageAdminDetailedReports()
    {
        $deptId = null;
        $semesterId = null;
        $levelId = null;
        $sessionId = null;
        return view('admin.report.admin_detailed_reports')
                ->with('current_nav','manage_reports')
                ->with(compact('deptId'))
                ->with(compact('semesterId'))
                ->with(compact('sessionId'))
                ->with(compact('deptId'))
                ->with(compact('levelId'));
    }

    public function manageFinalizeLevelResult($deptId, $sessionId, $semesterId, $levelId)
    {
        $deptId = decryptId($deptId);
        $sessionId = decryptId($sessionId);
        $semesterId = decryptId($semesterId);
        $levelId = decryptId($levelId);
        $finalize = finalizeResultReport($deptId, $sessionId, $semesterId, $levelId);
        return redirect()->back();
    }

    public function manageAdminFindResultSubmission(Request $request)
    {
        $courses = resultsReport($request);
        $deptId = $request->has('department_id')? $request->get('department_id') : null;
        $semesterId = $request->get('semester');
        $levelId = $request->get('level_id');
        $sessionId = $request->get('session_id');
        return view('admin.report.admin_reports')
                ->with('current_nav','manage_reports')
                ->with(compact('courses'))
                ->with(compact('deptId'))
                ->with(compact('semesterId'))
                ->with(compact('sessionId'))
                ->with(compact('deptId'))
                ->with(compact('levelId'));
    }

    public function manageAdminCourseResults($courseId, $sessionId, $semesterId)
    {
        $courseId = decryptId($courseId);
        $sessionId = decryptId($sessionId);
        $semesterId = decryptId($semesterId);
        $courses = manageAdminCourseResult($courseId, $sessionId, $semesterId);
        return view('admin.report.admin_report_course_result')
            ->with(compact('courses'))
            ->with('course_id', $courseId)
            ->with('semester_id', $semesterId)
            ->with('session_id', $sessionId)
            ->with('current_nav','manage_reports')
            ->with('sn',1);
    }

    public function manageAdminApproveCourseResults($courseId, $sessionId, $semesterId)
    {
        $courseId = decryptId($courseId);
        $sessionId = decryptId($sessionId);
        $semesterId = decryptId($semesterId);
        $approve = manageAdminReportApproveCourseResult($courseId, $sessionId, $semesterId);
        if($approve){
            return redirect()->back()->with([
                'flash_message' => 'Result Approved Successfully!',
                'flash_type'    => 'success'
            ]);
        } else {
            return redirect()->back()->with([
                'flash_message' => 'Result could not be Approved, please try again!',
                'flash_type'    => 'danger'
            ]);
        }
    }

    public function manageAdminDetailedCourseResults($deptId, $sessionId, $semesterId, $levelId)
    {
        $deptId = decryptId($deptId);
        $sessionId = decryptId($sessionId);
        $semesterId = decryptId($semesterId);
        $levelId = decryptId($levelId);
        $maxWidth = [];
//        $headerCourses = headerCourses($deptId, $sessionId, $semesterId, $levelId);
        $headerCourses = manageAdminDetailedResultsReports($deptId, $sessionId, $semesterId, $levelId, true);
//        $registeredCourses = manageAdminDetailedResultsReport($deptId, $sessionId, $semesterId, $levelId);
//        $registeredCourses = manageAdminDetailedResultsReports($deptId, $sessionId, $semesterId, $levelId);
//        $registeredStudents = manageAdminDetailedResultsReports($deptId, $sessionId, $semesterId, $levelId);
        $registeredStudents = manageAdminDetailedResultsReports($deptId, $sessionId, $semesterId, $levelId);
        return view('admin.report.admin_reports_results_detail')
                    ->with(compact('maxWidth'))
                    ->with(compact('headerCourses'))
                    ->with(compact('sessionId'))
                    ->with(compact('semesterId'))
                    ->with(compact('levelId'))
                    ->with(compact('deptId'))
                    ->with('sn',1)
                    ->with(compact('registeredStudents'));
    }

    public function manageAdminSummaryCourseResults($deptId, $sessionId, $semesterId, $levelId)
    {
        $deptId = decryptId($deptId);
        $sessionId = decryptId($sessionId);
        $semesterId = decryptId($semesterId);
        $levelId = decryptId($levelId);
        $maxWidth = [];
        $headerCourses = manageAdminDetailedResultsReports($deptId, $sessionId, $semesterId, $levelId, true);
//        $registeredCourses = manageAdminDetailedResultsReport($deptId, $sessionId, $semesterId, $levelId);
        $registeredStudents = manageAdminDetailedResultsReports($deptId, $sessionId, $semesterId, $levelId);
        return view('admin.report.admin_reports_results_summary')
                    ->with(compact('maxWidth'))
                    ->with(compact('headerCourses'))
                    ->with(compact('sessionId'))
                    ->with(compact('semesterId'))
                    ->with(compact('levelId'))
                    ->with(compact('deptId'))
                    ->with('sn',1)
                    ->with(compact('registeredStudents'));
    }

    public function manageAdminRemarkCourseResults($deptId, $sessionId, $semesterId, $levelId)
    {
        $deptId = decryptId($deptId);
        $sessionId = decryptId($sessionId);
        $semesterId = decryptId($semesterId);
        $levelId = decryptId($levelId);
//        $registeredStudents = manageAdminRemarkResultsReport($deptId, $sessionId, $semesterId, $levelId);
        $registeredStudents = manageAdminDetailedResultsReports($deptId, $sessionId, $semesterId, $levelId);
        return view('admin.report.admin_reports_results_remark')
                    ->with(compact('sessionId'))
                    ->with(compact('semesterId'))
                    ->with(compact('levelId'))
                    ->with(compact('deptId'))
                    ->with('sn',1)
                    ->with(compact('registeredStudents'));
    }

    public function manageAdminFindDetailedReport(Request $request)
    {
//        dd($request->all());
        $deptId = (session('role') == 'HOD' or session('role') == 'Lecturer') ? $deptId = encryptId(session('departments_department_id')) : $deptId = encryptId($request->get('department_id'));
        $sessionId = encryptId($request->get('session_id'));
        $semesterId = encryptId($request->get('semester'));
        $levelId = encryptId($request->get('level_id'));
        $reportType = $request->get('report');

        if($reportType == 'detailed_result') return redirect()->route('admin.report_detailed_results',[$deptId,$sessionId,$semesterId,$levelId]);
        if($reportType == 'detailed_summary') return redirect()->route('admin.report_results_summary',[$deptId,$sessionId,$semesterId,$levelId]);
        if($reportType == 'detailed_remark') return redirect()->route('admin.report_results_remark',[$deptId,$sessionId,$semesterId,$levelId]);
        return redirect()->back();
    }
}
