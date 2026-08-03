<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>

<article class="card">
    <div class="article-header"> <?php echo ($data['id']>0)? lang('edit-category'): lang('add-category'); ?> </div>
    <div class="card-wrap">
        <form id="saveform" class="site-info-form" method="post">
            <ul class="form-outer-block">
                <li>
                    <label for="inputName" class="main-label"><?php echo lang('name'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-12">
                            <input type="text" name="name" class="input-control" id="inputName" maxlength="25" placeholder="Name" value="<?php echo $data['name']; ?>">
                            <?php if (form_error('name')) { ?>
                            <label class="input-label validation_error"><?php echo form_error('name'); ?></label>
                            <?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <label for="inputDesc" class="main-label"><?php echo lang('description'); ?></label>
                    <div class="row">
                        <div class="form-col-12">
                            <textarea name="description" id="inputDesc" class="input-control textarea-control"><?php echo $data['description']; ?></textarea>
                            <?php if (form_error('description')) { ?><label class="input-label validation_error"><?php echo form_error('description'); ?></label> <?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <label class="main-label"><?php echo lang('status'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-dropdown">
                                <select name="status" data-type="custom-dropdown">
                                    <option <?php echo (isset($status) && $status == 1)?'selected="selected"':'';?> value="1">Active</option>
                                    <option <?php echo (isset($status) && $status == 0)?'selected="selected"':'';?> value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <div class="form-btn-outer">
                <button type="submit" name="submit" value="1" class="btn btn-secondary btn-submit"><?php echo lang('btn-save'); ?></button>
                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/categories'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('btn-cancel'); ?></a>
            </div>
        </form>
    </div>
</article>

<script type="text/javascript">
    $(document).ready(function() {
        $("#saveform").validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 25
                }
            }
        });
    });
</script>