<article class="card">
    <div class="article-header">
        <?php echo lang('view-data') ?>
    </div>
    <div class="card-wrap">
        <?php echo form_open_multipart('', array('id' => 'saveform', 'name' => 'saveform')); ?>
        <?php //echo anchor(site_url() . get_current_section($this) . '/roles', lang('view-all-role'), 'title="' . lang('view-all-role') . '"class=add-link tooltips"' . '" style="text-align:right;width:100%;"'); ?>      
        <ul class="form-outer-block">

            <li>
                <label class="main-label"><?php echo lang('title'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <input type="text" name="title" id="title" disabled="disabled" class="input-control" value="<?php echo isset($data[0]['c']['title']) && !empty($data[0]['c']['title']) ? htmlspecialchars_decode($data[0]['c']['title']) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('title'); ?></label>
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('slug_url'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <input type="text" name="slug_url" id="slug_url" disabled="disabled" class="input-control" value="<?php echo isset($data[0]['c']['slug_url']) && !empty($data[0]['c']['slug_url']) ? htmlspecialchars_decode($data[0]['c']['slug_url']) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('slug_url'); ?></label>
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('description'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <textarea name="description" disabled="disabled" id="description" class="input-control textarea-control"><?php echo isset($data[0]['c']['description']) && !empty($data[0]['c']['description']) ? htmlspecialchars_decode($data[0]['c']['description']) : ''; ?></textarea>
                        <label class="input-label validation_error"><?php echo form_error('description'); ?></label>
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('meta-title'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <input type="text" name="meta_title" id="meta_title" disabled="disabled" class="input-control" value="<?php echo isset($data[0]['cm']['meta_title']) && !empty($data[0]['cm']['meta_title']) ? htmlspecialchars_decode($data[0]['cm']['meta_title']) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('meta_title'); ?></label>
                    </div>
                </div>
            </li>

            <li>
                <label class="main-label"><?php echo lang('keywords'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <input type="text" name="keywords" id="keywords" disabled="disabled" class="input-control" value="<?php echo isset($data[0]['cm']['meta_keywords']) && !empty($data[0]['cm']['meta_title']) ? htmlspecialchars_decode($data[0]['cm']['meta_title']) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('keywords'); ?></label>
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('description'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <textarea name="meta_description" disabled="disabled" id="meta_description" class="input-control textarea-control"><?php echo isset($data[0]['cm']['meta_description']) && !empty($data[0]['cm']['meta_description']) ? htmlspecialchars_decode($data[0]['cm']['meta_description']) : ''; ?></textarea>
                        <label class="input-label validation_error"><?php echo form_error('meta_description'); ?></label>
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
            <button type="button" class="btn btn-secondary reset-btn btn-submit" onclick="location.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'cms' ?>'"><?php echo lang('btn-back'); ?></button>
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
