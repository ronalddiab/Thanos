<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>

<?php echo add_css('validationEngine.jquery'); ?>
<?php echo add_js(array('jqvalidation/languages/jquery.validationEngine-en', 'jqvalidation/jquery.validationEngine')); ?>
<?php echo add_js('jquery.slugify'); ?>


<?php
$ckeditor = array(
    //ID of the textarea that will be replaced
    'id' => 'forum_description',
    'path' => 'assets/ckeditor',
    //Optionnal values
    'config' => array(
        'toolbar' => "Full", //Using the Full toolbar
        'width' => "550px", //Setting a custom width
        'height' => '100px', //Setting a custom height
    ),
);
if (!isset($category_id)) {
    $category_id = "";
}
?>
<article class="card">
    <div class="article-header"> <?php echo lang('view-forum'); ?> </div>
    <div class="card-wrap">
        <?php 
        $attributes = array('name' => 'add_forum_form', 'id' => 'add_forum_form','class'=>'site-info-form');
        echo form_open('', $attributes);
        ?>
            <ul id="add_forum_form" name="add_forum_form" class="form-outer-block">

                <?php
                $title_data = array(
                    'name' => 'forum_title',
                    'id' => 'forum_title',
                    'value' => set_value('forum_title', ((isset($forum_name)) ? $forum_name : '')),
                    'disabled' => 'disabled',
                    'class' => "input-control validate[required]",
                );
                ?>

                <li>
                    <label class="main-label" for="Forum title"><?php echo lang('forum_title'); ?></label>
                    <div class="row">
                        <div class="form-col-12">
                            <?php echo form_input($title_data); ?><br/><span class="warning-msg"><?php echo form_error('forum title'); ?></span>
                        </div>
                    </div>
                </li>
                <?php
                $slug_url_data = array(
                    'name' => 'slug_url',
                    'id' => 'slug_url',
                    'value' => set_value('slug_url', ((isset($slug_url)) ? $slug_url : '')),
                    'disabled' => 'disabled',
                    'class' => 'input-control validate[required]'
                );
                ?>
                <li>
                    <label class="main-label" for="slug_url"><?php echo lang('slug_url'); ?></label>
                    <div class="row">
                        <div class="form-col-12">
                            <?php echo form_input($slug_url_data); ?><br/><span class="warning-msg"><?php echo form_error('slug_url'); ?>
                        </div>
                    </div>
                </li>

                <li>
                    <label class="main-label" for="forum_description"><?php echo lang('forum_description'); ?></label>
                    <div class="row">
                        <div class="form-col-12">
                        <?php
                        $description_data = array(
                            'name' => 'forum_description',
                            'id' => 'forum_description',
                            'value' => set_value('forum_description', ((isset($forum_description)) ? html_entity_decode($forum_description) : '')),
                            'disabled' => 'disabled',
                            'class' => 'input-control textarea-control'
                        );
                        echo form_textarea($description_data);
                        ?>
                        </div>
                    </div>
                </li>
                <?php
                $statuslist = array();
                $statuslist_value = array();
                foreach ($categories as $category) {
                    $statuslist_cat[$category["categories"]["category_id"]] = $category["categories"]["title"];
                    $statuslist_cat_value = $category["categories"]["category_id"];
                }
                if (!isset($forum_category)) {
                    $forum_category = "";
                }
                ?>
                <li>
                    <label class="main-label" for="Category"><?php echo lang('category'); ?></label>
                    <div class="row">
                        <div class="form-col-12">
                        <div class="form-dropdown">
                        <?php
                        echo form_dropdown('forum_category', $statuslist_cat, $forum_category, 'data-type="custom-dropdown" id="categorydropdown"');
                        ?>
                        <span class="warning-msg"><?php echo form_error('category'); ?></span>
                        </div>
                        </div>
                    </div>
                </li>

                <?php
                if ($action == 'edit' && $is_draft == 0) {
                    echo form_hidden('is_draft', $is_draft);
                } else {

                    $draftlist = array('0' => 'No', '1' => 'Yes');
                    ?>
                    <li>
                        <label class="main-label" for="is-draft"><?php echo lang('is-draft'); ?></label>
                        <div class="row">
                        <div class="form-col-12">
                        <div class="form-dropdown">
                            <?php
                            echo form_dropdown('is_draft', $draftlist, $is_draft, 'data-type="custom-dropdown" id="draft"');
                            ?>
                            </div>
                            </div>
                        </div>
                    </li>
                <?php } ?>

                <?php $stickylist = array('0' => 'No', '1' => 'Yes'); ?>
                <li>
                   <label class="main-label" for="is-sticky"><?php echo lang('is-sticky'); ?></label>
                    <div class="row">
                        <div class="form-col-12">
                        <div class="form-dropdown">
                        <?php
                        echo form_dropdown('is_sticky', $stickylist, $is_sticky, 'data-type="custom-dropdown" id="sticky"');
                        ?>
                        </div>
                        </div>
                    </div>
                </li>

                <li>
                    <label class="main-label"><?php echo lang('status'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-dropdown">
                                <?php 
                                $statuslist = array('1' => 'Active');
                                echo form_dropdown('status', $statuslist, $status, 'data-type="custom-dropdown" id="status"');
                                ?>
                                <span class="warning-msg"><?php echo form_error('status'); ?></span>
                                <?php
                                echo form_hidden('id', (isset($id)) ? $id : '0' );
                                ?>
                            </div>
                        </div>
                    </div>
                </li>

            </ul>

            <div class="form-btn-outer">
                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'forum/forum_listing/'.$forum_category."/".$language_code; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('back'); ?></a>
            </div>
            
        <?php echo form_close(); ?>
    </div>
</article>


<script type="text/javascript">
    $(document).ready(function(e) {
        var select = new Dropkick("#categorydropdown");
        select.disable();

        var select = new Dropkick("#status");
        select.disable();

        var select = new Dropkick("#sticky");
        select.disable();
        
    });
</script>