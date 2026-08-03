<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>

<article class="card">
    <div class="article-header"><?php echo ($id>0)? lang('edit-country'): lang('add-country'); ?></div>
    <div class="card-wrap">
        <form id="saveform" name="saveform" method="post">
        <ul class="form-outer-block">
            <li>
                <label for="inputName" class="main-label"><?php echo lang('country'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-12">
                        <?php
                        $country_input = array(
                            'name' => 'country',
                            'id' => 'country',
                            'value' => set_value('country', ((isset($country)) ? htmlspecialchars_decode($country) : '')),
                            'class' => 'input-control',
                            'maxlength'=>25
                        );
                        ?>
                        <?php echo form_input($country_input); ?>
                        <?php if (form_error('country')) { ?><label class="input-label validation_error"><?php echo form_error('country'); ?></label> <?php } ?>
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
            <button type="submit" name="mysubmit" value="1" class="btn btn-secondary btn-submit"><?php echo lang('btn-save'); ?></button>
            <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'countries'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('btn-cancel'); ?></a>
        </div>
        </form>
    </div>
</article>


<script type="text/javascript">
    $(document).ready(function() {
        $("#saveform").validate({
            rules: {
                country: {
                    required: true,
                    maxlength: 25
                }
            }
        });
    });
</script>