<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>

<article class="card">
    <div class="article-header"> <?php echo lang('category-view'); ?> </div>
    <div class="card-wrap">
        
            <ul class="form-outer-block">
                <li>
                    <label for="inputName" class="main-label"><?php echo lang('name'); ?></label>
                    <div class="row">
                        <div class="form-col-12">
                            <input type="text" name="name" class="input-control" disabled="disabled" id="inputName" placeholder="Name" value="<?php echo $data['name']; ?>">
                            <?php if (form_error('name')) { ?><span class="validation_error"><?php echo form_error('name'); ?></span> <?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <label for="inputDesc" class="main-label"><?php echo lang('description'); ?></label>
                    <div class="row">
                        <div class="form-col-12">
                            <textarea name="description" id="inputDesc" disabled="disabled" class="input-control textarea-control"><?php echo $data['description']; ?></textarea>
                            <?php if (form_error('description')) { ?><span class="validation_error"><?php echo form_error('description'); ?></span> <?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <label class="main-label"><?php echo lang('status'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-dropdown">
                                <select id="status" name="status" data-type="custom-dropdown">
                                    <option <?php echo ($status == 1)?'selected="selected"':'';?> value="1">Active</option>
                                    <option <?php echo ($status == 0)?'selected="selected"':'';?> value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <div class="form-btn-outer">
                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/categories'; ?>" class="btn btn-secondary reset-btn btn-submit">Cancel</a>
            </div>
        
    </div>
</article>
<script type="text/javascript">
    $(document).ready(function(e) {
        var select = new Dropkick("#status");
        select.disable();
    });
</script>