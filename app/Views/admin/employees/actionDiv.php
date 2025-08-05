<div class="d-flex" role="group" aria-label="Basic example">
    <button class="btn btn-icon btn-sm me-2" title="Remove Permanently"
        onclick="updateStatus('<?= base64_encode($sizeData['employee_id']) ?>', 2, '<?= base64_encode('employee') ?>', '<?= base64_encode(2) ?>')">
        <i class="fa fa-trash text-danger" aria-hidden="true"></i>
    </button>

    <!-- Button for opening offcanvas for employee Details (via editRecord) -->
<button class="btn btn-icon btn-sm" title="employee Details" 
        onclick="editRecord('<?php echo base64_encode($sizeData['employee_id']); ?>')">
    <i class="fa fa-pencil-alt text-info" aria-hidden="true"></i>
</button>


</div>
