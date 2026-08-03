<?php
$ckeditor = array(
    //ID of the textarea that will be replaced
    'id' => 'topic_text',
    'path' => 'assets/ckeditor',
    //Optionnal values
    'config' => array(
        'toolbar' => "Full", //Using the Full toolbar
        'width' => "100%", //Setting a custom width
        'height' => '100px', //Setting a custom height
        //'removePlugins' => 'dialogui,dialog,a11yhelp,about,bidi,blockquote,clipboard,colordialog,menu,contextmenu,dialogadvtab,div,elementspath,enterkey,entities,popup,filebrowser,find,fakeobjects,flash,floatingspace,forms,horizontalrule,htmlwriter,iframe,image,link,liststyle,magicline,maximize,newpage,pagebreak,pastefromword,pastetext,preview,print,removeformat,resize,save,menubutton,scayt,selectall,showblocks,showborders,smiley,sourcearea,specialchar,stylescombo,tab,templates,undo,wsc,table,tabletools',
        'extraAllowedContent' => 'ul(*)[*]{*};li(*)[*]{*};p(*)[*]{*};span(*)[*]{*};div(*)[*]{*};h1(*)[*]{*};h2(*)[*]{*};h3(*)[*]{*};h4(*)[*]{*};img(*)[*]{*};a(*)[*]{*};table(*)[*]{*};tr(*)[*]{*};td(*)[*]{*};'
    ),
);
?>
<article id="ajax_table" class="card">
    <div class="article-header"><?php echo lang('reply_this_post'); ?></div>
    <div class="card-wrap">

        <?php
        $attributes = array('name' => 'add_forum_post', 'id' => 'add_forum_post');
        echo form_open_multipart('', $attributes);
        ?>
        <ul class="form-outer-block">
            <li>
                <?php
                $title_data = array(
                    'name' => 'topic_title',
                    'id' => 'topic_title',
                    'value' => set_value('topic_title', ((isset($topic_title)) ? $topic_title : '')),
                    'class' => "input-control"
                );
                ?>
                <label for="inputName" class="main-label"><?php echo lang('reply_title'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-12">
                        <?php echo form_input($title_data); ?>
                        <?php if (form_error('topic_title')) { ?>
                        <label class="input-label validation_error"><?php echo form_error('topic_title'); ?></label>
                        <?php } ?>
                    </div>
                </div>
            </li>

            <li>
                <?php
                $reply_data = array(
                    'name' => 'topic_text',
                    'id' => 'topic_text',
                    'value' => set_value('topic_text', ((isset($topic_text)) ? html_entity_decode($topic_text) : '')),
                    'class' => 'input-control',
                );
                ?>
                <label for="inputDesc" class="main-label"><?php echo lang('topic_text'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-12">
                        <?php
                        echo form_textarea($reply_data);
                        echo display_ckeditor($ckeditor);
                        ?>
                        <?php if (form_error('topic_text')) { ?><label class="input-label validation_error"><?php echo form_error('topic_text'); ?></label> <?php } ?>
                    </div>
                </div>
            </li>
            <li>
                <label for="inputDesc" class="main-label"><?php echo lang('status'); ?></label>
                <div class="form-dropdown">
                    <?php $statuslist = array('2' => 'Inactive', '1' => 'Active'); ?>
                    <?php echo form_dropdown('status', $statuslist, $status,'data-type="custom-dropdown"'); ?>
                    <?php if (form_error('status')) { ?><label class="input-label validation_error"><?php echo form_error('status'); ?></label> <?php } ?>
                </div>
            </li>
        </ul>
        <div class="form-btn-outer">
            <button type="submit" name="mysubmit" value="<?php echo lang('edit'); ?>" class="btn btn-secondary btn-submit"><?php echo lang('edit'); ?></button>
            <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'forum/forum_post/'.$post_id; ?>" class="btn btn-secondary reset-btn btn-submit">Cancel</a>
        </div>
        <?php echo form_hidden('file_exist', (isset($attach)) ? $attach : '' ); ?>
        <?php echo form_close(); ?>
    </div>
</article>

<script type="text/javascript">
    $(document).ready(function() {
        $("#add_forum_post").validate({
            rules: {
                topic_title: {
                    required: true
                },
                topic_text:{
                    required: true
                }
            }
        });
    });
</script>