
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
            "url": baseUrl + "/admin/getDepartmentData",
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
        if (input.hasClass('required') && $.trim(input.val()) == '') {
            flag2 = 1;
            flag = 1;
            if (input.parent().hasClass('input-group')) {
                // If the input is inside an input-group, append the error message to the input-group div
                input.parent().after("<div class='err_warning blink'>*Required " + input.attr('placeholder') + "</div>");
            } else {
                // If not, append the error message after the input element
                input.after("<div class='err_warning blink'>*Required " + input.attr('placeholder') + "</div>");
            }
            $(input).addClass("errborder");
        }

        if (input.attr('data-bind') && flag2 == 0)
        {
            var str = input.val();
            var temp1 = new RegExp(reg_match[input.attr('data-bind')]);
            if ($.trim(str) != '')
            {
                if (!temp1.test(str))
                {
                    flag1 = 1;
                    if (input.parent().hasClass('input-group')) {
                        // If the input is inside an input-group, append the error message to the input-group div
                        input.parent().after("<div class='err_warning blink'>*Invalid Field " + input.attr('placeholder') + "</div>");
                    } else {
                        // If not, append the error message after the input element
                        input.after("<div class='err_warning blink'>*Invalid Field " + input.attr('placeholder') + "</div>");
                    }
                    $(input).addClass("errborder");
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
        var form = $("#addNewDepartmentForm");
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
                    if (result.validation && result.validation != undefined) {
                        if (result.validation.departmentName) {
                            $('#departmentName').after("<div class='err_warning blink'>" + result.validation.departmentName + "</div>");
                            $('#departmentName').addClass("errborder");
                        }

                        if (result.validation.departmentStatus) {
                            $('#departmentStatus').after("<div class='err_warning blink'>" + result.validation.departmentStatus + "</div>");
                            $('#departmentStatus').addClass("errborder");
                        }
                    }
                    
                    setTimeout(function () {
                        $(".card-title-desc").css('display', 'none');
                        document.getElementById(form_name).reset();
                    }, 3000);
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




