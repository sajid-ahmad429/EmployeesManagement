
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

<!-- Footer -->
<footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl">
        <div class="footer-container d-flex align-items-center justify-content-between py-3 flex-md-row flex-column">
            <div class="mb-2 mb-md-0">
                ©
                <script>
                    document.write(new Date().getFullYear());
                </script>
                , made with <span class="text-danger"><i class="tf-icons mdi mdi-heart"></i></span> by
                <a href="https://pixinvent.com" target="_blank" class="footer-link fw-medium">Pixinvent</a>
            </div>
            <div class="d-none d-lg-inline-block">
                <a href="https://themeforest.net/licenses/standard" class="footer-link me-4" target="_blank">License</a>
                <a href="https://1.envato.market/pixinvent_portfolio" target="_blank" class="footer-link me-4">More
                    Themes</a>

                <a href="https://demos.pixinvent.com/materialize-html-admin-template/documentation/" target="_blank"
                    class="footer-link me-4">Documentation</a>

                <a href="https://pixinvent.ticksy.com/" target="_blank"
                    class="footer-link d-none d-sm-inline-block">Support</a>
            </div>
        </div>
    </div>
</footer>
<!-- / Footer -->

<div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
</div>
<!-- / Layout page -->
</div>

<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>

<!-- Drag Target Area To SlideIn Menu On Small Screens -->
<div class="drag-target"></div>
</div>
<!-- / Layout wrapper -->

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/libs/popper/popper.js"></script>
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/js/bootstrap.js"></script>
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/libs/node-waves/node-waves.js"></script>
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/libs/hammer/hammer.js"></script>
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/libs/i18n/i18n.js"></script>
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/libs/typeahead-js/typeahead.js"></script>
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/js/menu.js"></script>

<!-- endbuild -->

<!-- Vendors JS -->
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/libs/apex-charts/apexcharts.js"></script>
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/libs/swiper/swiper.js"></script>
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/libs/moment/moment.js"></script>
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/libs/select2/select2.js"></script>
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js"></script>
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js"></script>
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js"></script>
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/libs/cleavejs/cleave.js"></script>
<script src="<?= base_url() ?>/public/dashboard/assets/vendor/libs/cleavejs/cleave-phone.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.13.4/b-2.3.6/b-colvis-2.3.6/b-html5-2.3.6/b-print-2.3.6/cr-1.6.2/fc-4.2.2/fh-3.3.2/r-2.4.1/sc-2.1.1/sp-2.1.2/sl-1.6.2/sr-1.2.2/datatables.min.js"></script>
<!-- Main JS -->
<script src="<?= base_url() ?>/public/dashboard/assets/js/main.js"></script>

<!-- Page JS -->
<script src="<?= base_url() ?>/public/dashboard/assets/js/dashboards-analytics.js"></script>
<script src="<?= base_url() ?>/public/dashboard/assets/js/pages-profile.js"></script>

<script>
    var baseUrl = "<?php echo base_url(); ?>";
    var roleName = "<?php echo $roleName; ?>";
    
</script>

<?php if (isset($assetsJs) && is_array($assetsJs)): ?>
    <?php foreach ($assetsJs as $jsFile): ?>
        <script src="<?php echo base_url('public/dashboard/assets/js/' . $jsFile. '.js'); ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<script>
    function addContent(formName) {
        $('#' + formName + ' input[type=hidden]').each(function (index) {
            if ($(this).hasClass('txt_csrfname') || $(this).hasClass('txt_acffname')) {
            }
            if ($(this).hasClass('registration_type') || $(this).hasClass('company_id')) {
            } else {
                $(this).val(0);
            }
        });

        document.getElementById(formName).reset();
        $(".txt_csrfname").val(acftknhs);
        $(".txt_acffname").val(acftknhs);
       
        // For Product Related Images
        // for users

        $("#passwordDiv").css('display', 'block').find('input').addClass('required').val('');
        $("#confirmPasswordDiv").css('display', 'block').find('input').addClass('required').val('');

        $(".EditPage").css('display', 'block');
        $("html, body").animate({scrollTop: 0}, "slow");
        $('body').addClass('no-scroll');
    }

    function goBackToListing() {
        $("#locations-type").val('').trigger('change');
        if (localStorage.getItem('useId')) {
            localStorage.removeItem('useId');
        }

        $(".err_warning").each(function (index) {
            $(this).remove();
        });
        $(".errborder").each(function (index) {
            $(this).removeClass("errborder");
        });

        $("#appendUserDiv").find("div").remove();
        $('.select2').val([]).trigger('change');

        // $(".page-content").css('display', 'none');
        $(".masterPage").css('display', 'block');
        $("html, body").animate({scrollTop: 0}, "slow");
    }

    function updateStatus(id, status, name) {
        icon = 'info';
        dangerMode = true;
        title = 'Are you sure?';
        text = "Once deleted, you will not be able to recover!";
        subtitle = 'Cancel';
        confirmButtonText = 'Yes';
        successtitle = '';
        successttext = '';
        status = parseInt(status);
        switch (status) {
            case 1:
                icon = 'success';
                dangerMode = true;
                title = 'Are you sure?';
                text = "Activate The Record!";
                confirmButtonText = 'Activate';
                successtitle = 'Activated';
                successttext = 'Record Activated Successfully....!';
                break;
            case 2:
                icon = 'error';
                dangerMode = true;
                title = 'Are you sure?';
                text = "Permenently Remove Record...!";
                confirmButtonText = 'Remove';
                successtitle = 'Deleted';
                successttext = 'Record Peremenently Removed Successfully....!';
                break;
            default:
                icon = 'warning';
                dangerMode = true;
                title = 'Are you sure?';
                text = "Deactivate The Record!";
                confirmButtonText = 'Deactivate';
                successtitle = 'Deactivated';
                successttext = 'Record Deactivated Successfully....!';
                break;
        }
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            showCancelButton: !0,
            confirmButtonText: confirmButtonText,
            cancelButtonText: "No, cancel!",
            confirmButtonClass: "btn btn-success mt-2",
            cancelButtonClass: "btn btn-danger ms-2 mt-2",
            buttonsStyling: !1}).then(function (t) {
            if (t.value) {
                $.ajax({
                    url: '<?php echo base_url(); ?>/chnage_status',
                    type: 'post',
                    datatype: 'json',
                    async: false,
                    data: {id: id, status: status, name: name, [acftkname]: acftknhs}, // get all form variables
                    success: function (result1) {
                        var getTableName = atob(name);
                        if (result1 == 1) {
                            Swal.fire({title: successtitle, text: successttext, icon: "success"})
                            setTimeout(function () {
                                location.reload();
                            }, 3000);
                        }
                    }
                });
            } else {
                t.dismiss;
                Swal.fire({title: "Cancelled", text: "Your record is safe :)", icon: "error"})
            }
        });
    }
</script>

</body>

</html>