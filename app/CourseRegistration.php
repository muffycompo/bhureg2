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

    public function registeredCourses($regno, $session, $semester)
    {
        return $this->where('students_student_id',$regno)
                        ->where('sessions_session_id',$session)
                        ->where('semester',$semester)
                        ->get();

    }

    public function saveCourseResult($cas, $exams, $students, $courseId, $session, $semester, $altEntry = null)
    {
        $casCount = count($cas['ca']);
        $examsCount = count($exams['exam']);

        if($casCount > 0){
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
        } else {
            for ($i = 0; $i < $examsCount; $i++) {
                $exam = (float) $exams['exam'][$i];
                $this->where('students_student_id', $students['student_id'][$i])
                    ->where('sessions_session_id', $session)
                    ->where('semester', $semester)
                    ->where('courses_course_id', $courseId)
                    ->update([
                        'ca' => 0,
                        'exam' => ($exam <= 100) ? $exam : 0
                    ]);
            }
        }

        // Update Alternate Entry
        $lectureId = lecturerIdFromCourse($courseId,$session);
        //updateAltEntryForResult($lectureId,$courseId,$session,$altEntry);

    }

//    public function saveCourseResultHod($lecturerId, $cas, $exams, $students, $courseId, $session, $semester)
    public function saveCourseResultHod($cas, $exams, $students, $courseId, $session, $semester)
    {
        $role = session('role');
        $casCount = count($cas['ca']);
        $examsCount = count($exams['exam']);
        if($casCount > 0){
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
        } else {
            for ($i = 0; $i < $examsCount; $i++) {
                $exam = (float) $exams['exam'][$i];
                $this->where('students_student_id', $students['student_id'][$i])
                    ->where('sessions_session_id', $session)
                    ->where('semester', $semester)
                    ->where('courses_course_id', $courseId)
                    ->update([
                        'ca' => 0,
                        'exam' => ($exam <= 100) ? $exam : 0
                    ]);
            }
        }

        // Finalize HOD editing privileges
//        return finalizeCourseResult($lecturerId, $courseId, $session, $role);
        return finalizeCourseResult($courseId, $session, $semester, $role);
    }

    public function uploadCourseResult($request, $courseId, $session, $semester, $altEntry = null)
    {
        if($request->hasFile('course_result')){
            $path = $request->file('course_result')->getRealPath();
            $data = Excel::load($path, function($reader) {
            })->get();
            if(!empty($data) && $data->count()){
                foreach ($data as $key => $value) {
                    if(is_null($altEntry) || $altEntry == 0){
                        $ca = (float) $value->ca;
                        $exam = (float) $value->exam;
                        $this->where('students_student_id', $value->matric)
                            ->where('courses_course_id', $courseId)
                            ->where('sessions_session_id', $session)
                            ->where('semester', $semester)
                            ->update([
                                'ca' => ($ca <= 40) ? $ca : 0,
                                'exam' => ($exam <= 60) ? $exam : 0
                            ]);

                    } else {
                        $exam = (float) $value->exam;
                        $this->where('students_student_id', $value->matric)
                            ->where('courses_course_id', $courseId)
                            ->where('sessions_session_id', $session)
                            ->where('semester', $semester)
                            ->update([
                                'ca' => 0,
                                'exam' => ($exam <= 100) ? $exam : 0
                            ]);

                    }
                }
                // Update Alternate Entry
                $lectureId = lecturerIdFromCourse($courseId,$session);
                //updateAltEntryForResult($lectureId,$courseId,$session,$altEntry);
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
            'Students Result for ' . $courseId,
        ];
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

        Excel::create($courseId . '_Results', function($excel) use($exportArray, $courseId, $ext) {

            $excel->sheet($courseId . '_Results', function($sheet) use($exportArray, $ext) {

                $sheet->fromArray($exportArray, null, 'A1', true, false);
                $sheet->setPageMargin(0.25);
                $sheet->mergeCells('A1:G1');
                $sheet->setAutoSize(true);
                if ($ext != 'pdf'){
                    $sheet->setAllBorders(\PHPExcel_Style_Border::BORDER_THIN);
                }
                $sheet->cells('A1:G1', function($cells) {
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                });
                $sheet->cells('A2:G2', function($cells) {
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                });
                $style = array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    )
                );

              $sheet->getDefaultStyle()->applyFromArray($style);

              $sheet->setSize('A2', 5);
              $sheet->setSize('B2', 25);
              $sheet->setSize('D2', 10);
              $sheet->setSize('E2', 10);
              $sheet->setSize('F2', 10);
              $sheet->setSize('G2', 10);

            });

        })->export($ext);
    }

    public function exportRegisteredStudentsForCourses($courses, $courseId, $ext = 'xlsx')
    {
        $sn = 1;
        $titleArray[] = [
            'List of Registered Student(s) for ' . $courseId,
        ];
        $titleArray[] = [
            'S/N',
            'Matriculation #',
            'Name',
        ];

        $dataArray = [];
        if(count($courses) > 0){
            foreach ($courses as $course){
                $dataArray[] = [
                    $sn++,
                    $course->students_student_id,
                    studentNameFromMatriculationNo($course->students_student_id),
                ];
            }
        }

        $exportArray = array_merge($titleArray, $dataArray);

        Excel::create($courseId . '_Registered_Students', function($excel) use($exportArray, $courseId, $ext) {

            $excel->sheet($courseId . '_Reg_Students', function($sheet) use($exportArray, $ext) {

                $sheet->fromArray($exportArray, null, 'A1', true, false);
                $sheet->setPageMargin(0.25);
                $sheet->mergeCells('A1:C1');
                $sheet->setAutoSize(true);
                if($ext != 'pdf') {
                    $sheet->setAllBorders(\PHPExcel_Style_Border::BORDER_THIN);
                }
                $sheet->cells('A1:C1', function($cells) {
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                });
                $sheet->cells('A2:C2', function($cells) {
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                });
                $style = [
                    'alignment' => [
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ]
                ];

                $sheet->getDefaultStyle()->applyFromArray($style);
                $sheet->setSize('A2',10);
                $sheet->setSize('B2',25);
            });

        })->export($ext);
    }
}
