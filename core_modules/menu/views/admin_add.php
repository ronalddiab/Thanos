<div class="main-container">
    <div id="moduleMiddle" class="grid-data">
        <div class="add-new">
            <?php echo anchor(get_current_section($this).'/menu',lang('menu-view'),'title="' . lang('menu-view') . '" style="text-align:center;width:100%;"');?>
        </div>
        <?php echo form_open(get_current_section($this)."/menu/save",array('id' => 'saveform','name' => 'saveform'));?>
        <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
            <tbody bgcolor="#fff">
                <tr>
                    <th><?php echo lang('menu-add-edit');?></th>
                </tr>
                <tr>
                    <td class="add-user-form-box">
                        <table cellspacing="1" cellpadding="5" border="0" width="100%">
                            <tbody>
                                <tr>
                                    <td align="right"><span class="star">*&nbsp;</span><label for="menu_name"><?php echo lang('menu-name');?></label>:</td>
                                    <td>
                                        <?php
                                        $menuArr = array();
                                        foreach ($menu_names as $key => $val)
                                        {
                                            $menuArr[$val['m']['id']] = $val['m']['value'];
                                        }
                                        if($id == "" || $id == 0)
                                        {
                                            $menuArr['add-menu'] = lang("menu-add");
                                        }
                                        echo form_dropdown('menu_name',$menuArr,array($menu_name),'id="menu_name" class="validate[required]"') . '&nbsp;&nbsp;';
                                        ?>

                                        <?php
                                        $display = ($menu_name == 'add-menu') ? "" : " display: none;";
                                        $inputData = array(
                                            'name' => 'new_menu',
                                            'id' => 'new_menu',
                                            'value' => set_value('new_menu',isset($new_menu) ? $new_menu : ''),
                                            'maxlength' => '100',
                                            'style' => 'width:198px; ' . $display,
                                            'class' => "validate[required,maxSize[100]]"
                                        );
                                        echo form_input($inputData);
                                        ?>

                                        <span class="validation_error"><?php echo form_error('new_menu');?> <?php echo form_error('menu_name');?></span>
                                    </td>

                                </tr>
                                <tr>
                                    <td align="right"><span class="star">*&nbsp;</span><label for="title"><?php echo lang('menu-title');?></label>:</td>
                                    <td>
                                        <?php
                                        $inputData = array(
                                            'name' => 'title',
                                            'id' => 'title',
                                            'value' => set_value('title',$title),
                                            'maxlength' => '100',
                                            'style' => 'width:198px',
                                            'class' => "validate[required,maxSize[100]]"
                                        );
                                        echo form_input($inputData);
                                        ?>
                                        <span class="validation_error"><?php echo form_error('title');?></span>
                                    </td>
                                </tr>

<!--                                <tr>
                                    <td align="right"><label for="module_name"><?php echo lang('menu-module');?></label>:</td>
                                    <td>
                                        <?php
                                //        echo form_dropdown('module_name',$modules,$module_name,'id="module_name"');
//                                        echo form_dropdown('module_name',$modules, 'sample','id="module_name"');
                                        ?>
                                        &nbsp;&nbsp;<span id="subpages"></span>

                                    </td>
                                </tr>-->

                                <tr>
                                    <td align="right"><label for="link"><?php echo lang('menu-section');?></label>:</td>
                                    <td>
                                        <?php
                                        $configArr = (array) $this->theme->ci()->config;
                                        $menu_section = $configArr['config']['section_name'];

