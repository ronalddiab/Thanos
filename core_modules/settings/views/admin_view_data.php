<article class="card">
    <div class="article-header">
        <?php echo lang('view-data') ?>
    </div>
    <div class="card-wrap">
        <?php echo form_open_multipart('', array('id' => 'saveform', 'name' => 'saveform', 'class' => 'site-info-form')); ?>
        <ul class="form-outer-block">
            <li>
                <label class="main-label"><?php echo lang('setting-title'); ?></label>
                <div class="row">
                    <div class="form-col-3">
                        <input type="text" name="setting_title" id="setting_title" disabled="disabled" class="input-control" value="<?php echo isset($data[0]['s']['setting_label']) && !empty($data[0]['s']['setting_label']) ? htmlspecialchars_decode($data[0]['s']['setting_label']) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('setting_title'); ?></label>
                    </div>
                </div>
            </li>


            <li>
                <label class="main-label"><?php echo lang('setting-label'); ?></label>
                <div class="row">
                    <div class="form-col-3">
                        <input type="text" name="setting_label" disabled="disabled" id="setting_label" class="input-control" value="<?php echo isset($data[0]['s']['setting_label']) && !empty($data[0]['s']['setting_label']) ? htmlspecialchars_decode($data[0]['s']['setting_label']) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('setting_label'); ?></label>
                    </div>
                </div>
            </li>


            <li>
                <label class="main-label"><?php echo lang('setting-value'); ?></label>
                <div class="row">
                    <div class="form-col-3">
                        <input type="text" name="setting_value" disabled="disabled" id="setting_value" class="input-control" value="<?php echo isset($data[0]['s']['setting_value']) && !empty($data[0]['s']['setting_value']) ? htmlspecialchars_decode($data[0]['s']['setting_value']) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('setting_value'); ?></label>
                    </div>
                </div>
            </li>



            <li>
                <label class="main-label"><?php echo lang('setting-comment'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <textarea name="comment" disabled="disabled" id="comment" class="input-control textarea-control"><?php echo isset($data[0]['s']['comment']) && !empty($data[0]['s']['comment']) ? htmlspecialchars_decode($data[0]['s']['comment']) : ''; ?></textarea>
                        <label class="input-label validation_error"><?php echo form_error('comment'); ?></label>
                    </div>
                </div>
            </li>
        </ul>
        <div class="form-btn-outer">
            <button type="button" class="btn btn-secondary reset-btn btn-submit" title="<?php echo lang('btn-back'); ?>" onclick="location.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'settings' ?>'"><?php echo lang('btn-back'); ?></button>
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