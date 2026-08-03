<?php echo add_css('validationEngine.jquery'); ?>
<?php echo add_js(array('jqvalidation/languages/jquery.validationEngine-en', 'jqvalidation/jquery.validationEngine')); ?>
<?php echo add_js('jquery.slugify'); ?>
<div class="main-content">
    <div class="grid-data info-content">
        <div class="profile-content-box" id="category_form">
            <!-- Form will come here -->
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
            url: '<?php echo base_url().$this->_data["section_name"]; ?>/categories/ajax_action/<?php echo $action; ?>/'+lang_code+'/<?php echo $id; ?>',
            data: {<?php echo $csrf_token; ?>: '<?php echo $csrf_hash; ?>'},
            error: function(){
		        alert("Server problem. Please try again.");
       			return false;
	        },
       		complete: function(){
       		},
            success: function(msg) {
                $("#category_form").html(msg);
            }
        });
        }

    });

</script><!--Accordion Jquery -->