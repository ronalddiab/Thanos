<?php echo add_js(array('jquery-1.9.1.min', 'jqvalidation/languages/jquery.validationEngine-en', 'jqvalidation/jquery.validationEngine')); ?>
<div class="main-content">
    <div class="grid-data info-content">
        <div class="add-new">
            <?php //echo "<pre>"; print_r($this);
            echo anchor(get_current_section($this->_ci). '/email_template/index/'.$language_code, lang('email-template-list'), 'title="'.lang('email-template-list').'" style="text-align:center;width:100%;"'); ?>
        </div>
        <div class="tab-nav">
            <ul class="tab-headings clearfix">
                <?php
                    for ($i = 0; $i < count($languages); $i++) {
                        $selected = '';
                        if (($languages[$i]['l']['id']) == $language_id) {
                            $selected = "selected";
                        }
                        ?><li class="<?php echo $selected; ?>"><a href="javascript:;" rel="#content_<?php echo ($languages[$i]['l']['language_code']); ?>" title="<?php echo $languages[$i]['l']['language_code'];?>"><?php echo $languages[$i]['l']['language_name']; ?></a></li><?php
                    }
                ?>
            </ul>
        </div>
        <div class="profile-content-box" id="cms_form">
            <?php echo $content; ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
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
                url: '<?php echo site_base_url().$this->_data['section_name']; ?>/email_template/ajax_view/'+lang_code+'/<?php echo $id; ?>',
                data: {<?php echo $csrf_token; ?>: '<?php echo $csrf_hash; ?>'},
                success: function(msg) {
                    $("#cms_form").html(msg);
                }
            });
        }
    });
</script>