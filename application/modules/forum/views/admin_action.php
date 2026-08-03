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
    <div class="article-header"> <?php echo ($action=='add')?lang('add-forum'):lang('edit-forum'); ?></div>
    <div class="card-wrap">
        <?php 
        $attributes = array('name' => 'add_forum_form', 'id' => 'saveform','class'=>'site-info-form');
        echo form_open('', $attributes);
        ?>
            <ul id="add_forum_form" name="add_forum_form" class="form-outer-block">

                <?php
                $title_data = array(
                    'name' => 'forum_title',
                    'id' => 'forum_title',
                    'value' => set_value('forum_title', ((isset($forum_name)) ? $forum_name : '')),
                    'class' => "input-control validate[required]",
                );
                ?>

                <li>
                    <label class="main-label" for="Forum title"><?php echo lang('forum_title'); ?><span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-12">
                            <?php echo form_input($title_data); ?><label class="input-label validation_error"><?php echo form_error('forum title'); ?></label>
                        </div>
                    </div>
                </li>
                <?php
                $slug_url_data = array(
                    'name' => 'slug_url',
                    'id' => 'slug_url',
                    'value' => set_value('slug_url', ((isset($slug_url)) ? $slug_url : '')),
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
                            'class' => 'input-control'
                        );
                        echo form_textarea($description_data);
                        echo display_ckeditor($ckeditor);
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
                        echo form_dropdown('forum_category', $statuslist_cat, $forum_category, 'data-type="custom-dropdown"');
                        ?>
                        <!--<?php
                        $options = array(
                            'name' => 'parent_id',
                            'id' => 'parent_id',
                            'value' => (isset($category['parent_id'])) ? $category['parent_id'] : '',
                            'language_id' => 1,
                            'module_id' => '4',
                            'class' => 'input-control '
                        );
                        widget('category_dropdown', $options);
                        ?>-->
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
                            echo form_dropdown('is_draft', $draftlist, $is_draft, 'data-type="custom-dropdown"');
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
                        echo form_dropdown('is_sticky', $stickylist, $is_sticky, 'data-type="custom-dropdown"');
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
                                $statuslist = array('1' => 'Active','2' => 'Archive');
                                echo form_dropdown('status', $statuslist, $status, 'data-type="custom-dropdown"');
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
                <button type="submit" id="mysubmit" name="mysubmit" value="<?php echo lang('btn-save'); ?>" class="btn btn-secondary btn-submit"><?php echo lang('btn-save'); ?></button>
                <?php
                if ($action == 'edit') {
                    ?>
                    <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'forum/forum_listing/'.$forum_category."/".$language_code; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('btn-cancel'); ?></a>
                    <?php
                }else{
                    ?>
                    <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'forum'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('btn-cancel'); ?></a>
                    <?php
                }
                ?>
            </div>
            
        <?php echo form_close(); ?>
    </div>
</article>

<script type="text/javascript">

    $(document).ready(function() {

<?php
if (!empty($industry)) {
    ?>load_sector(<?php echo $industry; ?>, <?php echo $sector; ?>);
<?php }
?>

                        /*jQuery("#add_forum_form").validationEngine({
                            validationEventTrigger: "submit"
                        });*/
                    });

                    $(document).ready(function() {
                        $("#saveform").validate({
                            rules: {
                                forum_title: {
                                    required: true
                                }
                            }
                        });
                        
                        $(".tab-headings li a").click(function()
                        {
                            var thisId = $(this).attr("rel");
                            $(".tab-headings li").removeClass("selected");
                            $(this).parent('li').addClass("selected");
                            $(".profile-content").hide();
                            $(".add-comment-box").hide();
                            var lang_code = thisId.replace("#content_", "");
                            load_form(lang_code);
                        });

                        load_form = function(lang_code) {
                            $.ajax({
                                type: 'POST',
                                url: '<?php echo base_url() . $this->_data["section_name"]; ?>/forum/action/<?php echo $action; ?>/0/' + lang_code + '/<?php if (isset($id)) {
    echo $id;
} else {
    echo "0";
} ?>',
                                data: {<?php echo $csrf_token; ?>: '<?php echo $csrf_hash; ?>'},
                                success: function(msg) {
                                    $("#ajax_table").html(msg);
                                }
                            });
                        }

                    });
                    function attach_error_event() {
                        $('div.formError').bind('click', function() {
                            $(this).fadeOut(1000, removeError);
                        });
                    }
                    function removeError()
                    {
                        jQuery(this).remove();
                    }
                    $(document).ready(function() {
                        $('#slug_url').slugify('#forum_title');
                    });

                    function load_sector(id, secid) {
                        $.ajax({
                            type: 'POST',
                            url: '<?php echo base_url(); ?>admin/users/get_related_sector',
                            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', industry_id: id, secid: secid},
                            success: function(data) {
                                //$("#related_sector").html(data);
                                $("#sector_id").html(data);
                            }
                        });
                    }

</script>