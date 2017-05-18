$(document).ready(function(){
    // Enable Tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Dynamic Dropdown
    var departmentDropdown = $('#department');

    // Default Departmental Lecturers
    $.ajax({
        type: 'get',
        url:'/admin/department_lecturer',
        data: {
            department_id: departmentDropdown.val()
        },
        success: function(data) {
            var options = '';
            var lecturerData = $.parseJSON(data);
            $.each(lecturerData, function(i, lecturer) {
                options += '<option value="' + lecturer.user_id + '">' + lecturer.first_name + ' ' + lecturer.last_name + ' ('+ lecturer.user_id +')</option>';
            });

            $('#lecturer').html(options);

        }
    });

    departmentDropdown.on('change', function () {
        var departmentId = departmentDropdown.val();

        $.ajax({
            type: 'get',
            url:'/admin/department_lecturer',
            data: {
                department_id: departmentId
            },
            success: function(data) {

                var options = '';
                var lecturerData = $.parseJSON(data);
                $.each(lecturerData, function(i, lecturer) {
                    options += '<option value="' + lecturer.user_id + '">' + lecturer.first_name + ' ' + lecturer.last_name + ' ('+ lecturer.user_id +')</option>';
                });

                $('#lecturer').html(options);

            }
        });
    });
});