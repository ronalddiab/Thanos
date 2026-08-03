<?php 

    $question_types = explode(',', QUESTION_TYPES);
    $question_types = SelectPromptDropdown($question_types);
    // pre($survey_question);
    if (isset($survey_question) && isset($survey_question['question_type'])) {
        $dropdownKey = array_search(ucfirst($survey_question['question_type']), $question_types);
    }

    else {

        $dropdownKey = 0;

    }



    $sections = explode('|', QUESTION_SECTIONS);
    $sections = SelectPromptDropdown($sections);
    if (isset($survey_question) && isset($survey_question['section'])) {
        $dropdownSectionKey = array_search(ucfirst($survey_question['section']), $sections);
    }

    else {

        $dropdownSectionKey = 0;

    }



    $sources = explode('|', QUESTION_SOURCES);
    $sources = SelectPromptDropdown($sources);
    if (isset($survey_question) && isset($survey_question['source'])) {
        $dropdownSourceKey = array_search(ucfirst($survey_question['source']), $sources);
    }

    else {

        $dropdownSourceKey = 0;

    }
    $ckeditor = array(
        //ID of the textarea that will be replaced
        'id' => 'question_description',
        'path' => 'assets/ckeditor',
        //Optionnal values
        'config' => array(
            'toolbar' => "Full", //Using the Full toolbar
            'width' => "550px", //Setting a custom width
            'height' => '100px', //Setting a custom height
        ),
    );
?>

