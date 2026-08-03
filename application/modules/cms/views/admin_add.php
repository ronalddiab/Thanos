<article class="card">
    <div class="article-header">
        <?php echo ($action == 'add')?lang('add-cms'):lang('edit-cms'); ?>
        <?php
        $ckeditor = array(
            //ID of the textarea that will be replaced
            'id' => 'description',
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
        <?php echo form_open_multipart(site_url() . BASE_ADMIN_URL_CUSTOM . 'cms/save', array('id' => 'saveform', 'name' => 'saveform', 'class' => 'site-info-form'));                    
            if ($action == "edit" && isset($old_slug_url) && trim($old_slug_url) != '')
                echo form_hidden('old_slug_url', trim($old_slug_url));
        ?>
        <ul class="form-outer-block">
            <li>
                <label class="main-label"><?php echo lang('title'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-12">
                        <input type="text" name="title" id="title" class="input-control" maxlength="25" value="<?php echo isset($title) && !empty($title) ? htmlspecialchars_decode($title) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('title'); ?></label>
                    </div>
                </div>
            </li>

            <li>
                <label class="main-label"><?php echo lang('slug_url'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-12">
                        <input type="text" name="slug_url" id="slug_url" readonly="readonly" class="input-control" value="<?php echo isset($slug_url) && !empty($slug_url) ? htmlspecialchars_decode($slug_url) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('slug_url'); ?></label>
                    </div>
                </div>
            </li>

            <li>
                <label class="main-label"><?php echo lang('description'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <textarea name="description" id="description" class="input-control textarea-control"><?php echo isset($description) && !empty($description) ? htmlspecialchars_decode($description) : ''; ?></textarea>
                        <?php echo display_ckeditor($ckeditor); ?> 
                        <label class="input-label validation_error"><?php echo form_error('description'); ?></label>
                    </div>
                </div>
            </li>
            
            <li>
                <label class="main-label"><?php echo lang('meta-title'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <input type="text" name="meta_title" id="meta_title" class="input-control" value="<?php echo isset($meta_title) && !empty($meta_title) ? htmlspecialchars_decode($meta_title) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('meta_title'); ?></label>
                    </div>
                </div>
            </li>
            
            
            <li>
                <label class="main-label"><?php echo lang('keywords'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <input type="text" name="meta_keywords" id="meta_keywords" class="input-control" value="<?php echo isset($meta_keywords) && !empty($meta_keywords) ? htmlspecialchars_decode($meta_keywords) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('meta_keywords'); ?></label>
                    </div>
                </div>
            </li>
            
            <li>
                <label class="main-label"><?php echo lang('description'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <textarea name="meta_description" id="meta_description" class="input-control textarea-control"><?php echo isset($meta_description) && !empty($meta_description) ? htmlspecialchars_decode($meta_description) : ''; ?></textarea>
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
                    <div class="form-col-12">
                        <div class="form-dropdown">
                            <?php echo form_dropdown('status', $statuslist, $status, 'data-type="custom-dropdown"', $disable); ?>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="form-btn-outer">
            <button type="submit" class="btn btn-secondary btn-submit" id="mysubmit" name="mysubmit"><?php echo lang('btn-save'); ?></button>
            <button type="button" class="btn btn-secondary reset-btn btn-submit" onclick="location.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'cms' ?>'"><?php echo lang('btn-cancel'); ?></button>
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
                title: {
                    required: true
                },
                slug_url: {
                    required: true
                }
            }
        });
    });
</script>