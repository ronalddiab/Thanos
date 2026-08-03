<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$full_report_exists = 0;
$executive_summary_exists = 0;
?>

<article class="card">
    <div class="article-header"><?php echo isset($audit['id']) && $audit['id']>0?lang('edit_form_fields'):lang('add_form_fields'); ?></div>
    <div class="card-wrap">
        <?php echo form_open_multipart('', array('id' => 'saveform', 'name' => 'saveform')); ?>
            <ul class="form-outer-block">
                <li>
                    <label for="audit_on" class="main-label"><?php echo lang('audit_on'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-6">
                            <?php                            
                            $audit_on_data = array(
                                'name' => 'audit_on',
                                'id' => 'audit_on',                                                                                                
                                'class' => 'input-control st datetimepicker',
                                'value' => set_value('audit_on', ((isset($audit['audit_on'])) ? date('d-M-Y',strtotime($audit['audit_on'])) : ''))
                            );
                            echo form_input($audit_on_data);
                            ?>
                            <?php if (form_error('audit_on')) { ?><label class="input-label validation_error"><?php echo form_error('audit_on'); ?></label><?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <label for="full_report_title" class="main-label"><?php echo lang('full_report_title'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-6">
                            <?php                            
                            $full_report_title_data = array(
                                'name' => 'full_report_title',
                                'id' => 'full_report_title',
                                'class' => 'input-control',
                                'value' => set_value('full_report_title', (isset($audit['full_report_title']) ? $audit['full_report_title'] : '')),
                                'maxlength' => '100'
                            );
                            echo form_input($full_report_title_data);
                            ?>
                            <?php if (form_error('full_report_title')) { ?><label class="input-label validation_error"><?php echo form_error('full_report_title'); ?></label><?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <label class="main-label"><?php echo lang('full_report'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-6">                            
                            <div class="custom-file-upload">
                                <input type="file" id="full_report" name="full_report" multiple value="<?php echo $full_report; ?>" />
                            </div>
                            <?php if (isset($audit['full_report']) && $audit['full_report'] != '') {                                
                                if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/audit/" . $audit['full_report'])) {
                                        $full_report_exists = 1;
                                    ?>
                                    <br/>
                                    <a href="<?php echo site_url() . "assets/uploads/audit/" . $audit['full_report']; ?>">Download <?php echo lang('full_report'); ?></a>
                                <?php }
                            } ?>
                            <?php if (form_error('full_report')) { ?><label class="input-label validation_error"><?php echo form_error('full_report'); ?></label><?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <label for="executive_summary_title" class="main-label"><?php echo lang('executive_summary_title'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-6">
                            <?php                            
                            $executive_summary_title_data = array(
                                'name' => 'executive_summary_title',
                                'id' => 'executive_summary_title',                                                                                                
                                'class' => 'input-control',
                                'value' => set_value('executive_summary_title', (isset($audit['executive_summary_title']) ? $audit['executive_summary_title'] : '')),
                                'maxlength' => '100'
                            );
                            echo form_input($executive_summary_title_data);
                            ?>
                            <?php if (form_error('executive_summary_title')) { ?><label class="input-label validation_error"><?php echo form_error('executive_summary_title'); ?></label><?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <label class="main-label"><?php echo lang('executive_summary'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-6">                            
                            <div class="custom-file-upload">
                                <input type="file" id="executive_summary" name="executive_summary" multiple value="<?php echo $executive_summary; ?>" />
                            </div>
                            <?php if (isset($audit['executive_summary']) && $audit['executive_summary'] != '') {                                
                                if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/audit/" . $audit['executive_summary'])) {
                                        $executive_summary_exists = 1;
                                    ?>
                                    <br/>
                                    <a href="<?php echo site_url() . "assets/uploads/audit/" . $audit['executive_summary']; ?>">Download <?php echo lang('executive_summary'); ?></a>
                                <?php }
                            } ?>
                            <?php if (form_error('executive_summary')) { ?><label class="input-label validation_error"><?php echo form_error('executive_summary'); ?></label><?php } ?>
                    </div>
                </li>
            </ul>
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <div class="form-btn-outer">
                <button type="submit" name="energyauditsubmit" id="energyauditsubmit" value="<?php echo lang('save'); ?>" class="btn btn-secondary btn-submit">Submit</button>
                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'audit'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('cancel'); ?></a>
            </div>
        <?php
        echo form_hidden('id', (isset($audit_id)) ? $audit_id : '0' );
        echo form_close();
        ?>
    </div>
</article>

<script type="text/javascript">
    $(document).ready(function() {
        $("#saveform").validate({
            rules: {
                audit_on: {
                    required: true
                },
<?php if(!$full_report_exists) { ?>
                full_report: {
                    required: true
                },
<?php } ?>
                full_report_title: {
                    required: true
                },
<?php if(!$executive_summary_exists) { ?>
                executive_summary: {
                    required: true
                },
<?php } ?>
                executive_summary_title: {
                    required: true
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