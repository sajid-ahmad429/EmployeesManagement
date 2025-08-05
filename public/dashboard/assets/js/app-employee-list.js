
var acftkname = $('.txt_csrfname').attr('name'); // acf Token name
var acftknhs = $('.txt_csrfname').val(); // acf hash

$(function () {
    var DataTables_Table_part_list = $('#datableDemartmentList').DataTable({
        "processing": true, 
        "serverSide": true, 
        "iDisplayLength": 25,
        "info": true,
        "bStateSave": true,
        "order": [[0, 'desc']], 
        "responsive": true,
        "lengthMenu": [
            [10, 25, 50, 100, 200, 500, 600, 750],
            [10, 25, 50, 100, 200, 500, 600, 750]
        ],
        "fixedHeader": true,
        "dom": "<'row'<'col-6'B><'col-4'f><'col-2 text-right'l>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12'ip>>",
        "buttons": [
            'csv', 'excel', 'pdf',
            {
                extend: 'colvis',
                text: 'Column Visibility',
                columns: ':not(.noVis)'
            }
        ],
        "columnDefs": [
            {
                "targets": [1], // Adjust based on your table
                "orderable": false
            }
        ],
        "ajax": {
            "url": baseUrl + "/admin/getEmployeeData",
            "type": "POST",
            "data": function (data) {
                data.id = ''; // Remove or populate if needed
                data[acftkname] = acftknhs;
            },
            "error": function (xhr, error, thrown) {
                console.error("AJAX Error:", error, thrown);
                alert("An error occurred while fetching data. Please try again.");
            }
        },
        "drawCallback": function (settings) {
            var response = settings.json;
            if (response) {
                $(".totalInActiveParts").html(response.totalInActiveRecods || 0);
                $(".totalActiveParts").html(response.totalActiveRecods || 0);
                $(".totalParts").html(response.recordsTotal || 0);
                $("#pendingVerifyCount").html(response.recordsPending || 0);
            } else {
                console.error("Invalid server response.");
            }
        }
    });

    // Filter and Reset Buttons
    $('#btn-filter').click(function () {
        DataTables_Table_part_list.ajax.reload();
    });
    $('#btn-reset').click(function () {
        $('#form-filter')[0].reset();
        DataTables_Table_part_list.ajax.reload();
    });

    // Initial search reset
    DataTables_Table_part_list.search('').draw();
});



