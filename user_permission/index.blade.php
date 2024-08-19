@extends('backend.layouts.master')

@section('content')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container">
        <h2>User Permission Management</h2>
        <form id="permission-form">
            <div class="list_ajax"></div>

            <div class="table-responsive">
                <div class="table_user_permission_ajax"></div>
            </div>
            {{-- <button type="button" id="save-all" class="btn btn-primary">Save All</button> --}}
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Set up CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            populateDropdown();
        });

        // Populate the dropdown
        function populateDropdown() {
            $.ajax({
                url: "{{ route('get.role.list') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    var resultHTML = '';
                    resultHTML +=
                        '<div class="row"><div class="col-sm-3"><div class="form-group"><select id="role_id" name="role_id" class="form-control"><option value="">Select Role</option>';
                    $.each(response.list, function(key, value) {
                        resultHTML += '<option value="' + value.id + '"> ' + value.role_name + '</option>';
                    });
                    resultHTML += '</select></div></div></div>';
                    $(".list_ajax").html(resultHTML);
                    $('#role_id').on('change', function() {
                        ajax_user_permission_table();
                    });
                },
                error: function() {
                    alert('Failed to load roles.');
                }
            });
        }

        // Save all permissions
        function form_submit_function() {
            // Disable the Save All button to prevent multiple submissions
            $("#save_all_btn").prop('disabled', true);

            // Make AJAX request to update permissions
            $.ajax({
                url: "{{ route('update.permissions') }}", // URL for updating permissions
                type: 'POST',
                data: $('#permission-form').serialize(), // Serialize form data
                success: function(response) {
                    if (response.status === 'Success') {
                        alert('Permissions updated successfully!');
                        location.reload();
                        // Optionally refresh the permissions table or perform other UI updates
                        ajax_user_permission_table(); // Call function to reload permissions table
                    } else {
                        alert('Failed to update permissions.');
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error details:', error);
                    alert('Failed to save permissions. Please try again.');
                },
                complete: function() {
                    // Re-enable the Save All button after request completion
                    $("#save_all_btn").prop('disabled', false);
                }
            });
        }

        function ajax_user_permission_table() {
            var role_id = $('#role_id').val();
            if (role_id) {
                $.ajax({
                        url: "{{ route('get.permissions') }}",
                        type: 'POST',
                        data: {
                            role_id: role_id,
                            _token: '{{ csrf_token() }}'
                        },
                    })
                    .done(function(data) {
                        console.log(data);
                        var count = 0;
                        var tableHTML =
                            '<table id="datatable" class="table table-bordered table-striped table-hover js-exportable dataTable"><thead><tr><th>Menu Name</th><th>Menu Type</th>';

                        @if (Auth::user()->id == 1)
                        tableHTML += '<th class="text-center">Add</th><th class="text-center">Edit</th><th class="text-center">Delete</th><th class="text-center">Action</th>';
                        @else
                        tableHTML += '<th class="text-center">Add</th><th class="text-center">Edit</th><th class="text-center">Delete</th>';
                        @endif

                        tableHTML += '</tr></thead><tbody>';
                        tableHTML += '<tr><td></td><td></td>';

                        @if (Auth::user()->id == 1)
                        tableHTML += '<td class="text-center"><input type="checkbox" id="select_all_add" onclick="select_al_add()" name="add_all" value=""><label for="select_all_add">Select All</label></td>';
                        tableHTML += '<td class="text-center"><input type="checkbox" id="select_all_edit" onclick="select_al_edit()" name="edit_all" value=""><label for="select_all_edit">Select All</label></td>';
                        tableHTML += '<td class="text-center"><input type="checkbox" id="select_all_delete" onclick="select_al_delete()" name="delete_all" value=""><label for="select_all_delete">Select All</label></td>';
                        tableHTML += '<td class="text-center"><button type="button" id="save_all_btn" class="btn btn-sm btn-warning" onclick="form_submit_function()"><i class="fa fa-save"></i> Save All</button></td>';
                        @endif

                        tableHTML += '</tr>';

                        $.each(data.list, function(key, value) {
                            var add_status = value.add_flag == "1" ? "checked" : "";
                            var edit_status = value.edit_flag == "1" ? "checked" : "";
                            var delete_status = value.delete_flag == "1" ? "checked" : "";
                            var disabled = {{ Auth::user()->id }} != 1 ? 'disabled' : '';

                            tableHTML += '<tr>' +
                                '<td>' +
                                '<input type="hidden" name="permission_id[]" id="permission_id_' + count + '" value="' + value.permission_id + '">' +
                                '<input type="hidden" name="menu_id[]" id="menu_id_' + count + '" value="' + value.menu_id + '">' +
                                value.menu_name +
                                '</td>' +
                                '<td class="text-center">' + value.menu_type.toUpperCase() + '</td>' +
                                '<td class="text-center"><input type="checkbox" id="add_' + count + '" name="add_' + count + '" value="1" ' + add_status + ' onchange="not_selected_add(' + count + ')" class="checkBoxClass" ' + disabled + '><label for="add_' + count + '">Yes</label></td>' +
                                '<td class="text-center"><input type="checkbox" id="edit_' + count + '" name="edit_' + count + '" value="1" ' + edit_status + ' onchange="not_selected_edit(' + count + ')" class="checkBoxClassEdit" ' + disabled + '><label for="edit_' + count + '">Yes</label></td>' +
                                '<td class="text-center"><input type="checkbox" id="delete_' + count + '" name="delete_' + count + '" value="1" ' + delete_status + ' onchange="not_selected_delete(' + count + ')" class="checkBoxClassDelete" ' + disabled + '><label for="delete_' + count + '">Yes</label></td>';

                            @if (Auth::user()->id == 1)
                            tableHTML += '<td class="text-center"><button type="button" class="btn btn-sm btn-success submit_button_ajax" data-id="' + count + '"><i class="fa fa-save"></i> Save</button></td>';
                            @endif

                            tableHTML += '</tr>';

                            count++;
                        });

                        tableHTML += '</tbody></table>';
                        $(".table_user_permission_ajax").html(tableHTML);

                        // Attach click event handler for single row save buttons
                        $('.submit_button_ajax').on('click', function() {
                            var count = $(this).attr('data-id');
                            form_submit_function_single(count);
                        });
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        console.error("Request failed: ", textStatus, errorThrown);
                    });
            } else {
                $(".table_user_permission_ajax").html("");
            }
        }

        function not_selected_add(count) {
            if (!$('#select_all_add').is(":checked")) {
                $('#select_all_add').prop('checked', false);
            }
        }

        function not_selected_edit(count) {
            if (!$('#select_all_edit').is(":checked")) {
                $('#select_all_edit').prop('checked', false);
            }
        }

        function not_selected_delete(count) {
            if (!$('#select_all_delete').is(":checked")) {
                $('#select_all_delete').prop('checked', false);
            }
        }

        function select_al_add() {
            if ($('#select_all_add').is(":checked")) {
                $('.checkBoxClass').each(function() {
                    $(this).prop('checked', true);
                });
            } else {
                $('.checkBoxClass').prop('checked', false);
            }
        }

        function select_al_edit() {
            if ($('#select_all_edit').is(":checked")) {
                $('.checkBoxClassEdit').each(function() {
                    $(this).prop('checked', true);
                });
            } else {
                $('.checkBoxClassEdit').prop('checked', false);
            }
        }

        function select_al_delete() {
            if ($('#select_all_delete').is(":checked")) {
                $('.checkBoxClassDelete').each(function() {
                    $(this).prop('checked', true);
                });
            } else {
                $('.checkBoxClassDelete').prop('checked', false);
            }
        }

        function form_submit_function_single(count) {
            var role_id = $('#role_id').val();
            var permission_id = $('#permission_id_' + count).val();
            var menu_id = $('#menu_id_' + count).val();
            var add_flag = $('#add_' + count).is(':checked') ? 1 : 0;
            var edit_flag = $('#edit_' + count).is(':checked') ? 1 : 0;
            var delete_flag = $('#delete_' + count).is(':checked') ? 1 : 0;

            $.ajax({
                url: "{{ route('update.single.permission') }}",
                type: 'POST',
                data: {
                    role_id: role_id,
                    permission_id: permission_id,
                    menu_id: menu_id,
                    add_flag: add_flag,
                    edit_flag: edit_flag,
                    delete_flag: delete_flag,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'Success') {
                        alert('Permissions updated successfully!');
                        location.reload();
                    } else {
                        alert('Failed to update permissions.');
                        location.reload();
                    }
                },
                error: function() {
                    alert('Failed to save permissions. Please try again.');
                }
            });
        }
    </script>
@endsection
