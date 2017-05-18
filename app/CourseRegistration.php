<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Excel;

class CourseRegistration extends Model
{
    protected $connection = 'mysql2';

    protected $table = 'course_registration';

    public $timestamps = false;

//    protected $fillable = ['course_id'];
    protected $guarded = [];

    public function register($courseIds, $session)
    {
        $registration = [];

        $courseIds = array_flatten($courseIds);

        if(!is_array($courseIds) and count($courseIds) < 0) return false;

        foreach ($courseIds as $courseId){
            if(!isCourseRegistered($courseId, $session)){
                $registration[] = [
                    'ca' => 0,
                    'exam' => 0,
                    'semester'          => currentSemester(),
                    'students_student_id'       => session('regno'),
                    'sessions_session_id'    => $session,
                    'courses_course_id' =>  $courseId
                ];
            }

        }

        if(count($registration) > 0){
            return $this->insert($registration);
        } else {
            return false;
        }

    }

    public function dropCourse($courseId)
    {
        return $this->where('students_student_id',session('regno'))
            ->where('courses_course_id',$courseId)
            ->delete();
    }

    public function registeredCourses($regno, $session)
    {
        return $this->where('students_student_id',$regno)
                        ->where('sessions_session_id',$session)
                        ->get();

    }

    public function saveCourseResult($cas, $exams, $students, $courseId, $session, $semester)
    {
        $casCount = count($cas['ca']);
        for ($i = 0; $i < $casCount; $i++) {
            $ca = (float) $cas['ca'][$i];
            $exam = (float) $exams['exam'][$i];
            $this->where('students_student_id', $students['student_id'][$i])
                    ->where('sessions_session_id', $session)
                    ->where('semester', $semester)
                    ->where('courses_course_id', $courseId)
                    ->update([
                        'ca' => ($ca <= 40) ? $ca : 0,
                        'exam' => ($exam <= 60) ? $exam : 0
                    ]);
        }
    }

//    public function saveCourseResultHod($lecturerId, $cas, $exams, $students, $courseId, $session, $semester)
    public function saveCourseResultHod($cas, $exams, $students, $courseId, $session, $semester)
    {
        $role = session('role');
        $casCount = count($cas['ca']);
        for ($i = 0; $i < $casCount; $i++) {
            $ca = (float) $cas['ca'][$i];
            $exam = (float) $exams['exam'][$i];
            $this->where('students_student_id', $students['student_id'][$i])
                    ->where('sessions_session_id', $session)
                    ->where('semester', $semester)
                    ->where('courses_course_id', $courseId)
                    ->update([
                        'ca' => ($ca <= 40) ? $ca : 0,
                        'exam' => ($exam <= 60) ? $exam : 0
                    ]);
        }
        // Finalize HOD editing privileges
//        return finalizeCourseResult($lecturerId, $courseId, $session, $role);
        return finalizeCourseResult($courseId, $session, $role);
    }

    public function uploadCourseResult($request, $courseId, $session, $semester)
    {
        if($request->hasFile('course_result')){
            $path = $request->file('course_result')->getRealPath();
            $data = Excel::load($path, function($reader) {
            })->get();
            if(!empty($data) && $data->count()){
                foreach ($data as $key => $value) {
                    $ca = (float) $value->ca;
                    $exam = (float) $value->exam;
                    $this->where('students_student_id', $value->matric)
                         ->where('courses_course_id', $courseId)
                         ->where('sessions_session_id', $session)
                         ->where('semester', $semester)
                         ->update(['ca' => ($ca <= 40) ? $ca : 0, 'exam' => ($exam <= 60) ? $exam : 0]);
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function exportCourseResult($courses, $courseId, $ext = 'xls')
    {
        $sn = 1;
        $titleArray[] = [
            'S/N',
            'Matriculation #',
            'Name',
            'C.A',
            'Exam',
            'Total',
            'Grade'
        ];
        $dataArray = [];
        if(count($courses) > 0){
            foreach ($courses as $course){
                $total = $course->ca + $course->exam;
                    $dataArray[] = [
                        $sn++,
                        $course->students_student_id,
                        studentNameFromMatriculationNo($course->students_student_id),
                        $course->ca,
                        $course->exam,
                        $total,
                        expandGrade($total)
                    ];
            }
        }

        $exportArray = array_merge($titleArray, $dataArray);

        Excel::create($courseId . '_Results', function($excel) use($exportArray, $courseId) {

            $excel->sheet($courseId . '_Results', function($sheet) use($exportArray) {

                $sheet->fromArray($exportArray, null, 'A1', true, false);
                $sheet->setPageMargin(0.25);
                $sheet->cells('A1:G1', function($cells) {
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                });
                $style = array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    )
                );

                $sheet->getDefaultStyle()->applyFromArray($style);

              $sheet->setSize('A1', 5);
              $sheet->setSize('B1', 25);
              $sheet->setSize('C1', 25);
              $sheet->setSize('D1', 10);
              $sheet->setSize('E1', 10);
              $sheet->setSize('F1', 10);
              $sheet->setSize('G1', 10);

            });

        })->export($ext);
    }
}