echo form_dropdown('menu_section',$menu_section,array($menu_section_val),'id="menu_section"') . '&nbsp;&nbsp;';
                                        ?>
                                    </td>
                                </tr>


                                <tr>
                                    <td align="right"><span class="star">*&nbsp;</span><label for="link"><?php echo lang('menu-link');?></label>:</td>
                                    <td>
                                        <?php
                                        $inputData = array(
                                            'name' => 'link',
                                            'id' => 'link',
                                            'value' => set_value('link',$link),
                                            'maxlength' => '100',
                                            'style' => 'width:198px',
                                             'class' => "validate[required]"
                                        );
                                        echo form_input($inputData);
                                        ?>
                                        <span class="validation_error"><?php echo form_error('link');?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"><label for="parent_id"><?php echo lang('menu-parent');?></label>:</td>
                                    <td id="parent_menu">
                                        <?php
                                        echo form_dropdown('parent_id',$menu_items,array($parent_id),'id=parent_id');
                                        ?>
                                        <span class="validation_error"><?php echo form_error('parent_id');?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"><label for="status"><?php echo lang('menu-status'); ?></label>:</td>
                                    <td>
                                        <?php
                                        $statuslist = array('1' => lang('menu-active'),'0' => lang('menu-inactive'));
                                        echo form_dropdown('status',$statuslist,array($status),'');
                                        ?>
                                        <span class="validation_error"><?php echo form_error('status');?></span>
                                    </td>
                                </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="submit-btns clearfix">
            <input type="hidden" id="lang_id" name="lang_id" value ="<?php echo (isset($lang_id)) ? $lang_id : '1';?>" />
            <input type="hidden" id="language_name" name="language_name" value ="<?php echo (isset($language_name)) ? strtolower($language_name) : 'en';?>" />
            <input type="hidden" id="id" name="id" value ="<?php echo (isset($id)) ? $id : '0';?>" />
            <?php
            $submit_button = array(
                'name' => 'savemenu',
                'id' => 'savemenu',
                'value' => lang('menu-save'),
                'title' => 'Save',
                'class' => 'inputbutton',
            );
            echo form_submit($submit_button);

            $langname = (isset($language_name)) ? strtolower($language_name) : 'en';
            $siteurl = site_url(get_current_section($this).'/menu') . '/index/' . $langname;

            $cancel_button = array(
                'name' => 'button',
                'title' => lang('menu-cancel'),
                'class' => 'inputbutton',
                'onclick' => "location.href='" . $siteurl . "'"
            );
            echo "&nbsp;";
            echo form_button($cancel_button,lang('menu-cancel'));
            ?>
        </div>
        <?php echo form_close();?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){

        $('#new_menu').focus();

        jQuery("#saveform").validationEngine({
            //promptPosition : '<?php echo VALIDATION_ERROR_POSITION;?>',
            validationEventTrigger: "submit",
            'custom_error_messages': {
                // Custom Error Messages for Validation Types
                'required': {
                    'message': "<?php echo lang('menu-required');?>"
                }
            }
        });

        $('#menu_name').on('change',function() {
            if ($(this).val() == 'add-menu')
            {
                $('#new_menu').show('slow');
                $('#parent_id').html('<option id="0"><?php echo lang('menu-root');?></option>');
            }
            else
            {
                $('#new_menu').val('');
                $('#new_menu').hide('slow');
                $('#new_menu').validationEngine('hide');
                //load menu items
                getmenulist($(this).val());
            }
        });

        $('#module_name').on('change',function(){
            if($(this).val() == 'cms'){
                getsubpages($(this).val());
                $('#link').attr('readonly',true);
            }else{
                $('#link').attr('readonly',false);
                //generate link
                $("#subpages").html('');
                $("#subpages").hide();
            }
        });

        getsubpages = function(modulename){
            blockUI();
            $.ajax({
                type:'POST',
                url:'<?php echo base_url().get_current_section($this);?>/menu/get_subpages',
                data:{<?php echo $this->theme->ci()->security->get_csrf_token_name();?>:'<?php echo $this->theme->ci()->security->get_csrf_hash();?>',
                        module_name:encodeURI(modulename),
                        lang_id:$('#lang_id').val()
                    },
                    success: function(data) {
                        if(data == ''){
                            $("#subpages").hide();
                        }else{
                            $('#link').val('');
                            $("#subpages").html(data);
                            $("#subpages").show();
                        }
                        unblockUI();
                    }
                });
            }

            getmenulist = function(menu_name){
                blockUI();
                $.ajax({
                    type:'POST',
                    url:'<?php echo base_url().get_current_section($this);?>/menu/get_menulist',
                    data:{<?php echo $this->theme->ci()->security->get_csrf_token_name();?>:'<?php echo $this->theme->ci()->security->get_csrf_hash();?>',
                        menu_name:encodeURI(menu_name),
                        lang_id:$('#lang_id').val(),
                        id: $('#id').val()
                    },
                    success: function(data) {
                        if(data == ''){
                            $("#parent_menu").hide();
                        }else{
                            $("#parent_menu").html(data);
                            $("#parent_menu").show();
                        }
                        unblockUI();
                    }
                });
            }

            change_subpages = function(page){
                if(page != '0'){
                    $('#link').val(page);
                }else{
                    $('#link').val('');
                }
            }

        });
</script>