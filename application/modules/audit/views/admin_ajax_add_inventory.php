<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$full_report_exists = 0;
?>

<article class="card">
    <div class="article-header"><?php echo isset($inventory['id']) && $inventory['id']>0?lang('edit_form_fields'):lang('add_form_fields'); ?></div>
    <div class="card-wrap">
        <?php echo form_open_multipart('', array('id' => 'saveform', 'name' => 'saveform')); ?>
            <ul class="form-outer-block">
                <li>
                    <label for="inventory_on" class="main-label"><?php echo lang('inventory_on'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-6">
                            <?php                            
                            $inventory_on_data = array(
                                'name' => 'inventory_on',
                                'id' => 'inventory_on',
                                'class' => 'input-control st datetimepicker',
                                'value' => set_value('inventory_on', ((isset($inventory['inventory_on'])) ? date('d-M-Y',strtotime($inventory['inventory_on'])) : ''))
                            );
                            echo form_input($inventory_on_data);
                            ?>
                            <?php if (form_error('inventory_on')) { ?><label class="input-label validation_error"><?php echo form_error('inventory_on'); ?></label><?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <label for="full_report_title" class="main-label">Category <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-6">
                            <div class="">
                                <div class="form-dropdown">
                                    <?php echo form_dropdown('inventoty_title', $inventory_titles, 0, 'id="inventoty_title" data-type="custom-dropdown"'); ?>
                                </div>
                            </div>
                            <?php if (form_error('inventoty_title')) { ?><label class="input-label validation_error"><?php echo form_error('inventoty_title'); ?></label><?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <label class="main-label">File <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-6">                            
                            <div class="custom-file-upload">
                                <input type="file" id="full_report" name="full_report" multiple value="<?php echo $full_report; ?>" />
                            </div>
                            <?php if (isset($inventory['full_report']) && $inventory['full_report'] != '') {                                
                                if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/inventory/" . $inventory['full_report'])) {
                                        $full_report_exists = 1;
                                    ?>
                                    <br/>
                                    <a href="<?php echo site_url() . "assets/uploads/inventory/" . $inventory['full_report']; ?>">Download <?php echo lang('full_report'); ?></a>
                                <?php }
                            } ?>
                            <?php if (form_error('full_report')) { ?><label class="input-label validation_error"><?php echo form_error('full_report'); ?></label><?php } ?>
                        </div>
                    </div>
                </li>
            </ul>
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <div class="form-btn-outer">
                <button type="submit" name="inventorysubmit" id="inventorysubmit" value="<?php echo lang('save'); ?>" class="btn btn-secondary btn-submit">Submit</button>
                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'audit'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('cancel'); ?></a>
            </div>
        <?php
        echo form_hidden('id', (isset($inventory_id)) ? $inventory_id : '0' );
        echo form_close();
        ?>
    </div>
</article>

<script type="text/javascript">
    $(document).ready(function() {
        $("#saveform").validate({
            rules: {
                inventory_on: {
                    required: true
                },
<?php if(!$full_report_exists) { ?>
                full_report: {
                    required: true
                },
<?php } ?>
                inventoty_title: {
                    required: true,
                    validators: {
                        notEmpty: {
                            message: 'The inventory title is required'
                        },
                    }
                }
            }
        });
    });

    $(".st.datetimepicker").datepicker({
        dateFormat: 'dd-M-yy',
        onSelect: function(selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate());
            $(".ed.datetimepicker").datepicker("option", "minDate", dt);
        }
    });
</script>