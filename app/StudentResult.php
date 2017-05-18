<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentResult extends Model
{
    protected $connection = 'mysql2';

    protected $table = 'course_registration';

    public $timestamps = false;

    public function studentSemesterResult($sessionId, $semesterId)
    {
        $regno = session('regno');

        return $this->where('sessions_session_id', $sessionId)
                    ->where('semester', $semesterId)
                    ->where('students_student_id', $regno)
                    ->where('approval_status', 'Senate')
                    ->get();
    }
}