<article class="card">

    <div class="article-header"><?php echo isset($data['id']) && $data['id']>0?lang('edit_form_fields'):lang('add_form_fields'); ?></div>

    <div class="card-wrap">

    <?php echo form_open_multipart('', array('id' => 'saveform', 'name' => 'saveform')); ?>

            <ul class="form-outer-block">

                <li>

                    <label class="main-label"><?php echo lang('question_text'); ?> <span class="asterisk">*</span></label>

                    <div class="row">

                        <div class="form-col-12">

                        <?php

                            $question_text_data = array(

                                'name' => 'question_text',

                                'id' => 'question_text',

                                'class' => 'input-control',
                                'maxlength' => '256',
                                'value' => set_value('question_text', ((isset($survey_question['question_text'])) ? $survey_question['question_text'] : ''))

                            );

                            echo form_input($question_text_data);

                            ?>

                            <?php if (form_error('question_text')) { ?><label class="input-label validation_error"><?php echo form_error('question_text'); ?></label> <?php } ?>

                        </div>

                    </div>

                </li>

                <li>

                    <label class="main-label"><?php echo lang('question_type'); ?>  <span class="asterisk">*</span></label>

                    <div class="row">

                        <div class="form-col-12">

                            <div class="form-dropdown">
                                <?php echo form_dropdown('question_type', $question_types, $dropdownKey,'id="question_type" data-type="custom-dropdown"');?>
                            </div>

                            <?php if (form_error('question_type')) { ?><label class="input-label validation_error"><?php echo form_error('question_type'); ?></label><?php } ?>

                        </div>

                    </div>

                </li>

                <li class="question_option_div">

                    <label class="main-label"><?php echo lang('question_options'); ?>  <span class="asterisk">*</span> <a href="#" data-toggle="tooltip" id="tooltip-question-option" title="Question option required for selected question type" data-original-title="Question option required for selected question type"><img alt="Note" src="images/notify-icon.png"></a></label>

                    <div class="row">

                        <div class="form-col-12">

                            <?php

                            $question_options_data = array(

                                'name' => 'question_options',

                                'id' => 'question_options',

                                'size' => '50',
                                'class' => 'input-control textarea-control',

                                'value' => set_value('question_options', ((isset($survey_question['question_options'])) ? html_entity_decode(html_entity_decode($survey_question['question_options'])) : ''))

                            );

                            echo form_textarea($question_options_data);

                            echo display_ckeditor($ckeditor);

                            ?>

                            <?php if (form_error('question_options')) { ?><label class="input-label validation_error"><?php echo form_error('question_options'); ?></label> <?php } ?>

                        </div>

                    </div>

                </li>
                <li>
                    <label class="main-label"><?php echo lang('required'); ?></label>

                    <div class="row">

                        <div class="form-col-12">

                            <input id="required" name="required" type="radio" class="" <?php if(isset($survey_question) && isset($survey_question['required']) && $survey_question['required']=='No') echo "checked='checked'"; ?> value="No" />

                            <label for="required" class="">No</label>



                            <input id="required" name="required" type="radio" class="" <?php if(isset($survey_question) && isset($survey_question['required']) && $survey_question['required']=='Yes') echo "checked='checked'"; ?> value="Yes" />

                            <label for="required" class="">Yes</label>

                        </div>

                    </div>
                </li>
                <li>

                    <label class="main-label"><?php echo lang('source'); ?> <span class="asterisk">*</span></label>

                    <div class="row">

                        <div class="form-col-12">
                            <div class="form-dropdown">
                                <?php echo form_dropdown('source', $sources, $dropdownSourceKey,'id="source" data-type="custom-dropdown"');?>
                            </div>

                            <?php if (form_error('source')) { ?><label class="input-label validation_error"><?php echo form_error('source'); ?></label><?php } ?>

                        </div>

                    </div>

                </li>

                <li>
                    <label class="main-label"><?php echo lang('question_description'); ?></label>
                    <div class="row">

                        <div class="form-col-12">

                        <?php

                            $question_description_data = array(

                                'name' => 'question_description',

                                'id' => 'question_description',
                                'size' => '50',
                                'maxlength' => '256',
                                'class' => 'input-control textarea-control',
                                'value' => set_value('question_description', ((isset($survey_question['question_description'])) ? html_entity_decode($survey_question['question_description']) : ''))
                            );
                            echo form_textarea($question_description_data);
                            echo display_ckeditor($ckeditor);
                            ?>

                            <?php if (form_error('question_description')) { ?><label class="input-label validation_error"><?php echo form_error('question_description'); ?></label> <?php } ?>

                        </div>

                    </div>

                </li>

                <li>

                    <label class="main-label"><?php echo lang('section'); ?>  <span class="asterisk">*</span></label>

                    <div class="row">

                        <div class="form-col-12">

                            <div class="form-dropdown">
                                <?php echo form_dropdown('section', $sections, $dropdownSectionKey,'id="section" data-type="custom-dropdown"');?>
                            </div>

                            <?php if (form_error('section')) { ?><label class="input-label validation_error"><?php echo form_error('section'); ?></label><?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <label class="main-label">Upload File <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-12">
                            <label class="checkbox-outer col-sm-4">
                                <input name='is_upload' class='icheck' value='1' type='checkbox' <?php echo ($survey_question['is_upload']) ? 'checked' : ''; ?>>
                            </label>
                        </div>
                    </div>
                </li>
                <br/>
            </ul>

            <input type="hidden" name="id" value="<?php echo $id; ?>" />

            <div class="form-btn-outer">

                <button type="submit" name="questionsubmit" id="questionsubmit" value="<?php echo lang('save'); ?>" class="btn btn-secondary btn-submit">Submit</button>

                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'survey'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('cancel'); ?></a>

            </div>

        <?php

        echo form_hidden('id', (isset($data['id'])) ? $data['id'] : '0' );

        echo form_close();

        ?>

    </div>

</article>

<script>

$(document).ready(function(){  

    var value = $("#question_type").val();

    if(value != 'undefined' && value == 2 || value == 3 || value == 5){

        $( ".question_option_div" ).fadeOut( "slow", function() {});

    } else {

        $( ".question_option_div" ).fadeIn( "slow", function() {});

    }  

    $('#question_type').on('change',function(){

        var value = $(this).val();

        if(value == 2 || value == 3 || value == 5){

            $( ".question_option_div" ).fadeOut( "slow", function() {});

        } else {

            $( ".question_option_div" ).fadeIn( "slow", function() {});

        }

    });

});

</script>