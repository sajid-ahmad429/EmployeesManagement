<?php
$roleName = '';
if (isset($_SESSION['employee_type'])) {
    switch ($_SESSION['employee_type']) {
        case 'admin':
            $roleName = 'admin';
            break;
        case 'employee':
            $roleName = 'employee';
            break;
    }
}
?> 

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-primary rounded">
                                    <i class="tf-icons mdi mdi-office-building mdi-24px"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="d-flex align-items-center">
                                    <h5 class="totalParts mb-0"></h5>
                                </div>
                                <small class="text-muted">Total Department</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-success rounded">
                                    <i class="tf-icons mdi mdi-office-building mdi-24px"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="d-flex align-items-center">
                                    <h5 class="totalActiveParts mb-0"></h5>
                                </div>
                                <small class="text-muted">Active Department</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-danger rounded">
                                    <i class="tf-icons mdi mdi-office-building mdi-24px"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="d-flex align-items-center">
                                    <h5 class="totalInActiveParts mb-0"></h5>
                                </div>
                                <small class="text-muted">Inactive Department</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Size List Table -->
        <div class="card page-content masterPage">
            <div class="card-header border-bottom">
                <div class="d-flex justify-content-between align-items-center py-3 gap-3 gap-md-0">
                    <h5 class="card-title mb-0">Department List</h5>
                    <button class="btn btn-secondary add-new btn-primary" tabindex="0"
                                aria-controls="DataTables_Table_0" type="button" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasAddUser" onclick="addContent('addNewDepartmentForm');">
                            <span><i class="bx bx-plus me-0 me-lg-2"></i>
                                <span class="d-none d-lg-inline-block">Add New Department</span></span>
                        </button>
                </div>
                <div class="d-flex justify-content-between align-items-center row py-3 gap-3 gap-md-0">
                    <div class="col-md-4 user_plan">
                        <?php if (isset($validation)) : ?>
                            <div class="col-12">
                                <div class="alert alert-danger text-center" role="alert">
                                    <?= $validation->listErrors() ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php
                        echo "<div class='text-danger text-center'>";
                        if (isset($error_message)) {
                            echo $error_message;
                        }
                        echo "</div>";
                        ?>
                        <?php if (isset($_SESSION['msg'])) : ?>
                            <div class="col-12">
                                <div class="alert alert-success text-center" role="alert">
                                    <?= $_SESSION['msg']; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['errmsg'])) : ?>
                            <div class="col-12">
                                <div class="alert alert-danger text-center" role="alert">
                                    <?= $_SESSION['errmsg']; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4 user_status"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="card-datatable">
                    <table class="datatables table" id="datableDemartmentList">
                        <thead>
                            <tr>
                                <th>Sr No</th>                                
                                <th>Actions</th>
                                <th>Department Name</th>
                                <th>Registered Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Paper Size Add/Edit Offcanwas -->
        <!-- Offcanvas to Add Category -->
        <div class="EditPage" style="display:none;">
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser"
                 aria-labelledby="offcanvasAddUserLabel">
                <div class="offcanvas-header border-bottom">
                    <h6 id="offcanvasAddUserLabel" class="offcanvas-title">Add Department</h6>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close" onclick="goBackToListing();"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0" style="min-height: -webkit-fill-available;">
                    <form class="add-new-user pt-0" id="addNewDepartmentForm" action="<?php echo base_url(); ?>/<?= $roleName ?>/addDepartment" method="post">
                    <input class="txt_csrfname" type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                    <input type="hidden" id="Id" value="0" name="user_id"/>
                    <div class="form-floating form-floating-outline mb-4">
                        <input
                          type="text"
                          class="form-control required"
                          id="departmentName"
                          placeholder="Department Name"
                          data-bind="name"
                          name="departmentName"
                          aria-label="John Doe" />
                        <label for="departmentName">Department Name</label>
                      </div>
                      <div class="form-floating form-floating-outline mb-4">
                        <select id="departmentStatus" name="departmentStatus" class="form-select required" placeholder="Select Status">
                         <option value="">Select Status</option>
                          <option value="1">Active</option>
                          <option value="0">In-Active</option>
                        </select>
                        <label for="departmentStatus">Select Status</label>
                      </div>
                      <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" onclick="validate_form('addNewDepartmentForm');">Submit</button>
                      <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas" onclick="goBackToListing();">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
    <!-- Offcanvas to Manage discount -->
    <!-- END Offcanvas to Manage discount -->
    <style>
        #offcanvasEditStylistRec {
            width: 60%;
        }

        .offcanvas {
            overflow-y: auto;
        }

    </style>
    <!-- Footer -->
    <!-- Offcanvas to Add Category -->

    <!-- Offcanvas to Add Category -->
    <!-- / Footer -->
    <div class="content-backdrop fade"></div>
</div>

<script>
    function editRecord(id) {

        $(".err_warning").each(function (index) {
            $(this).remove();
        });
        $(".errborder").each(function (index) {
            $(this).removeClass("errborder");
        });


        // Perform the AJAX request to fetch department details
        $.ajax({
            async: false,
            dataType: 'json',
            url: baseUrl + roleName + "/getDepartmentDetails",  // Adjust URL as needed
            method: "POST",
            data: {
                id: id,
                [acftkname]: acftknhs // Pass CSRF tokens
            },
            success: function (response) {
                // Update CSRF tokens
                acftkname = response.acftkn.acftkname;
                acftknhs = response.acftkn.acftknhs;
                $(".txt_csrfname").val(acftknhs);

                // Handle successful response
                if (response.status === 1) {
                    // Populate form fields with the data
                    $("#Id").val(response.data.department_id);
                    $("#departmentName").val(response.data.department_name);
                    $("#departmentStatus").val(response.data.status);

                    $(".EditPage").css('display', 'block');

                    // Open off-canvas after data is populated
                    var myOffcanvas = document.getElementById('offcanvasAddUser');
                        var bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas);
                        bsOffcanvas.show(); // Assuming 'offcanvasAddUser' is the ID of your off-canvas container
                } else {
                    console.error("Invalid data: " + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error); // Debugging AJAX errors
            }
        });
    }
</script>