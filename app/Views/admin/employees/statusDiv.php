<?php
if ($sizeData['status'] == 1) {
    ?>
    <button class="badge bg-label-success btn btn-sm" onclick="updateStatus('<?php echo base64_encode($sizeData['employee_id']); ?>', 0, '<?php echo base64_encode('employee'); ?>')">
        Active</button> 
    <?php
} else if ($sizeData['status'] == 0) {
    ?>
    <button class="badge bg-label-secondary  btn btn-sm" onclick="updateStatus('<?php echo base64_encode($sizeData['employee_id']); ?>', 1, '<?php echo base64_encode('employee'); ?>')">
        Inactive</button> 
    <?php
}
?>