<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>

<article class="card">
    <div class="article-header"> <?php echo lang('add-comment'); ?> </div>
    <div class="card-wrap">
        <form id="saveform" class="site-info-form" method="post">
            <ul class="form-outer-block">
                <li>
                    <label for="inputComment" class="main-label"><?php echo lang('comment'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-12">
                            <textarea name="comments" id="inputComment" class="input-control textarea-control"><?php echo $data['comments']; ?></textarea>
                            <?php if (form_error('comments')) { ?><span class="validation_error"><?php echo form_error('comments'); ?></span> <?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <label class="main-label"><?php echo lang('project'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-dropdown">
                                <?php echo form_dropdown('project_id',$projects,$project_id,'data-type="custom-dropdown"'); ?>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <label class="main-label"><?php echo lang('hotel'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-dropdown">
                                <?php echo form_dropdown('hotel_id',$hotels,$hotel_id,'data-type="custom-dropdown"'); ?>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <label class="main-label"><?php echo lang('site'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-dropdown">
                                <?php //echo form_dropdown('site_id',$sites,$site_id,'data-type="custom-dropdown"'); ?>
                                <select name="site_id" data-type="custom-dropdown">
                                    <?php
                                    foreach ($sites as $site) {
                                        ?>
                                        <option <?php echo ($site['s']['id'] == $site_id)?'selected="selected"':'';?> value="<?php echo $site['s']['id']; ?>"><?php echo $site['s']['site_location_name']; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <label class="main-label"><?php echo lang('status'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-dropdown">
                                <?php 
                                    $options = array('1'=>'Active','0'=>'Inactive');
                                    echo form_dropdown('status',$options,$status,'data-type="custom-dropdown"'); 
                                ?>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <div class="form-btn-outer">
                <button type="submit" name="submit" value="1" class="btn btn-secondary btn-submit"><?php echo lang('btn-save'); ?></button>
                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/comments'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('btn-cancel'); ?></a>
            </div>
        </form>
    </div>
</article>

<script type="text/javascript">
    $(document).ready(function() {
        $("#saveform").validate({
            rules: {
                comments: {
                    required: true
                }
            }
        });
    });
</script>