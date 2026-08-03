<article class="card">
    <div class="article-header">
        <?php echo lang('view-data') ?>
    </div>
    <div class="card-wrap">
        <?php echo form_open_multipart('', array('id' => 'saveform', 'name' => 'saveform')); ?>
        <?php echo $this->theme->message(); ?>
        <?php //echo anchor(site_url() . get_current_section($this) . '/roles', lang('view-all-role'), 'title="' . lang('view-all-role') . '"class=add-link tooltips"' . '" style="text-align:right;width:100%;"'); ?>      
        <ul class="form-outer-block">
            <li>
                <label class="main-label"><?php echo lang('role-name'); ?><span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-3">
                        <input type="text" name="role_name" id="role_name" disabled="disabled" class="input-control" value="<?php echo isset($data['role_name']) && !empty($data['role_name']) ? htmlspecialchars_decode($data['role_name']) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('role_name'); ?></label>
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('role-description'); ?><span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-3">
                        <textarea name="role_description" disabled="disabled" id="role_description" class="input-control"><?php echo isset($data['role_description']) && !empty($data['role_description']) ? htmlspecialchars_decode($data['role_description']) : ''; ?></textarea>
                        <label class="input-label validation_error"><?php echo form_error('role_description'); ?></label>
                    </div>
                </div>
            </li>
            <li>
                <?php
                $statuslist = array('1' => 'Active', '0' => 'Inactive');
                $disable = "disabled='disabled'";
                ?> 
                <label class="main-label"><?php echo lang('status'); ?></label>
                <div class="row">
                    <div class="form-col-3">
                        <div class="form-dropdown">
                            <?php echo form_dropdown('status', $statuslist, $data['status'], 'data-type="custom-dropdown" id="status"', $disable); ?>
                            <label class="input-label validation_error"><?php echo form_error('status'); ?></label>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="form-btn-outer">
            <button type="button" class="btn btn-secondary reset-btn btn-submit" title="<?php echo lang('btn-cancel'); ?>" onclick="location.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'roles' ?>'"><?php echo lang('btn-cancel'); ?></button>
        </div>
        <?php
        echo form_hidden('id', (isset($id)) ? $id : '0' );
        echo form_close();
        ?>
    </div>
</article>

<script>
    $(document).ready(function(e) {
        var select = new Dropkick("#status");
        select.disable();
    });

</script>
