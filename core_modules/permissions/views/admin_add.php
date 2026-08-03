<article class="card">
    <div class="article-header">
        <?php echo lang('add-edit-permission') ?>
    </div>
    <div class="card-wrap">
        <?php echo form_open_multipart(site_url() . BASE_ADMIN_URL_CUSTOM . 'permissions/save', array('id' => 'saveform', 'name' => 'saveform', 'class' => 'site-info-form')); ?>
        <ul class="form-outer-block">
            <li>
                <label class="main-label"><?php echo lang('permission-label'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-3">
                        <input type="text" name="permission_label" id="permission_label" class="input-control" value="<?php echo isset($permission_label) && !empty($permission_label) ? htmlspecialchars_decode($permission_label) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('permission_label'); ?></label>
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('permission-title'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-3">
                        <input type="text" name="permission_title" id="permission_title" class="input-control" value="<?php echo isset($permission_title) && !empty($permission_title) ? htmlspecialchars_decode($permission_title) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('permission_title'); ?></label>
                    </div>
                </div>
            </li>

            <li>
                <label class="main-label"><?php echo lang('parent'); ?></label>
                <div class="row">
                    <div class="form-col-3">
                        <div class="form-dropdown">
                            <?php echo form_dropdown('parent_id', $parent_list, ((isset($parent_id)) ? $parent_id : ''), 'data-type="custom-dropdown"'); ?>
                            <label class="input-label validation_error"><?php echo form_error('parent_id'); ?></label>
                        </div>
                    </div>
                </div>
            </li>

            <li>
                <?php
                $statuslist = array('1' => 'Active', '0' => 'Inactive');?>
                <label class="main-label"><?php echo lang('status'); ?></label>
                <div class="row">
                    <div class="form-col-3">
                        <div class="form-dropdown">
                            <?php echo form_dropdown('status', $statuslist, $status, 'data-type="custom-dropdown"'); ?>
                            <label class="input-label validation_error"><?php echo form_error('status'); ?></label>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="form-btn-outer">
            <button type="submit" class="btn btn-secondary btn-submit" id="mysubmit" name="mysubmit" title="<?php echo lang('btn-save') ?>"><?php echo lang('btn-save'); ?></button>
            <button type="button" class="btn btn-secondary reset-btn btn-submit" title="<?php echo lang('btn-cancel'); ?>" onclick="location.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'permissions' ?>'"><?php echo lang('btn-cancel'); ?></button>
        </div>
        <?php
        echo form_hidden('id', (isset($id)) ? $id : '0' );
        echo form_close();
        ?>
    </div>
</article>

<script type="text/javascript">
    $(document).ready(function() {
        $("input:text:visible:first").focus().val($('input:text:visible:first').val());
        $("#saveform").validate({
            rules: {
                permission_label: {
                    required: true
                },
                role_description: {
                    required: true
                },
                permission_title: {
                    required: true
                }
            }
        });
    });
</script>