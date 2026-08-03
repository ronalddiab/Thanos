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
                <label class="main-label"><?php echo lang('permission-label'); ?></label>
                <div class="row">
                    <div class="form-col-3">
                        <input type="text" name="permission_label" id="permission_label" disabled="disabled" class="input-control" value="<?php echo isset($data['permission_label']) && !empty($data['permission_label']) ? htmlspecialchars_decode($data['permission_label']) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('permission_label'); ?></label>
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('permission-title'); ?></label>
                <div class="row">
                    <div class="form-col-3">
                        <input type="text" name="permission_title" id="permission_title" disabled="disabled" class="input-control" value="<?php echo isset($data['permission_title']) && !empty($data['permission_title']) ? htmlspecialchars_decode($data['permission_title']) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('permission_title'); ?></label>
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('parent'); ?></label>
                <div class="row">
                    <div class="form-col-3">
                        <div class="form-dropdown">
                            <?php echo form_dropdown('parent_id', $parent_list, ((isset($parent_id)) ? $parent_id : ''), 'data-type="custom-dropdown" id="parent_permssion"'); ?>
                            <label class="input-label validation_error"><?php echo form_error('parent_id'); ?></label>
                        </div>
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
            <button type="button" class="btn btn-secondary reset-btn btn-submit" title="<?php echo lang('btn-back'); ?>" onclick="location.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'permissions' ?>'"><?php echo lang('btn-back'); ?></button>
        </div>
        <?php
        echo form_hidden('id', (isset($id)) ? $id : '0' );
        echo form_close();
        ?>
    </div>
</article>

<script>
    $(document).ready(function(e) {
        var select_permission = new Dropkick("#parent_permssion");
        var select = new Dropkick("#status");
        select_permission.disable();
        select.disable();
    });

</script>
