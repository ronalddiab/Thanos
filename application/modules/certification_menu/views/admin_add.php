<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
?>
<?php echo add_js('jquery.slugify'); ?>
<article class="card">
<div class="article-header"><?php echo ($action == 'add') ? lang('add-certification_menu') : lang('edit-certification_menu'); ?></div>
    <div class="card-wrap">
        <?php echo form_open_multipart(BASE_ADMIN_URL_CUSTOM . 'certification_menu/save', array('id' => 'saveform', 'name' => 'saveform')); ?>
        <ul class="form-outer-block">
            <li>
                <label for="inputName" class="main-label"><?php echo lang('title'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-12">
                        <?php
                        $title_data = array(
                            'name' => 'title',
                            'id' => 'title',
                            'value' => set_value('title', ((isset($title)) ? htmlspecialchars_decode($title) : '')),
                            'class' => 'input-control',
                            'maxlength'=>25,
                        );
                        ?>
                        <?php echo form_input($title_data); ?>
                        <?php if (form_error('title')) { ?><label class="input-label validation_error"><?php echo form_error('title'); ?></label> <?php } ?>
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('upload-section-logo'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <div class="custom-file-upload">
                            <input type="file" id="file" name="section_logo"  value="<?php echo $section_logo; ?>" />
                        </div>
                    </div>
                </div>
                <?php if (isset($section_logo) && $section_logo != '') { ?>
                    <div class="row">
                        <div class="form-col-12">
                            <?php
                                if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/" . $section_logo)) {
                                $is_section_logo_exists = 1;
                                ?>
                                    <img src='<?php echo site_url() . "assets/uploads/" . $section_logo; ?> '>
                                <?php } else { ?>
                                    <img class="siteImage" src='<?php echo site_url() . NOT_AVAILABLE_site_logo; ?> '>
                                <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </li>
            <li>
                <label class="main-label"><?php echo lang('parent_menu'); ?></label> 
                <div class="row">
                    <div class="form-col-12">
                        <div class="form-dropdown">
                            <select data-type="custom-dropdown" data-level="1" id="main_menu" name="main_menu_id" style="height: 10px !important;overflow-y: auto; !important">
                            <?php foreach ($certification_menus as $menu) {?>
                                <option value="<?=$menu['id']?>" <?=($menu['id']==$main_menu_id)?'selected':'';?>><?=$menu['title']?></option>
                            <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </li>
            <div id="dropdownBlock">
            </div>
             

            <li>
                <label class="main-label"><?php echo lang('section_description'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                    <?php
                        $ckeditor = array(
                        //ID of the textarea that will be replaced
                        'id' => 'section_description',
                        'path' => 'assets/ckeditor',
                        //Optionnal values
                        'config' => array(
                            'toolbar' => "Full", //Using the Full toolbar
                            'width' => "550px", //Setting a custom width
                            'height' => '100px', //Setting a custom height
                        ),
                        );
                    ?>
                        <textarea name="section_description" id="inputDesc" class="input-control"><?php echo isset($data['section_description']) && !empty($data['section_description']) ? htmlspecialchars_decode($data['section_description']) : ''; ?></textarea>
                        <?php 
                        echo display_ckeditor($ckeditor);
                        ?>
                        <?php if (form_error('section_description')) { ?><label class="input-label validation_error"><?php echo form_error('section_description'); ?></label> <?php } ?>
                    </div>

                </div>
            </li>

            <li>
                <label class="main-label"><?php echo lang('section_question'); ?></label> 
                <div class='row add-row'>
                    <div class="form-col-1">
                        <button class="btn-control addition" type="button" data-row="<div class='row add-row'>
                            
                        <div class='form-col-6'>
                            <input name='question[ids][{random}]' value='0' type='hidden'>
                            <input name='question[titles][{random}]' type='text' class='input-control' placeholder='Title' required>
                        </div>
                      
                        <div class='form-col-1'>
                            <button type='button' class='btn-control substract'>
                                <img src='images/minus-icon.png' alt='Minus'>
                            </button>
                        </div>
                    
                        </div>"><img src="images/plus-icon.png" alt="Plus"></button>
                    </div>
                </div> 
                <?php 
                if(isset($questions) && count($questions) > 0){
                foreach($questions as $question){?>
                <div class='row add-row'>

				    <div class='form-col-6'>
                    <input name='question[ids][]' value='<?php echo $question['id']; ?>' type='hidden'><input name='question[id][]' value='<?php echo $question['id']; ?>' type='hidden'>
					<input name='question[titles][]' type='text' class='input-control' placeholder='Title' value="<?php echo $question['section_question']; ?>">
				    
                    </div>
                    <div class='form-col-1'>
					<button type='button' class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button>
				    </div>
                    
                </div>
                <?php } }
                ?>
            </li>
           
            <li>
                <?php $statuslist = array('1' => 'Active', '0' => 'Inactive'); ?>
                <label class="main-label"><?php echo lang('status'); ?></label> 
                <div class="row">
                    <div class="form-col-12">
                        <div class="form-dropdown">
                            <?php echo form_dropdown('status', $statuslist, $status, 'data-type="custom-dropdown"'); ?>
                        </div>
                    </div>
                </div>
            </li>
            <li>
            <label class="main-label"><?php echo lang('upload_check'); ?></label>
            <div class="row">
                <div class="form-col-1">
                    <input type="checkbox" name="is_upload_checked" class="icheck checkboxlist check_box" value="1" <?= ($is_upload_checked == 1) ? 'checked' :''?>>
                </div>
            </div>
            </li>
        </ul>
        <input type="hidden" id="id" name="id" value="<?php echo $id; ?>" />
        <input type="hidden" name="slug" id="slug" value="<?php echo $slug; ?>" />
        <input type="hidden" value="" name="level" id="selected-level">

        <div class="form-btn-outer">
            <button type="submit" name="mysubmit" value="<?php echo lang('btn-save'); ?>" class="btn btn-secondary btn-submit"><?php echo lang('btn-save'); ?></button>
            <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'certification_menu'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('btn-cancel'); ?></a>
        </div>
        </form>            
    </div>
</article>
<script type="text/javascript">
var count = 2;
$(document).ready(function() {
    var parent_id = $("#main_menu").val();
    updateSubmenu(parent_id,2)
    $('#slug').slugify('#title');
    $(".btn-control.substract").click(function (e) {
	    e.preventDefault();
	    var $this = $(this);
	    $this.closest(".row").remove();
	});
    $(":input").each(function(i) {
        $(this).attr('tabindex', i + 1);
    })
});

function updateSubmenu(parent_id, level = 2) {
    var html = `
        <li id="submenu-level-${level}">
            <label class="main-label">Section ${level}</label>
            <div class="row">
                <div class="form-col-12">
                    <div class="form-dropdown">
                        <select data-type="custom-dropdown-update" data-level="${level}" id="main_menu_${level}" name="parent_id_${level}">
                            <option value="">--Select section--</option>
                        </select>
                    </div>
                </div>
            </div>
        </li>`;
    
    var id = $('#id').val();

    $.ajax({
        type: 'POST',
        url: '<?php echo site_url(); ?>certification_menu/submenus',
        data: {parent_id: parent_id, id: id},
        dataType: 'html',
        success: function(response) {
            if (response != null && response != '') {
                $('#submenu-level-' + level).remove();
                $('#dropdownBlock').append(html);
                $('#main_menu_' + level).append(response);

                $("select[data-type='custom-dropdown-update']").dropkick({
                    mobile: true
                });

                $('#submenu-level-' + level).show();
                $('#main_menu_' + level).change()
                $('#main_menu_' + level).change(function() {
                    var selectedValue = $(this).val();
                    if (selectedValue) {
                        updateSubmenu(selectedValue, level + 1);
                    } else {
                        $('li[id^="submenu-level-"]').filter(function() {
                            return $(this).attr('id').split('-')[2] > level;
                        }).remove();
                    }
                });
            }
            else{
                $('#submenu-level-' + level).remove();
            }
        }
    });
}


$(document).on('change', '#main_menu, select[data-type="custom-dropdown-update"]', function(e) {
    var parent_id = $(this).val();
    var currentLevel = $(this).data('level');
    $('li[id^="submenu-level-"]').filter(function() {
        return $(this).attr('id').split('-')[2] > currentLevel;
    }).remove();

    if (parent_id) {
        updateSubmenu(parent_id, currentLevel + 1);
    }

    var lastSelectedLevel = getLastSelectedLevel();
    $('#selected-level').val(lastSelectedLevel);
});

function getLastSelectedLevel() {
    let lastSelectedLevel = null;
    $('select[data-type="custom-dropdown-update"]').each(function() {
        if ($(this).val()) {
            lastSelectedLevel = $(this).data('level');
        }
    });

    return lastSelectedLevel;
}

</script>