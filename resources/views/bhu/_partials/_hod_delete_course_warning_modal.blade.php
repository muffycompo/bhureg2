<div class="modal fade" tabindex="-1" role="dialog" id="hodDeleteCourseModal{{ $sn }}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-danger"><span class="glyphicon glyphicon-warning-sign"></span> WARNING!!!</h4>
            </div>
            <div class="modal-body">
                <p class="text-danger">Are You SURE You Want To PROCEED? Doing So Will affect all students that REGISTERED for the Course!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a href="{{ route('admin.hod_manage_delete_course',[encryptId($course->courses_course_id)]) }}" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>