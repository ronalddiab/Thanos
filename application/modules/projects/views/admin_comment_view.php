<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>

<article class="card">
    <div class="article-header"> <?php echo lang('view-comment'); ?> </div>
    <div class="card-wrap">
        <ul class="form-outer-block">
                <li>
                    <label for="inputComment" class="main-label"><?php echo lang('comment'); ?></label>
                    <div class="row">
                        <div class="form-col-12">
                            <textarea name="comments" disabled="disabled" id="inputComment" class="input-control textarea-control"><?php echo $data['comments']; ?></textarea>
                        </div>
                    </div>
                </li>
                <li>
                    <label class="main-label"><?php echo lang('project'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-dropdown">
                                <?php echo form_dropdown('project_id',$projects,$project_id,'data-type="custom-dropdown" id="project_id"'); ?>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <label class="main-label"><?php echo lang('hotel'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-dropdown">
                                <?php echo form_dropdown('hotel_id',$hotels,$hotel_id,'data-type="custom-dropdown" id="hotel_id"'); ?>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <label class="main-label"><?php echo lang('site'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-dropdown">
                                <?php //echo form_dropdown('site_id',$sites,$site_id,'data-type="custom-dropdown" id="site_id"'); ?>
                                <select id="site_id" name="site_id" data-type="custom-dropdown">
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
                                    echo form_dropdown('status',$options,$status,'data-type="custom-dropdown" id="status"'); 
                                ?>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <div class="form-btn-outer">
                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/comments'; ?>" class="btn btn-secondary reset-btn btn-submit">Cancel</a>
            </div>
    </div>
</article>

<script type="text/javascript">
    $(document).ready(function(e) {
        var select = new Dropkick("#status");
        select.disable();
        var select = new Dropkick("#project_id");
        select.disable();
        var select = new Dropkick("#hotel_id");
        select.disable();
        var select = new Dropkick("#site_id");
        select.disable();
    });
</script>