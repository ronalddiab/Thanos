<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$ckeditor = array(
    //ID of the textarea that will be replaced
    'id' => 'description',
    'path' => 'assets/ckeditor',
    //Optionnal values
    'config' => array(
        'toolbar' => "Full", //Using the Full toolbar
        'width' => "550px", //Setting a custom width
        'height' => '100px', //Setting a custom height
    ),
);
?>

<article class="card">
    <div class="article-header"><?php echo lang('add_form_fields'); ?></div>
    <div class="card-wrap">
        <form id="saveform" class="site-info-form" method="post">
            <ul class="form-outer-block">
                <li>
                    <label for="title" class="main-label"><?php echo lang('title'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-12">
                            <?php
                            $title_data = array(
                                'name' => 'title',
                                'id' => 'title',
                                'value' => '',
                                'size' => '50',
                                'maxlength' => '100',
                                'class' => 'input-control validate[required]',
                                'value' => set_value('title', ((isset($category['title'])) ? $category['title'] : ''))
                            );
                            echo form_input($title_data);
                            ?>
                            <?php if (form_error('title')) { ?><label class="input-label validation_error"><?php echo form_error('title'); ?></label> <?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <label for="description" class="main-label"><?php echo lang('description'); ?></label>
                    <div class="row">
                        <div class="form-col-12">
                            <?php
                            $description_data = array(
                                'name' => 'description',
                                'id' => 'description',
                                'size' => '50',
                                'class' => 'input-control textarea-control',
                                'value' => set_value('description', ((isset($category['description'])) ? $category['description'] : ''))
                            );
                            echo form_textarea($description_data);
                            echo display_ckeditor($ckeditor);
                            ?>
                            <?php if (form_error('description')) { ?><label class="input-label validation_error"><?php echo form_error('description'); ?></label> <?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <label class="main-label"><?php echo lang('status'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-dropdown">
                                <?php
                                $options = array(
                                    '1' => lang('active'),
                                    '0' => lang('inactive')
                                );
                                echo form_dropdown('status', $options, (isset($category['status'])) ? $category['status'] : '','data-type="custom-dropdown"');
                                echo form_hidden('module_id', "4");
                                ?>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <?php 
            if ($action == "edit" && isset($category['slug_url']) && $category['slug_url'] != ''){
                echo form_hidden('old_slug_url', $category['slug_url']);
            }
            ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <div class="form-btn-outer">
                <button type="submit" name="categorysubmit" id="categorysubmit" value="<?php echo lang('save'); ?>" class="btn btn-secondary btn-submit">Submit</button>
                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'categories'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('cancel'); ?></a>
            </div>
        </form>
    </div>
</article>

<?php


/*$attributes = array('class' => '', 'id' => 'categoryadd', 'name' => 'categoryadd');
echo form_open(''.$this->section_name.'/categories/action/' . $action . "/" . $language_code . "/" . $id, $attributes);
if ($action == "edit" && isset($category['slug_url']) && $category['slug_url'] != '')
    echo form_hidden('old_slug_url', $category['slug_url']);
?>
<?php echo form_close();*/ ?>
<script type="text/javascript">
$(document).ready(function() {
    $('#slug_url').slugify('#title');
    /*jQuery("#categoryadd").validationEngine(
            {
                validationEventTrigger: "submit"
            }
    );*/

    
    $("#saveform").validate({
        rules: {
            title: {
                required: true
            }
        }
    });

});

</script>