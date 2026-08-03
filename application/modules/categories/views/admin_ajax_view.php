<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>

<article class="card">
    <div class="article-header"><?php echo lang('add_form_fields'); ?></div>
    <div class="card-wrap">
        <form class="site-info-form" method="post">
            <ul class="form-outer-block">
                <li>
                    <label for="title" class="main-label"><?php echo lang('title'); ?></label>
                    <div class="row">
                        <div class="form-col-12">
                            <?php
                            $title_data = array(
                                'name' => 'title',
                                'id' => 'title',
                                'value' => '',
                                'size' => '50',
                                'maxlength' => '255',
                                'disabled' => 'disabled',
                                'class' => 'input-control validate[required]',
                                'value' => set_value('title', ((isset($category['title'])) ? $category['title'] : ''))
                            );
                            echo form_input($title_data);
                            ?>
                            <?php if (form_error('title')) { ?><span class="validation_error"><?php echo form_error('title'); ?></span> <?php } ?>
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
                                'disabled' => 'disabled',
                                'class' => 'input-control textarea-control',
                                'value' => set_value('description', ((isset($category['description'])) ? $category['description'] : ''))
                            );
                            echo form_textarea($description_data);
                            ?>
                            <?php if (form_error('description')) { ?><span class="validation_error"><?php echo form_error('description'); ?></span> <?php } ?>
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
                                echo form_dropdown('status', $options, (isset($category['status'])) ? $category['status'] : '','id="status" data-type="custom-dropdown"');
                                echo form_hidden('module_id', "4");
                                ?>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <div class="form-btn-outer">
                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'categories'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('back'); ?></a>
            </div>
        </form>
    </div>
</article>
<script type="text/javascript">
    $(document).ready(function(e) {
        var select = new Dropkick("#status");
        select.disable();
    });
</script>