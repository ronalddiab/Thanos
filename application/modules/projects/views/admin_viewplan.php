<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>

<article class="card">
    <div class="article-header"><?php echo lang('view-project'); ?></div>
    <div class="card-wrap">
        <form class="site-info-form" method="post">
            <ul class="form-outer-block">
                <li>
                    <label for="inputName" class="main-label"><?php echo lang('name'); ?></label>
                    <div class="row">
                        <div class="form-col-12">
                            <input type="text" name="project_name" class="input-control" id="inputName" disabled='disabled' placeholder="Name" value="<?php echo $data['project_name']; ?>">
                            <?php if (form_error('project_name')) { ?><span class="validation_error"><?php echo form_error('project_name'); ?></span> <?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <label for="inputDesc" class="main-label"><?php echo lang('description'); ?></label>
                    <div class="row">
                        <div class="form-col-12">
                            <textarea name="project_description" id="inputDesc" disabled='disabled' class="input-control textarea-control"><?php echo isset($data['project_description']) && !empty($data['project_description']) ? strip_tags(htmlspecialchars_decode($data['project_description'])) : ''; ?></textarea><?php if (form_error('project_description')) { ?><span class="validation_error"><?php echo form_error('project_description'); ?></span> <?php } ?>
                        </div>
                    </div>
                </li>
                <?php /* ?>
                  <li>
                  <label class="main-label"><?php echo 'Color'; ?></label>
                  <div class="row">
                  <div class="form-col-2"><input type="text" class="input-control" disabled='disabled' <?php echo (!empty($data['todo_color']))?'style="background-color:'.$data['todo_color'].'"':''; ?>></div>
                  </div>
                  </li>
                  <li>
                  <label class="main-label"><?php echo 'Image'; ?></label>
                  <div class="row">
                  <div class="form-col-12">
                  <?php if(!empty($data['todo_image']) && file_exists(BASE_PATH_CUSTOM."/assets/uploads/".$data['todo_image'])){ ?>
                  <img width="150" src="<?php echo site_url().'assets/uploads/'.$data['todo_image']; ?>">
                  <?php } ?>
                  </div>
                  </div>
                  </li>
                  <?php */ ?>
                <li>
                    <label class="main-label"><?php echo lang('project-category'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-dropdown">
                                <select id="project_category_id" disabled='disabled' name="project_category_id" data-type="custom-dropdown">
                                    <?php
                                    foreach ($categories as $category) {
                                        ?>
                                        <option <?php echo ($category['pc']['id'] == $project_category_id) ? 'selected="selected"' : ''; ?> value="<?php echo $category['pc']['id']; ?>"><?php echo $category['pc']['name']; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <label class="main-label"><?php echo lang('todo'); ?></label> 

                    <?php
                    if (!empty($todos)) {
                        foreach ($todos as $key => $todo) {
                            ?>
                            <div class="row add-row">
                                <div class="form-col-5 form-col-add">
                                    <input name="todo[todo_key][]" type="text" disabled="disabled" class="input-control" value="<?php echo $todo['todo_name']; ?>">
                                    <label class="input-label">Name</label>
                                    <input name="todo[todo_id][]" value="<?php echo $todo['id']; ?>" type="hidden" />
                                </div>                                
                                <div class="form-col-10 form-col-add">                                    
                                    <textarea name="todo[todo_value][]" id="inputDesc" disabled='disabled' class="input-control textarea-control"><?php echo $todo['todo_value']; ?></textarea>
                                    <label class="input-label">Description</label>
                                    <?php if (form_error('todo[todo_value][]')) { ?><label class="input-label validation_error"><?php echo form_error('todo_value'); ?></label> <?php } ?>
                                </div> 
                                <div class="form-col-10 form-col-add">
                                    <br/>
                                    <?php if (!empty($todo['todo_color'])) { ?>
                                        <input type="text" style="background-color:<?php echo $todo['todo_color']; ?>" disabled='disabled' class="input-control color-picker form-col-2"  />
                                    <?php } ?>
                                </div>
                                <div class="form-col-10 form-col-add">
                                    <label class="input-label">Color</label>
                                </div>
                                <div class="form-col-10 form-col-add">
                                    <br/>
                                    <?php if (!empty($todo['todo_image']) && file_exists(BASE_PATH_CUSTOM . "/assets/uploads/" . $todo['todo_image'])) { ?>
                                        <img width="150" src="<?php echo site_url() . 'assets/uploads/' . $todo['todo_image']; ?>">
                                    <?php } ?>
                                    <br/>
                                    <label class="input-label">Image</label>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </li>
            </ul>
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <div class="form-btn-outer">
                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/index'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('btn-back'); ?></a>
            </div>
        </form>
    </div>
</article>
<script type="text/javascript">
    $(document).ready(function() {
        var select = new Dropkick("#status");
        select.disable();

        var select = new Dropkick("#project_category_id");
        select.disable();
        var select = new Dropkick("#hotel_id");
        select.disable();
        var select = new Dropkick("#site_id");
        select.disable();
    });
</script>