<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>

<article class="card">
    <div class="article-header"><?php echo lang('view-country'); ?></div>
    <div class="card-wrap">
        <ul class="form-outer-block">
            <li>
                <label for="inputName" class="main-label"><?php echo lang('country'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <?php 
                        $country_data = array(
                            'name' => 'country',
                            'id' => 'country',
                            'value' => set_value('country', ((isset($country)) ? htmlspecialchars_decode($country) : '')),
                            'maxlength' => 50,
                            'disabled' => 'disabled',
                            'class' => 'input-control validate[required,maxSize[50]]'
                        );
                        ?>
                        <?php echo form_input($country_data); ?>
                        <?php if (form_error('country')) { ?><span class="validation_error"><?php echo form_error('country'); ?></span> <?php } ?>
                    </div>
                </div>
            </li>
            <li>
                <?php $statuslist = array('1' => 'Active', '0' => 'Inactive'); ?>
                <label class="main-label"><?php echo lang('status'); ?></label> 
                <div class="row">
                    <div class="form-col-12">
                        <div class="form-dropdown">
                            <?php echo form_dropdown('status', $statuslist, $status, 'data-type="custom-dropdown" id="status"'); ?>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="form-btn-outer">
            <button onclick="location.href='<?php echo site_url().BASE_ADMIN_URL_CUSTOM . 'countries'; ?>'" class="btn btn-secondary reset-btn btn-submit" type="button"><?php echo lang('btn-back'); ?></button>
        </div>
    </div>
</article>
<script type="text/javascript">
    $(document).ready(function(){
        var select = new Dropkick("#status");
        select.disable();
    });
</script>