function validate_form(form_name) {
    var flag = 0;
    var flag2 = 0;
    var flag1 = 0;
    var reg_match = [];
    reg_match['name'] = /^[a-zA-Z\s]+$/;
    reg_match['text'] = /^[a-zA-Z0-9\-\s,.\']+$/;
    reg_match['content'] = /^[a-zA-Z0-9\-\s,.]+$/;
    reg_match['date'] = /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/;
    reg_match['numberCharacter'] = /^[a-zA-Z0-9_.-]*$/;
    reg_match['datetime'] = /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) (2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/;
    reg_match['url'] = /^(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:/~+#-]*[\w@?^=%&amp;/~+#-])?$/;
    reg_match['number'] = /^[0-9]/;
    reg_match['decimal'] = /^\s*-?[1-9]\d*(\.\d{1,2})?\s*$/;
    reg_match['password'] = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=(?:[^@$!%*?&#]*[@$!%*?&#]){1,3})([A-Za-z\d@$!%*?&#]){8,}$/;
    reg_match['mobileNumber'] = /^[0-9]{10}$/;
    reg_match['email'] = /^\w+([-+.'][^\s]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
    $(".err_warning").each(function (index) {
        $(this).remove();
    });
    $(".errborder").each(function (index) {
        $(this).removeClass("errborder");
    });
    $('#' + form_name + ' input,#' + form_name + ' select,#' + form_name + ' textarea,#' + form_name + ' checkbox,#' + form_name + ' date').each(function (index)
    {

        flag2 = 0;
        var input = $(this);
        // if (input.hasClass('required') && $.trim(input.val()) == '') {
        //     flag2 = 1;
        //     flag = 1;
        //     if (input.parent().hasClass('input-group')) {
        //         // If the input is inside an input-group, append the error message to the input-group div
        //         input.parent().after("<div class='err_warning blink'>*Required " + input.attr('placeholder') + "</div>");
        //     } else {
        //         // If not, append the error message after the input element
        //         input.after("<div class='err_warning blink'>*Required " + input.attr('placeholder') + "</div>");
        //     }
        //     $(input).addClass("errborder");
        // }

        if (input.hasClass('required') && $.trim(input.val()) == '') {
            flag2 = 1;
            flag = 1;
            var errorMessage = "*Required " + input.attr('placeholder');
        
            // Append error message after the closing div of .input-group
            if (input.closest('.input-group').length) {
                // Insert the error message after the parent input-group div
                input.closest('.input-group').after("<div class='err_warning blink'>" + errorMessage + "</div>");
            } else {
                // If not, append the error message after the input element
                input.after("<div class='err_warning blink'>*Required " + input.attr('placeholder') + "</div>");
            }
        
            $(input).addClass("errborder"); // Add error styling to the input
        }
        
        if (input.attr('data-bind') && flag2 == 0)
        {
            var str = input.val();
            var temp1 = new RegExp(reg_match[input.attr('data-bind')]);
            if ($.trim(str) != '')
            {
                if (!temp1.test(str))
                {
                    flag2 = 1;
                    flag = 1;

                    var errorMessage = "*Invalid Field " + input.attr('placeholder');
                    // Append error message after the closing div of .input-group
                    if (input.closest('.input-group').length) {
                        // Insert the error message after the parent input-group div
                        input.closest('.input-group').after("<div class='err_warning blink'>" + errorMessage + "</div>");
                    } else {
                        // If not, append the error message after the input element
                        input.after("<div class='err_warning blink'>*Required " + input.attr('placeholder') + "</div>");
                    }
                
                    $(input).addClass("errborder"); // Add error styling to the input
                }
            }
        }

        // Check if the input is for password or confirmPassword
        if ((input.attr('data-bind') === 'password' || input.attr('data-bind') === 'confirmPassword') && flag2 == 0) {
            var str = input.val();
            var regex = reg_match[input.attr('data-bind')]; // Get the corresponding regex for password or confirmPassword
            
            // Check if the field is not empty and if the value matches the regex
            if ($.trim(str) !== '') {
                // If it's password field, validate with regex
                if (input.attr('data-bind') === 'password' && !regex.test(str)) {
                    flag1 = 1;
                    var errorMessage = "*Invalid password " + input.attr('placeholder');
                    
                    // Append error message after the input group or input element
                    if (input.parent().hasClass('input-group')) {
                        input.closest('.input-group').after("<div class='err_warning blink'>" + errorMessage + "</div>");
                    } else {
                        input.after("<div class='err_warning blink'>" + errorMessage + "</div>");
                    }

                    $(input).addClass("errborder"); // Add error styling to the input field
                }
                
                // If it's confirm password field, check if it matches the password field value
                if (input.attr('data-bind') === 'confirmPassword') {
                    var passwordValue = $('#password').val(); // Get the password value
                    
                    // Check if the confirm password does not match the password
                    if (str !== passwordValue) {
                        flag1 = 1;
                        var errorMessage = "*Passwords do not match";
                        
                        // Append error message after the input group or input element
                        if (input.closest('.input-group').length) {
                            // Insert the error message after the parent input-group div
                            input.closest('.input-group').after("<div class='err_warning blink'>" + errorMessage + "</div>");
                        } else {
                            input.after("<div class='err_warning blink'>" + errorMessage + "</div>");
                        }

                        $(input).addClass("errborder"); // Add error styling to the input field
                    }
                }
            }
        }

        if (input.hasClass('select2') && input.hasClass('select2Required')) {
            var getId = $(this).attr('id');
            var getAttribute = $(this).attr('multiple');
            var inputValue = $.trim($("#" + getId).val());

            // Check if the error container exists
            var errorContainer = $("#" + getId).parent().find(".error-container");

            if (inputValue === '' || parseInt(inputValue) === 0) {
                $(".error-container").css("margin-top", "11px");
                flag2 = 1;
                flag = 1;

                var errorMessage = "<div class='err_warning blink'>*Required " + $("#" + getId).attr('placeholder') + "</div>";

                // If the error container exists, just update the error message
                if (errorContainer.length > 0) {
                    errorContainer.html(errorMessage);
                } else {
                    // If the error container doesn't exist, create it and append the error message
                    $("#" + getId).parent().append("<div class='error-container'>" + errorMessage + "</div>");
                }

                // Add error classes to the input and the select2 container
                $("#" + getId).addClass("errborder");
                if (getAttribute == 'multiple') {
                    $(".error-container").css("margin-top", "11px");
                    var select2Container = $("#" + getId).parent().find(".select2-container--default .select2-selection--multiple");
                    select2Container.addClass("errborder");
                } else {
                    $(".error-container").css("margin-top", "11px");
                    var select2Container = $("#select2-" + getId + "-container");
                }

            } else {
                // Reset or remove error classes
                $("#" + getId).removeClass("errborder");
                $("#select2-" + getId + "-container").removeClass("errborder");

                // Clear error messages in the associated error container
                if (errorContainer.length > 0) {
                    errorContainer.html("");
                }
            }
        }

        if (input.attr('type') == 'file')
        {
            var file_selected = $(input).get(0).files;
            if (input.hasClass('required')) {
                if (file_selected.length == 0) {
                    flag1 = 1;
                    $(input).after("<div class='err_warning blink'>*Invalid Field " + input.attr('placeholder') + "</div>");
                    $(input).addClass("errborder");
                } else if (file_selected.length > 5)
                {
                    flag1 = 1;
                    $(input).after("<div class='err_warning blink'>*Invalid Field " + input.attr('placeholder') + "</div>");
                    $(input).addClass("errborder");
                }
            }
        }

        if (input.attr('type') == 'select') {
            var str = input.val();
            if (input.hasClass('required') && (str == '' || str == 0) && !input.hasClass('errborder'))
            {
                flag1 = 1;
                if (input.data('select2'))
                {
                    $(input).select2({containerCssClass: "errborder"});
                    $(input).appendTo("<div class='err_warning blink'>*Required " + input.attr('placeholder') + "</div>");
                } else {
                    $(input).after("<div class='err_warning blink'>*Required " + input.attr('placeholder') + "</div>");
                    $(input).addClass("errborder");
                }
            }
        }
    });
    if (flag == 1 || flag1 == 1)
    {
        efCount = 0;
        $(".errborder").each(function (index) {
            if (efCount == 0)
                $(this).focus();
            efCount++;
        });
        return false;
    } else {
        var form = $("#addNewEmployeeForm");
        var formData = new FormData(form[0]);
        $.ajax({
            async: false,
            dataType: 'json',
            url: form.attr("action"),
            method: form.attr("method"),
            data: formData,
            processData: false,
            contentType: false,
            success: function (result) {
                console.log(result);
                acftkname = result.acftkn.acftkname;
                acftknhs = result.acftkn.acftknhs;
                $(".txt_csrfname").val(acftknhs);
                if (result.status != 1) {
                    $(".card-title-desc").html('');
                    $(".card-title-desc").css('display', 'block');
                    $(".card-title-desc").html(result.message);

                    if (result.validation && result.validation !== undefined) {
                        Object.keys(result.validation).forEach(function (field) {
                            const fieldElement = $("#" + field);
                            fieldElement.after("<div class='err_warning blink'>" + result.validation[field] + "</div>");
                            fieldElement.addClass("errborder");
                        });
                    }
                } else if (result.status == 1) {
                    // Close offcanvas before Ajax
                    var myOffcanvas = document.getElementById('offcanvasAddUser');
                    var bsOffcanvas = bootstrap.Offcanvas.getInstance(myOffcanvas) || new bootstrap.Offcanvas(myOffcanvas);
                    bsOffcanvas.hide();

                    Swal.fire({icon: "success",title: result.message ,showConfirmButton: false,timer: 1500});
                    setTimeout(function () {
                        location.reload();
                    }, 3000);
                }

            }
        });
    }
}




