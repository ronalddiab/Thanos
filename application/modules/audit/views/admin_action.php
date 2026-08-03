<?php echo add_css('validationEngine.jquery'); ?>
<?php echo add_js(array('jqvalidation/languages/jquery.validationEngine-en', 'jqvalidation/jquery.validationEngine')); ?>
<?php echo add_js('jquery.slugify'); ?>
<div class="main-content">
    <div class="grid-data info-content">
        <div class="profile-content-box" id="audit_form">
            <!-- Form will come here -->
            <?php echo $content; ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        load_form = function(lang_code) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . $this->_data["section_name"]; ?>/audit/ajax_action/<?php echo $action; ?>/<?php echo $id; ?>',
                data: {<?php echo $csrf_token; ?>: '<?php echo $csrf_hash; ?>'},
                error: function() {
                    alert("Server problem. Please try again.");
                    return false;
                },
                complete: function() {
                },
                success: function(msg) {
                    $("#audit_form").html(msg);
                }
            });
        }
    });
</script>