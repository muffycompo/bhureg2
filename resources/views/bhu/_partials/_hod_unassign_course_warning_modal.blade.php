<div class="modal fade" tabindex="-1" role="dialog" id="hodUnassignCourseModal{{ $sn }}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-danger"><span class="glyphicon glyphicon-warning-sign"></span> WARNING!!!</h4>
            </div>
            <div class="modal-body">
                <p class="text-danger">Are You SURE You Want To PROCEED? Doing So will make <strong>{{ changeStringToTitleCase($assignedCourse->first_name) . ' ' . changeStringToTitleCase($assignedCourse->last_name) }}</strong> unable to manage <strong>{{ $assignedCourse->courses_course_id }}</strong>!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a href="{{ route('admin.hod_manage_unaasign_course',[encryptId($assignedCourse->user_id),encryptId($assignedCourse->courses_course_id),encryptId($assignedCourse->sessions_session_id)]) }}" class="btn btn-danger">Unassign</a>
            </div>
        </div>
    </div>
</div>