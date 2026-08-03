<div class="main-container">
    <div id="moduleMiddle" class="grid-data">
        <?php echo form_open(); ?>
        <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
            <tr>
                <td style="width:10%">
                    <strong><label><?php e(lang('tr_current_lang')); ?></label></strong>
                </td>
                <td style="width:15%">
                    <select name="trans_lang" id="trans_lang">
                        <?php foreach ($languages as $lang) : ?>
                            <option value="<?php e($lang) ?>" <?php echo isset($trans_lang) && $trans_lang == $lang ? 'selected="selected"' : '' ?>><?php e(ucfirst($lang)) ?></option>
                        <?php endforeach; ?>
                        <option value="other"><?php e(lang('tr_other')); ?></option>
                    </select>
                    &nbsp;&nbsp;
                    <input type="text" name="new_lang" id="new_lang" style="display: none" value="" />                   
                </td>   
                <td>
                    <input type="submit" name="select_lang" class="btn btn-small btn-primary" value="<?php e(lang('tr_select_lang')); ?>" />    
                </td>
            </tr>
        </table>
        <?php echo form_close(); ?>

        <!-- Core -->
        <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
            <tbody bgcolor="#fff">
                <tr>
                    <th align="left"><?php echo lang('tr_core') ?></th>
                </tr>

                <?php foreach ($lang_files as $file) : ?>
                    <tr class="odd-row">
                        <td>
                            <a href="<?php echo site_url('/'.get_current_section($this).'/translate/edit/' . $trans_lang . '/' . $file) ?>">
                                <?php e($file); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>                

            </tbody>
        </table>

        <!-- Modules -->
        <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
            <tbody bgcolor="#fff">
                <tr>
                    <th align="left"><?php echo lang('tr_modules') ?></th>
                </tr>

                <?php if(isset($modules) && is_array($modules) && count($modules)) : ?>
                    <?php foreach ($modules as $file) : ?>
                        <tr class="odd-row">
                            <td>
                                <a href="<?php echo site_url('/'.get_current_section($this).'/translate/edit/' . $trans_lang . '/' . $file) ?>">
                                    <?php e($file); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td>
                            <div class="alert alert-info fade in">
                                <a class="close" data-dismiss="alert">&times;</a>		
                                <?php echo lang('tr_no_modules'); ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>               

            </tbody>
        </table>

    </div>
    <script type="text/javascript">
        $(document).ready(function(e){

            $('#trans_lang').change(function() {
                var lang = $(this).find("option:selected").val();
		
                if (lang == 'other')
                {
                    $('#new_lang').show('slow');
                }
                else
                {
                    $('#new_lang').hide('slow');
                }
            });

        });
    </script>