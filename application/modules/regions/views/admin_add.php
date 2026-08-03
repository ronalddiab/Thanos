<script type="text/javascript">
    (function($) {

        $.fn.maxlength = function() {

            $("textarea[maxlength]").keypress(function(event) {
                var key = event.which;

                //all keys including return.
                if (key >= 33 || key == 13 || key == 32) {
                    var maxLength = $(this).attr("maxlength");
                    var length = this.value.length;
                    if (length >= maxLength) {
                        event.preventDefault();
                    }
                }
            });
        }

    })(jQuery);

    $(document).ready(function($) {
        //Set maxlength of all the textarea (call plugin)
        $().maxlength();
    })


</script>
<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>

<article class="card">
    <div class="article-header"><?php echo ($action=='add')?lang('add-region'):lang('edit-region'); ?></div>
    <div class="card-wrap">
        <?php echo form_open_multipart(BASE_ADMIN_URL_CUSTOM . 'regions/save', array('id' => 'saveform', 'name' => 'saveform')); ?>
        <ul class="form-outer-block">
            <li>
                <label for="inputName" class="main-label"><?php echo lang('region-name'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-12">
                        <?php
                        $region_name_data = array(
                            'name' => 'region_name',
                            'id' => 'region_name',
                            'value' => set_value('region_name', ((isset($region_name)) ? htmlspecialchars_decode($region_name) : '')),
                            'class' => 'input-control',
                            'maxlength'=>25
                        );
                        ?>
                        <?php echo form_input($region_name_data); ?>
                        <?php if (form_error('region_name')) { ?><label class="input-label validation_error"><?php echo form_error('region_name'); ?></label> <?php } ?>
                    </div>
                </div>
            </li>
            <li>
                <?php $statuslist = array('1' => 'Active', '0' => 'Inactive'); ?>
                <label class="main-label"><?php echo lang('status'); ?></label> 
                <div class="row">
                    <div class="form-col-12">
                        <div class="form-dropdown">
                            <?php echo form_dropdown('status', $statuslist, $status, 'data-type="custom-dropdown"'); ?>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <div class="form-btn-outer">
            <button type="submit" name="mysubmit" value="<?php echo lang('btn-save'); ?>" class="btn btn-secondary btn-submit"><?php echo lang('btn-save'); ?></button>
            <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'regions'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('btn-cancel'); ?></a>
        </div>
        </form>
    </div>
</article>

<script type="text/javascript">
    $(document).ready(function() {

        $(":input").each(function(i) {
            $(this).attr('tabindex', i + 1);
        })
        $("#saveform").validate({
            rules: {
                region_name: {
                    required: true,
                    maxlength: 25
                },
            }});
        $("input:text:visible:first").focus().val($('input:text:visible:first').val());
    });

</script>



