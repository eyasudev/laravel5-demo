$(document).ready(function () {

    $('#formModal').on('hidden.bs.modal', function () {
        html = '';
        $('#form_result').html(html);
        $('#sample_form')[0].reset();
        $('#store_image').html('');
    });

    function fade_out() {
        $(".alert").fadeOut().empty();
    }

    $('#user_table').DataTable({
        processing: true,
        serverSide: true,
        async: false,
        ajax: {
            url: "ajax-crud",
        },
        columns: [
            {
                data: 'image',
                name: 'image',
                render: function (data, type, full, meta) {
                    return "<img height='120px !important'' width='120px !important' src=/images/" + data + " width='70' class='img-thumbnail' />";
                },
                orderable: false
            },
            {
                data: 'first_name',
                name: 'first_name'
            },
            {
                data: 'last_name',
                name: 'last_name'
            },
            {
                data: 'email',
                name: 'email'
            },
            {
                data: 'address1',
                name: 'address1'
            },
            {
                data: 'phone',
                name: 'phone'
            },
            {
                data:       'action',
                name:       'action',
                orderable:   false
            }
        ]
    });

    $('#create_record').click(function () {
        $('.modal-title').text("Add New User");
        $('#action_button').val("Add");
        $('#action').val("Add");
        $('#formModal').modal('show');
    });

    $('#sample_form').on('submit', function (event) {
        event.preventDefault();
        if ($('#action').val() == 'Add') {
            $.ajax({
                url: "ajax-crud/store",
                method: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                dataType: "json",
                success: function (data) {
                    var html = '';
                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        $('.alert').html(data.success);
                        $(".alert").attr("style", "display:block");
                        $('html, body').animate({
                            scrollTop: $(".alert").offset().top
                        }, 1000);
                        setTimeout(fade_out, 5000);
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#sample_form')[0].reset();
                        $('#user_table').DataTable().ajax.reload();

                        $('#formModal').modal('hide');
                    }
                    $('#form_result').html(html);
                }
            })
        }

        if ($('#action').val() == "Update") {
            $.ajax({
                url: "ajax-crud/update",
                method: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                dataType: "json",
                success: function (data) {
                    var html = '';
                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        $('.alert').html(data.success);
                        $(".alert").attr("style", "display:block");
                        $('html, body').animate({
                            scrollTop: $(".alert").offset().top
                        }, 1000);
                        setTimeout(fade_out, 5000);
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#sample_form')[0].reset();
                        $('#store_image').html('');
                        $('#user_table').DataTable().ajax.reload();
                        $('#formModal').modal('hide');
                    }
                    $('#form_result').html(html);
                }
            });
        }
    });

    $(document).on('click', '.edit', function () {
        var id = $(this).attr('id');
        $('#form_result').html('');
        $.ajax({
            url: "/ajax-crud/" + id + "/edit",
            dataType: "json",
            success: function (html) {
                $('#first_name').val(html.data.first_name);
                $('#last_name').val(html.data.last_name);
                $('#email').val(html.data.user_profile.email);
                $('#address1').val(html.data.user_profile.address1);
                $('#address2').val(html.data.user_profile.address2);
                $('#phone').val(html.data.user_profile.phone);
                $('#store_image').html("<img src=images/" + html.data.user_profile.image + " width='70' class='img-thumbnail' />");
                $('#store_image').append("<input type='hidden' name='hidden_image' value='" + html.data.user_profile.image + "' />");
                $('#hidden_id').val(html.data.id);
                $('.modal-title').text("Update User");
                $('#action_button').val("Update");
                $('#action').val("Update");
                $('#formModal').modal('show');
            }
        })
    });

    var user_id;
    
    $(document).on('click', '.delete', function () {
        user_id = $(this).attr('id');
        $('.modal-title').text("Delete User");
        $('#confirmModal').modal('show');
    });

    $('#ok_button').click(function () {
        $.ajax({
            url: "ajax-crud/destroy/" + user_id,
            beforeSend: function () {
                $('#ok_button').text('Deleting...');
            },
            success: function (data) {
                setTimeout(function () {
                    $('.alert').html(data.success);
                    $(".alert").attr("style", "display:block");
                    $('html, body').animate({
                        scrollTop: $(".alert").offset().top
                    }, 1000);
                    setTimeout(fade_out, 5000);
                    $('#confirmModal').modal('hide');
                    $('#ok_button').text('OK');
                    $('#user_table').DataTable().ajax.reload();
                }, 2000);
            }
        })
    });
});