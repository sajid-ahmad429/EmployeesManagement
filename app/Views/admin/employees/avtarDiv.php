<div class="d-flex justify-content-start align-items-center user-name">
    <div class="avatar-wrapper">
        <div class="avatar avatar-lg me-3">
            <?php
                $stateNum = rand(1, 6);
                $states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
                $state = $states[$stateNum];
                $tes = [$sizeData["employee_name"]];
                $name = $tes;
                $initials = $name;
                $initials = array_shift($initials);
                $initials = strtoupper($initials);
                $initials = substr($initials, 0, 1);
                $output = '<span class="avatar-initial rounded-circle bg-label-' . $state . '">' . $initials . '</span>';
                echo $output;
            ?>
        </div>
    </div>
    <div class="d-flex flex-column">
        <a onclick="editRecord('<?php echo base64_encode($sizeData['employee_id']); ?>')" class="text-body text-truncate" style="cursor: pointer;">
            <span class="fw-semibold"><?= $sizeData['employee_name'] ?></span>
        </a>
        <small class="text-muted"><?= $sizeData['email'] ?></small>
    </div>

</div>