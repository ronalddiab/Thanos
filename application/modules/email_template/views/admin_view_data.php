<article class="card">
    <div class="article-header">
        <?php echo lang('view-data') ?>
    </div>
    <div class="card-wrap">
        <?php echo form_open_multipart('', array('id' => 'saveform', 'name' => 'saveform')); ?>
        <?php //echo anchor(site_url() . get_current_section($this) . '/roles', lang('view-all-role'), 'title="' . lang('view-all-role') . '"class=add-link tooltips"' . '" style="text-align:right;width:100%;"'); ?>      
        <ul class="form-outer-block">
          
            <li>
                <label class="main-label"><?php echo lang('template-name'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <input type="text" name="template_name" id="template_name" disabled="disabled" class="input-control" value="<?php echo isset($data[0]['c']['template_name']) && !empty($data[0]['c']['template_name']) ? htmlspecialchars_decode($data[0]['c']['template_name']) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('template_name'); ?></label>
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('template-subject'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <input type="text" name="template_subject" id="template_subject" disabled="disabled" class="input-control" value="<?php echo isset($data[0]['c']['template_subject']) && !empty($data[0]['c']['template_subject']) ? htmlspecialchars_decode($data[0]['c']['template_subject']) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('template_subject'); ?></label>
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('template-body'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <textarea name="template_body" disabled="disabled" id="template_body" class="input-control textarea-control"><?php echo isset($data[0]['c']['template_body']) && !empty($data[0]['c']['template_body']) ? htmlspecialchars_decode($data[0]['c']['template_body']) : ''; ?></textarea>
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
                            <?php echo form_dropdown('status', $statuslist, $data[0]['c']['status'], 'data-type="custom-dropdown" id="status"', $disable); ?>
                            <label class="input-label validation_error"><?php echo form_error('status'); ?></label>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="form-btn-outer">
            <button type="button" class="btn btn-secondary reset-btn btn-submit" title="<?php echo lang('btn-back'); ?>" onclick="location.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'email_template' ?>'"><?php echo lang('btn-back'); ?></button>
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
