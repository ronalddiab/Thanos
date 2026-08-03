<article class="card">
    <div class="article-header">
        <?php echo lang('add-edit-settings') ?>
    </div>
    <div class="card-wrap">
        <?php echo form_open_multipart(site_url() . BASE_ADMIN_URL_CUSTOM . 'settings/save', array('id' => 'saveform', 'name' => 'saveform', 'class' => 'site-info-form')); ?>
        <ul class="form-outer-block">
            <li>
                <label class="main-label"><?php echo lang('setting-title'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-3">
                        <input type="text" name="setting_title" id="setting_title" class="input-control" value="<?php echo isset($setting_title) && !empty($setting_title) ? htmlspecialchars_decode($setting_title) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('setting_title'); ?></label>
                    </div>
                </div>
            </li>


            <li>
                <label class="main-label"><?php echo lang('setting-label'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-3">
                        <input type="text" name="setting_label" id="setting_label" class="input-control" value="<?php echo isset($setting_label) && !empty($setting_label) ? htmlspecialchars_decode($setting_label) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('setting_label'); ?></label>
                    </div>
                </div>
            </li>


            <li>
                <label class="main-label"><?php echo lang('setting-value'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-3">
                        <input type="text" name="setting_value" id="setting_value" class="input-control" value="<?php echo isset($setting_value) && !empty($setting_value) ? htmlspecialchars_decode($setting_value) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('setting_value'); ?></label>
                    </div>
                </div>
            </li>



            <li>
                <label class="main-label"><?php echo lang('setting-comment'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-12">
                        <textarea name="comment" id="comment" class="input-control textarea-control"><?php echo isset($comment) && !empty($comment) ? htmlspecialchars_decode($comment) : ''; ?></textarea>
                        <label class="input-label validation_error"><?php echo form_error('comment'); ?></label>
                    </div>
                </div>
            </li>
        </ul>
        <div class="form-btn-outer">
            <button type="submit" class="btn btn-secondary btn-submit" id="mysubmit" name="mysubmit" title="<?php echo lang('btn-save') ?>"><?php echo lang('btn-save'); ?></button>
            <button type="button" class="btn btn-secondary reset-btn btn-submit" title="<?php echo lang('btn-cancel'); ?>" onclick="location.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'settings' ?>'"><?php echo lang('btn-cancel'); ?></button>
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
                setting_title: {
                    required: true
                },
                setting_label: {
                    required: true
                },
                setting_value: {
                    required: true
                }
            }
        });
    });
</script>