<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>

<article class="card">
    <div class="article-header"><?php echo lang('view-hotel'); ?></div>
    <div class="card-wrap">
        <ul class="form-outer-block">
            <li>
                <label for="inputName" class="main-label"><?php echo lang('hotel-name'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <?php 
                        $hotel_name_data = array(
                            'name' => 'hotel_name',
                            'id' => 'hotel_name',
                            'value' => set_value('hotel_name', ((isset($hotel_name)) ? htmlspecialchars_decode($hotel_name) : '')),
                            'maxlength' => 50,
                            'disabled' => 'disabled',
                            'class' => 'input-control validate[required,maxSize[50]]'
                        );
                        ?>
                        <?php echo form_input($hotel_name_data); ?>
                        <?php if (form_error('hotel_name')) { ?><span class="validation_error"><?php echo form_error('hotel_name'); ?></span> <?php } ?>
                    </div>
                </div>
            </li>
            <li>
                <label for="inputDesc" class="main-label"><?php echo lang('hotel-address'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <?php
                        $hotel_address_data = array(
                            'name' => 'hotel_address',
                            'id' => 'hotel_address',
                            'value' => set_value('hotel_address', ((isset($hotel_address)) ? htmlspecialchars_decode($hotel_address) : '')),
                            'rows' => '3',
                            'cols' => '10',
                            'disabled' => 'disabled',
                            'class' => 'input-control textarea-control validate[required]'
                        );
                        ?>
                        <?php echo form_textarea($hotel_address_data); ?>
                        <?php if (form_error('hotel_address')) { ?><span class="validation_error"><?php echo form_error('hotel_address'); ?></span> <?php } ?>
                    </div>
                </div>
            </li>
            <li>
                <?php
                $hotel_phone_data = array(
                    'name' => 'hotel_phone',
                    'id' => 'hotel_phone',
                    'value' => set_value('hotel_phone', ((isset($hotel_phone)) ? htmlspecialchars_decode($hotel_phone) : '')),
                    'maxlength' => '150',
                    'disabled' => 'disabled',
                    'class' => 'input-control validate[required]'
                );
                ?>
                <label for="hotel_phone" class="main-label"><?php echo lang('hotel-phone'); ?></label>
                <?php echo form_input($hotel_phone_data); ?>
                <?php if (form_error('hotel_address')) { ?><span class="validation_error"><?php echo form_error('hotel_phone'); ?></span> <?php } ?>
            </li>
        </ul>
        <div class="form-btn-outer">
            <button onclick="location.href='<?php echo site_url().BASE_ADMIN_URL_CUSTOM . 'hotels'; ?>'"  class="btn btn-secondary reset-btn btn-submit" type="button"><?php echo lang('btn-back'); ?></button>
        </div>
    </div>
</article>
<script type="text/javascript">
    $(document).ready(function(){
        var select = new Dropkick("#status");
        select.disable();
    });
</script>