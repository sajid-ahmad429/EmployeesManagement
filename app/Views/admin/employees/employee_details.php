<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">User Profile /</span> Profile</h4>

        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="user-profile-header-banner">
                        <img src="<?= base_url() ?>/public/dashboard/assets/img/pages/profile-banner.png" alt="Banner image" class="rounded-top" />
                    </div>
                    <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                            <img src="<?= base_url() ?>/public/dashboard/assets/img/avatars/1.png" alt="user image"
                                class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img" />
                        </div>
                        <div class="flex-grow-1 mt-3 mt-sm-5">
                            <div
                                class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                                <div class="user-profile-info">
                                    <h4> <?= isset($employees[0]->employee_name) ? $employees[0]->employee_name : 'Default Name'; ?></h4>
                                    <ul
                                        class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                                        <li class="list-inline-item">
                                            <i class="mdi mdi-invert-colors me-1 mdi-20px"></i><span
                                                class="fw-medium"><?= isset($employees[0]->designation) ? $employees[0]->designation : 'Default Name'; ?></span>
                                        </li>
                                        <li class="list-inline-item">
                                            <!-- Example icon change based on employee_type -->
                                            <?php
                                                // Check employee_type and set the icon accordingly
                                                $icon = '';
                                                $employeeType = isset($employees[0]->employee_type) ? ucwords($employees[0]->employee_type) : 'Default Type';

                                                // Set icon based on employee_type
                                                if ($employeeType == 'admin') {
                                                    $icon = 'mdi-account-settings-outline';  // Admin Icon
                                                } elseif ($employeeType == 'employee') {
                                                    $icon = 'mdi-account-tie';  // Manager Icon
                                                } elseif ($employeeType == 'User') {
                                                    $icon = 'mdi-account';  // User Icon
                                                } else {
                                                    $icon = 'mdi-account-circle';  // Default icon
                                                }
                                            ?>

                                            <i class="mdi <?= $icon ?> me-1 mdi-20px"></i>
                                            <span class="fw-medium"><?= $employeeType ?></span>
                                        </li>

                                        <li class="list-inline-item">
                                            <i class="mdi mdi-calendar-blank-outline me-1 mdi-20px"></i>
                                            <span class="fw-medium">
                                                <?php
                                                    // Check if created_at exists and format it
                                                    $created_at = isset($employees[0]->created_at) ? strtotime($employees[0]->created_at) : null;
                                                    echo $created_at ? 'Joined ' . date('F Y', $created_at) : 'Joined N/A';
                                                ?>
                                            </span>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Header -->

        <!-- Navbar pills -->
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-pills flex-column flex-sm-row mb-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="javascript:void(0);"><i
                                class="mdi mdi-account-outline me-1 mdi-20px"></i>Profile</a>
                    </li>
                </ul>
            </div>
        </div>
        <!--/ Navbar pills -->

        <!-- User Profile Content -->
        <div class="row">
            <div class="col-xl-12 col-lg-5 col-md-5">
                <!-- About User -->
                <div class="card mb-4">
                    <div class="card-body">
                        <small class="card-text text-uppercase">About</small>
                        <ul class="list-unstyled my-3 py-1">
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-account-outline mdi-24px"></i>
                                <span class="fw-medium mx-2">Full Name:</span> 
                                <span><?= isset($employees[0]->employee_name) ? $employees[0]->employee_name : 'N/A' ?></span>
                            </li>

                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-check mdi-24px"></i>
                                <span class="fw-medium mx-2">Status:</span>
                                <span><?= isset($employees[0]->status) ? ($employees[0]->status == 1 ? 'Active' : 'Inactive') : 'N/A' ?></span>
                            </li>

                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-star-outline mdi-24px"></i>
                                <span class="fw-medium mx-2">Role:</span>
                                <span><?= isset($employees[0]->designation) ? $employees[0]->designation : 'N/A' ?></span>
                            </li>

                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-briefcase-outline mdi-24px"></i>
                                <span class="fw-medium mx-2">Department:</span>
                                <span><?= isset($employees[0]->department_name) ? $employees[0]->department_name : 'N/A' ?></span>
                            </li>

                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-flag-outline mdi-24px"></i><span
                                    class="fw-medium mx-2">India:</span>
                                <span>India</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-translate mdi-24px"></i><span class="fw-medium mx-2">Languages:</span>
                                <span>English,</span>
                            </li>
                        </ul>
                        <small class="card-text text-uppercase">Contacts</small>
                        <ul class="list-unstyled my-3 py-1">
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-email-outline mdi-24px"></i><span class="fw-medium mx-2">Email:</span>
                                <span><?= isset($employees[0]->email) ? $employees[0]->email : 'N/A' ?></span>
                            </li>
                        </ul>
                        <small class="card-text text-uppercase">Department</small>
                        <ul class="list-unstyled mb-0 mt-3 pt-1">
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-github mdi-24px text-secondary me-2"></i>
                                <div class="d-flex flex-wrap">
                                    <span class="fw-medium me-2"><?= isset($employees[0]->department_name) ? $employees[0]->department_name : 'N/A' ?></span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!--/ About User -->
            </div>
        </div>
        <!--/ User Profile Content -->
    </div>
    <!-- / Content -->
    <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->