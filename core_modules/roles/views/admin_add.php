<article class="card">
    <div class="article-header">
        <?php echo ($action=='add')?lang('add-role'):lang('edit-role'); ?>
    </div>
    <div class="card-wrap">
        <?php echo form_open_multipart(site_url() . BASE_ADMIN_URL_CUSTOM . 'roles/save', array('id' => 'saveform', 'name' => 'saveform', 'class' => 'site-info-form')); ?>
        <ul class="form-outer-block">
            <li>
                <label class="main-label"><?php echo lang('role-name'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-12">
                        <input type="text" name="role_name" id="role_name" class="input-control" value="<?php echo isset($role_name) && !empty($role_name)?htmlspecialchars_decode($role_name):''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('role_name'); ?></label>
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('role-description'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-12">
                        <textarea name="role_description" id="role_description" class="input-control textarea-control"><?php echo isset($role_description) && !empty($role_description)?htmlspecialchars_decode($role_description):''; ?></textarea>
                        <label class="input-label validation_error"><?php echo form_error('role_description'); ?></label>
                    </div>
                </div>
            </li>
            <li>
                 <?php
                $statuslist = array('1' => 'Active', '0' => 'Inactive');
                if ($user_id == 1) {
                    $disable = "disabled='disabled'";
                } else {
                    $disable = "";
                }
                ?> 
                <label class="main-label"><?php echo lang('status'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <div class="form-dropdown">
                            <?php echo form_dropdown('status', $statuslist, $status, 'data-type="custom-dropdown"', $disable); ?>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="form-btn-outer">
            <button type="submit" class="btn btn-secondary btn-submit" id="mysubmit" name="mysubmit" title="<?php echo lang('btn-save') ?>"><?php echo lang('btn-save'); ?></button>
            <button type="button" class="btn btn-secondary reset-btn btn-submit" title="<?php echo lang('btn-cancel'); ?>" onclick="location.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'roles' ?>'"><?php echo lang('btn-cancel'); ?></button>
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
                role_name: {
                    required: true
                },
                role_description: {
                    required: true
                },
                status: {
                    required: true
                }
            }
        });
    });
</script>