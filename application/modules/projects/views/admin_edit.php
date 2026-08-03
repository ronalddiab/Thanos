<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>
<?php echo add_css(array('spectrum')); ?>
<?php echo add_js(array('spectrum')); ?>
<article class="card">
    <div class="article-header"><?php echo ($data['id']>0)? lang('edit-project'): lang('add-project'); ?></div>
    <div class="card-wrap">
        <form id="saveform" class="site-info-form" method="post" enctype="multipart/form-data">
            <ul class="form-outer-block">
                <li>
                    <label for="inputName" class="main-label"><?php echo lang('name'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-12">
                            <input type="text" name="project_name" class="input-control" id="inputName" maxlength="25" placeholder="Name" value="<?php echo $data['project_name']; ?>">
                            <?php if (form_error('project_name')) { ?><label class="input-label validation_error"><?php echo form_error('project_name'); ?></label> <?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <?php
                    $ckeditor = array(
                            //ID of the textarea that will be replaced
                            'id' => 'project_description',
                            'path' => 'assets/ckeditor',
                            //Optionnal values
                            'config' => array(
                            'toolbar' => "Small", //Using the Full toolbar
                        ),
                    );
                    ?>
                    <label for="inputDesc" class="main-label"><?php echo lang('description'); ?></label>
                    <div class="row">
                        <div class="form-col-12">
                            <textarea name="project_description" id="inputDesc" class="input-control"><?php echo isset($data['project_description']) && !empty($data['project_description']) ? htmlspecialchars_decode($data['project_description']) : ''; ?></textarea>
                            <?php echo display_ckeditor($ckeditor); ?> 
                            <?php if (form_error('project_description')) { ?><span class="validation_error"><?php echo form_error('project_description'); ?></span> <?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <label class="main-label"><?php echo lang('project-category'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-dropdown">
                                <select name="project_category_id" data-type="custom-dropdown">
                                    <?php
                                    foreach ($categories as $category) {
                                        ?>
                                        <option <?php echo ($category['pc']['id'] == $project_category_id)?'selected="selected"':'';?> value="<?php echo $category['pc']['id']; ?>"><?php echo $category['pc']['name']; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </li>
                <?php if($role_id == 3) { ?>
                <li>
                    <label class="main-label"><?php echo lang('site'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-dropdown">
                                <select name="site_id" data-type="custom-dropdown">
                                    <?php 
                                    if($role_id == 1) {
                                        ?>
                                        <option <?php echo ($site['s']['id'] == $site_id)?'selected="selected"':'';?> value="0"><?php echo lang('select-site'); ?></option>
                                        <?php
                                    }
                                    ?>
                                    <?php foreach ($sites as $site) { ?>
                                    <option <?php echo ($site['s']['id'] == $site_id)?'selected="selected"':'';?> value="<?php echo $site['s']['id']; ?>"><?php echo $site['s']['site_location_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </li>
                <?php } ?>
                <li>
                    <label class="main-label"><?php echo lang('todo'); ?></label> 
                    <?php 
                    if(!empty($todos)) {                        
                        foreach ($todos as $key=>$todo) {
                            ?>
                            <div class="row add-row">
                                <div class="form-col-10 form-col-add">
                                    <input name="todo[todo_key][]" type="text" class="input-control" value="<?php echo $todo['todo_name']; ?>">
                                    <label class="input-label">Name</label>
                                    <input name="todo[todo_id][]" value="<?php echo $todo['id']; ?>" type="hidden" />
                                </div>                                
                                <?php if($key == 0) { ?>                                    
                                    <div class="form-col-1">
                                        <button class="btn-control addition" data-row="<br/><div class='row add-row'><div class='form-col-10'><input name='todo[todo_key][]' type='text' class='input-control'>
                                                <label class='input-label'>Name</label>
                                                <input name='todo[todo_id][]' value='0' type='hidden' /></div>
                                                <div class='form-col-1'><button class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button></div>
                                                <div class='form-col-10'><textarea name='todo[todo_value][]' class='input-control textarea-control' style='margin-top: 0px;'></textarea><label class='input-label'>Description</label></div>
                                                <div class='form-col-10 form-col-add custom-color-dropdown form-dropdown colorDropdown'><br/><label class='input-label'>Color</label>
                                                <select name='todo[todo_color][]' id='todo_color' data-type='custom-dropdown'>
                                                <?php foreach ($this->_ci->config->config['action_to_do_colors'] as $fkey => $fval) { ?>
                                                    <option value='<?php echo $fkey; ?>'><?php echo $fval; ?></option>
                                                <?php } ?>
                                                </select>
                                                </div><div class='form-col-10 form-col-add'><br/><input type='file' name='todo[todo_image][]' class='input-control' /><label class='input-label'>Image</label></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>
                                    </div>
                                <?php } else { ?>
                                <div class="form-col-1">
                                    <button class="btn-control substract"><img alt="Minus" src="images/minus-icon.png"></button>
                                </div> <?php } ?>
                                <div class="form-col-10 form-col-add">
                                    <!--<input name="todo[todo_value][]" type="text" class="input-control" value="<?php //echo $todo['todo_value']; ?>"> -->
                                            <textarea name="todo[todo_value][]" id="inputDesc" class="input-control textarea-control"><?php echo $todo['todo_value']; ?></textarea>
                                            <label class="input-label">Description</label>
                                            <?php if (form_error('todo[todo_value][]')) { ?><label class="input-label validation_error"><?php echo form_error('todo_value'); ?></label> <?php } ?>
                
                                </div> 
                                <div class="form-col-10 form-col-add custom-color-dropdown form-dropdown colorDropdown" id="toDocolorDropdown">
                                    <br/>
                                    <select name="todo[todo_color][]" data-type="custom-dropdown" id="todo_color">
                                        <option value="0"><?php echo 'Select Color'; ?></option>
                                        <?php foreach ($this->_ci->config->config['action_to_do_colors'] as $fkey => $fval) { ?>
                                            <option <?php echo ($fkey == $todo['todo_color'])?'selected="selected"':'';?> value="<?php echo $fkey; ?>"><?php echo $fval; ?></option>
                                        <?php } ?>                                    
                                    </select>
                                    <br/>
                                    <!--<input type="text" name="todo[todo_color][]" value="<?php //echo (!empty($todo['todo_color']))?$todo['todo_color']:'#397A3E'; ?>" class="input-control color-picker"  /> -->
                                    <label class="input-label">Color</label>
                                </div>
                                <div class="form-col-10 form-col-add">
                                    <br/>
                                    <input type="file" name="todo[todo_image][]" class="input-control" />
                                    <?php if(!empty($todo['todo_image']) && file_exists(BASE_PATH_CUSTOM."/assets/uploads/".$todo['todo_image'])){ ?>
                                    <img width="150" src="<?php echo site_url().'assets/uploads/'.$todo['todo_image']; ?>">
                                    <?php } ?>
                                    <br/>
                                    <label class="input-label">Image</label>
                                </div>                               
                            </div>                            
                            <?php
                        }
                    }else{
                        ?>
                        <div class="row add-row">
                            <div class="form-col-10 form-col-add">
                                <input name="todo[todo_key][]" type="text" class="input-control">
                                <label class="input-label">Name</label>
                                <input name="todo[todo_id][]" value="0" type="hidden" />                                
                            </div>
                            <div class="form-col-1">
                                <button class="btn-control addition" data-row="<div class='row add-row'><div class='form-col-10'><input name='todo[todo_key][]' type='text' class='input-control'>
                                        <label class='input-label'>Name</label>
                                        <input name='todo[todo_id][]' value='0' type='hidden' /></div>
                                        <div class='form-col-1'><button class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button></div>
                                        <div class='form-col-10'><textarea name='todo[todo_value][]' class='input-control textarea-control' style='margin-top: 0px;'></textarea><label class='input-label'>Description</label></div>
                                        <div class='form-col-10 form-col-add form-dropdown colorDropdown'><br/><label class='input-label'>Color</label>
                                        <select name='todo[todo_color][]' data-type='custom-dropdown' id='todo_color'>
                                        <?php foreach ($this->_ci->config->config['action_to_do_colors'] as $fkey => $fval) { ?>
                                            <option value='<?php echo $fkey; ?>'><?php echo $fval; ?></option>
                                        <?php } ?>
                                        </select></div><div class='form-col-10 form-col-add'><br/><input type='file' name='todo[todo_image][]' class='input-control' /><label class='input-label'>Image</label></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>
                            </div>
                            <div class="form-col-10 form-col-add">
                                <textarea name="todo[todo_value][]" class="input-control textarea-control" style="margin-top: 0px;"></textarea>
                                <label class="input-label">Description</label>
                            </div>
                            <div class="form-col-10 form-col-add form-dropdown colorDropdown">
                                <br/>
                                    <!--<input type="text" name="todo[todo_color][]" value='#397A3E' class="input-control color-picker"  /> -->
                                    <label class="input-label">Color</label>
                                    <select name="todo[todo_color][]" data-type="custom-dropdown" id="todo_color">                                        
                                        <?php foreach ($this->_ci->config->config['action_to_do_colors'] as $fkey => $fval) { ?>
                                            <option value="<?php echo $fkey; ?>"><?php echo $fval; ?></option>
                                        <?php } ?>
                                    </select>
                            </div>
                            <div class="form-col-10 form-col-add">
                                <br/>
                                <input type="file" name="todo[todo_image][]" class="input-control" />
                                <label class="input-label">Image</label>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </li>
            </ul>
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <div class="form-btn-outer">
                <button type="submit" name="submit" value="1" class="btn btn-secondary btn-submit"><?php echo lang('btn-save'); ?></button>
                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/listing'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('btn-cancel'); ?></a>
            </div>
        </form>
    </div>
</article>

<script type="text/javascript">
    $(document).ready(function() {
        $(".color-picker").spectrum({
            preferredFormat: "hex",
            showInput: true,
            showPalette: true
        });

        $(".addition").click(function() {
            $(".color-picker-more").spectrum({
                preferredFormat: "hex",
                showInput: true,
                showPalette: true
            });
            $(".color-picker-more").removeClass('color-picker-more');
            $('.form-col-add > input[type=file]').customFile();
            $("select[data-type='custom-dropdown']").dropkick({
                mobile: true
            });
        });

        $(".btn-control.substract").click(function(e){
            e.preventDefault();
            var $this = $(this);
            $this.closest(".row").remove();
        });

        $("#saveform").validate({
            rules: {
                project_name: {
                    required: true,
                    maxlength: 25
                }
            }
        });
		
		$('.colorDropdown ul .dk-option').each(function () {
			var color = $(this).data('value');
			if(color != 0)
			{
					$(this).css('background',color);
					$(this).text('');
			}
		
		});
		
		$(".btn-control.addition").click(function(e){
			e.preventDefault();
			$('.colorDropdown ul .dk-option').each(function () {
				var color = $(this).data('value'); 
				if(color != 0)
				{
						$(this).css('background',color);
						$(this).text('');
				}
			});
		});
    });
</script>