<article class="card">
    <div class="article-header">
        <?php echo lang('add-edit-template'); ?>
        <?php
        $ckeditor = array(
            //ID of the textarea that will be replaced
            'id' => 'template_body',
            'path' => 'assets/ckeditor',
            //Optionnal values
            'config' => array(
                'toolbar' => "Small", //Using the Full toolbar
            //'width' => "550px", //Setting a custom width
            //height' => '100px', //Setting a custom height
            ),
        );
        ?>
    </div>
    <div class="card-wrap">
        <?php echo form_open_multipart(site_url() . BASE_ADMIN_URL_CUSTOM . 'email_template/save', array('id' => 'saveform', 'name' => 'saveform', 'class' => 'site-info-form')); ?>
        <ul class="form-outer-block">
            <li>
                <label class="main-label"><?php echo lang('template-name'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-12">
                        <input type="text" name="template_name" id="template_name" class="input-control" value="<?php echo isset($template_name) && !empty($template_name) ? htmlspecialchars_decode($template_name) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('template_name'); ?></label>
                    </div>
                </div>
            </li>

            <li>
                <label class="main-label"><?php echo lang('template-subject'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-12">
                        <input type="text" name="template_subject" id="template_subject" class="input-control" value="<?php echo isset($template_subject) && !empty($template_subject) ? htmlspecialchars_decode($template_subject) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('template_subject'); ?></label>
                    </div>
                </div>
            </li>

            <li>
                <label class="main-label"><?php echo lang('template-body'); ?> </label>
                <div class="row">
                    <div class="form-col-12">
                        <textarea name="template_body" id="template_body" class="input-control textarea-control"><?php echo isset($template_body) && !empty($template_body) ? htmlspecialchars_decode($template_body) : ''; ?></textarea>
                        <?php echo display_ckeditor($ckeditor); ?> 
                        <label class="input-label validation_error"><?php echo form_error('template_body'); ?></label>
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
                            <?php echo form_dropdown('status', $statuslist, $status, 'data-type="custom-dropdown"', $disable); ?>
                            <label class="input-label validation_error"><?php echo form_error('status'); ?></label>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="form-btn-outer">
            <button type="submit" class="btn btn-secondary btn-submit" id="mysubmit" name="mysubmit" title="<?php echo lang('btn-save') ?>"><?php echo lang('btn-save'); ?></button>
            <button type="button" class="btn btn-secondary reset-btn btn-submit" title="<?php echo lang('btn-cancel'); ?>" onclick="location.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'email_template' ?>'"><?php echo lang('btn-cancel'); ?></button>
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
                template_name: {
                    required: true
                },
                template_subject: {
                    required: true
                }
            }
        });
    });
</script>