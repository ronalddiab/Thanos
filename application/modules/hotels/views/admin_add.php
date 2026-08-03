<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$is_hotel_logo_exists = 0;
?>

<article class="card">
    <div class="article-header"><?php echo ($data['id']>0)? lang('edit-hotel'): lang('add-hotel'); ?></div>
    <div class="card-wrap">
        <?php echo form_open_multipart(BASE_ADMIN_URL_CUSTOM . 'hotels/save', array('id' => 'saveform', 'name' => 'saveform')); ?>
            <ul class="form-outer-block">
                <li>
                    <label for="inputName" class="main-label"><?php echo lang('hotel-name'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-12">
                            <?php 
                            $hotel_name_data = array(
                                'name' => 'hotel_name',
                                'id' => 'hotel_name',
                                'value' => set_value('hotel_name', ((isset($hotel_name)) ? htmlspecialchars_decode($hotel_name) : '')),
                                'maxlength' => 50,
                                'class' => 'input-control validate[required,maxSize[50]]'
                            );
                            ?>
                            <?php echo form_input($hotel_name_data); ?>
                            <?php if (form_error('hotel_name')) { ?><label class="input-label validation_error"><?php echo form_error('hotel_name'); ?></label> <?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <label for="inputDesc" class="main-label"><?php echo lang('hotel-logo'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-10">
                            <input type="file" id="file" name="hotel_logo" />
                            <?php if (form_error('hotel_logo')) { ?>
                                <label for="file" generated="true" class="error"><?php echo form_error('hotel_logo'); ?></label>
                            <?php } ?>
                        </div>
                        <?php if (isset($hotel_logo) && $hotel_logo != '') { ?>
                            <?php if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/" . $hotel_logo)) {
                                $is_hotel_logo_exists = 1;
                                ?>
                                <div class="form-col-2">
                                    <img src='<?php echo site_url() . "assets/uploads/" . $hotel_logo; ?> '>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </li>
                <li>
                    <label for="inputDesc" class="main-label"><?php echo lang('hotel-address'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-12">
                            <?php
                            $hotel_address_data = array(
                                'name' => 'hotel_address',
                                'id' => 'hotel_address',
                                'value' => set_value('hotel_address', ((isset($hotel_address)) ? htmlspecialchars_decode($hotel_address) : '')),
                                'rows' => '3',
                                'cols' => '10',
                                'class' => 'input-control textarea-control validate[required]'
                            );
                            ?>
                            <?php echo form_textarea($hotel_address_data); ?>
                            <?php if (form_error('hotel_address')) { ?><label class="input-label validation_error"><?php echo form_error('hotel_address'); ?></label> <?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <?php
                    $hotel_phone_data = array(
                        'name' => 'hotel_phone',
                        'id' => 'hotel_phone',
                        'value' => set_value('hotel_phone', ((isset($hotel_phone)) ? htmlspecialchars_decode($hotel_phone) : '')),
                        'maxlength' => '15',
                        'class' => 'input-control validate[required]'
                    );
                    ?>
                    <label for="hotel_phone" class="main-label"><?php echo lang('hotel-phone'); ?> <span class="asterisk">*</span></label>
                    <?php echo form_input($hotel_phone_data); ?>
                    <?php if (form_error('hotel_phone')) { ?><label class="input-label validation_error"><?php echo form_error('hotel_phone'); ?></label> <?php } ?>
                </li>
                <?php /* ?>
                <li>
                    <?php $statuslist = array('1' => 'Active'); ?>
                    <label class="main-label"><?php echo lang('status'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-dropdown">
                                <?php echo form_dropdown('status', $statuslist, $status, 'data-type="custom-dropdown"'); ?>
                            </div>
                        </div>
                    </div>
                </li>
                <?php */ ?>
            </ul>
            <input type="hidden" name="status" value="1" />
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <div class="form-btn-outer">
            <button type="submit" name="mysubmit" value="<?php echo lang('btn-save'); ?>" class="btn btn-secondary btn-submit"><?php echo lang('btn-save'); ?></button>
            <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'hotels'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('btn-cancel'); ?></a>
            </div>
        </form>
    </div>
</article>

<script type="text/javascript">
    $(document).ready(function() {
        $("#saveform").validate({
            rules: {
                hotel_name: {
                    required: true,
                    maxlength: 50
                },
                hotel_address: {
                    required: true
                },
                hotel_phone: {
                    required: true,
                    maxlength:15,
                    digits:true
                },
                <?php if($is_hotel_logo_exists == 0){ ?>
                    hotel_logo: {
                        required: true
                    },    
                <?php } ?>
            }
        });
    });
</script>
