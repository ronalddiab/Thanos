<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>

<article class="card">
    <div class="article-header"><?php echo lang('view-certification_menu'); ?></div>
    <div class="card-wrap">
        <ul class="form-outer-block">
            <li>
                <label for="inputName" class="main-label"><?php echo lang('title'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <?php 
                        $title_data = array(
                            'name' => 'title',
                            'id' => 'title',
                            'value' => set_value('title', ((isset($title)) ? htmlspecialchars_decode($title) : '')),
                            'class' => 'input-control',
                            'maxlength'=>25,
                            'disabled' => 'disabled',
                        );
                        ?>
                        <?php echo form_input($title_data); ?>
                        <?php if (form_error('title')) { ?><span class="validation_error"><?php echo form_error('title'); ?></span> <?php } ?>
                    </div>
                </div>
            </li>
            <?php if (isset($section_logo) && $section_logo != '') { ?>
            <li>
                <label class="main-label"><?php echo lang('upload-section-logo'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <?php
                            if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/" . $section_logo)) {
                            ?>
                                <img src='<?php echo site_url() . "assets/uploads/" . $section_logo; ?> '>
                            <?php } else { ?>
                                <img class="siteImage" src='<?php echo site_url() . NOT_AVAILABLE_site_logo; ?> '>
                            <?php } ?>
                    </div>
                </div>
            </li>
           <?php } ?>
          
            <?php 
            if(isset($parent_array) && count($parent_array) > 0)
            {
                foreach($parent_array as $key=>$parent)
                {
                    ?>
                    <li>
                        <?php $section = array('1'=>$parent['title']);?>
                        <label class="main-label">Section <?=$key+1?></label> 
                        <div class="row">
                            <div class="form-col-12">
                                <div class="form-dropdown">
                                    <?php echo form_dropdown('section', $section, $section, 'data-type="custom-dropdown" id="status"'); ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php
                }
            }
            ?> 
            <?php if(!empty($section_description)){?>
            <li>
                <label class="main-label"><?php echo lang('section_description'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <p>
                            <?php echo html_entity_decode($section_description);?>
                        </p>
                    </div>
                </div>
            </li>  
            <?php } ?>    
            <li>
            <label class="main-label"><?php echo lang('upload_check'); ?></label>
            <div class="row">
                <div class="form-col-1">
                    <input type="checkbox" name="is_upload_check" class="icheck checkboxlist check_box" value="1" <?= (isset($is_upload_checked) && $is_upload_checked == 1) ? 'checked' :''?>>
                </div>
            </div>
            </li>      
            <li>
                <?php $statuslist = array('1' => 'Active', '0' => 'Inactive'); ?>
                <label class="main-label"><?php echo lang('status'); ?></label> 
                <div class="row">
                    <div class="form-col-12">
                        <div class="form-dropdown">
                            <?php echo form_dropdown('status', $statuslist, $status, 'data-type="custom-dropdown" id="status"'); ?>
                        </div>
                    </div>
                </div>
            </li>
            <?php if(count($questions)>0){?>
            <li>
                <label for="inputName" class="main-label"><?php echo lang('section_question'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <?php foreach($questions as $que){?>
                            <p><?=$que['section_question']?>
                        <?php } ?>
                    </div>
                </div>
            </li>
            <?php } ?>
        </ul>
        <div class="form-btn-outer">
            <button onclick="location.href='<?php echo site_url().BASE_ADMIN_URL_CUSTOM . 'certification_menu'; ?>'" class="btn btn-secondary reset-btn btn-submit" type="button"><?php echo lang('btn-back'); ?></button>
        </div>
    </div>
</article>
<script type="text/javascript">
    $(document).ready(function(){
        var select = new Dropkick("#status");
        select.disable();
    });
</script>