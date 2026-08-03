<div class="main-container">       
    <div id="moduleMiddle" class="grid-data">
        <?php if(isset($orig) && is_array($orig) && count($orig)) : ?>
            <?php echo form_open(current_url(), 'class="form-horizontal"'); ?>

            <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
                <tbody bgcolor="#fff">
                    <tr>
                        <th align="left"><?php echo lang('tr_language') . ': ' . ucfirst($trans_lang) ?></th>
                    </tr>
                    <tr>
                        <td>
                            <fieldset>
                                <legend><?php echo lang('tr_translate_file') . ": $lang_file" ?></legend>                            
                                <input type="hidden" name="trans_lang" value="<?php e($trans_lang) ?>" />                                            
                                <table cellspacing="1" cellpadding="4" width="100%">                    
                                    <?php foreach ($orig as $key => $val) : ?>
                                        <tr>
                                            <td style="width:50%">
                                                <label class="control-label" for="lang<?php echo $key ?>"><?php e($val) ?></label>
                                            </td>
                                            <td style="width:50%">
                                                <input type="text" class="input-xxlarge" name="lang[<?php echo $key ?>]" id="lang<?php echo $key ?>" value="<?php e(isset($new[$key]) ? $new[$key] : $val) ?>"  style="width:100%"/>                        
                                            </td>
                                        </tr>                    
                                    <?php endforeach; ?>                
                                </table>                    
                            </fieldset>
                        </td>
                    </tr>                    
                </tbody>
            </table>
            <div class="submit-btns clearfix">                
                <?php
                $submit_button = array(
                    'name' => 'submit',
                    'id' => 'submit',
                    'value' => 'Save',
                    'title' => 'Save',
                    'class' => 'inputbutton',
                );
                echo form_submit($submit_button);
                ?>
                <?php e(lang('ci_or')) ?> 
                <button type="button" onclick="location.href='<?php echo site_url(get_current_section($this).'/translate/index/' . $trans_lang); ?>'" class="inputbutton" title="Cancel" id="cancel">Cancel</button>                               
            </div>
            <?php echo form_close(); ?>
        <?php else : ?>
        <?php endif; ?>
    </div>
</div>
