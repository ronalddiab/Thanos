<style type="text/css">
.survey_form_image {
	width: 75px;
    height: 75px;
}
.survey_form_image_div {
	width: 100px;
    height: 100px;
    border: 2px solid #dbdbdb !important;
    padding: 10px;
}
.resp-tabs-list li:first-child {
    margin: 0 2px;
}
.resp-tabs-list li {
    min-width: 210px;
}
.resp-tabs-list li.resp-tab-active {
    margin-top:-5px;
}
.question-label {
    background-color: #007856;
    color: white;
    width: 38px;
    height: 38px;
    text-align: center;
    padding-top: 5px;
    border-radius: 50%;
    font-size: 10px;
}

pre {
    word-break: break-word;
    white-space:pre-wrap;
    border: none;
    background-color: transparent;
    color: #c8c0bc !important;
}
pre a{
    text-decoration: underline;
    color: #95ceff;
}
pre p a{
    text-decoration: underline;
    color: #95ceff;
}
</style>
<?php 
echo add_js(array('easyResponsiveTabs', 'MonthPicker.min' , 'bootstrap-datepicker-new','bootstrap-multiselect'));
echo add_css(array('MonthPicker.min', 'bootstrap-datepicker-new','bootstrap-multiselect'));

function returnDecodedString($string) {
    return html_entity_decode(html_entity_decode(html_entity_decode(html_entity_decode($string))));
}

function render_element_by_type($value, $if_survey_is_open) {    
    $isDisabled = $if_survey_is_open ? "" : "disabled";
    switch($value['question_type']) {
        case 'textbox':
            $question_text_data = array(
                'name' => 'survey_question_'.$value['question_id'],
                'id' => 'survey_question_'.$value['question_id'],
                'class' => 'input-control',
                'maxlength' => '256',
                'value' => set_value('survey_question_'.$value['question_id'], ((isset($value['questions_answer'])) ? returnDecodedString($value['questions_answer']) : ''))
            );
            if(isset($isDisabled) && !empty($isDisabled)) {
                $question_text_data['disabled'] = $isDisabled;
            }
            echo form_input($question_text_data);
            break;
        case 'textarea':
            $question_options_data = array(
                'name' => 'survey_question_'.$value['question_id'],
                'id' => 'survey_question_'.$value['question_id'],
                'size' => '50',
                'maxlength' => '256',
                'class' => 'input-control textarea-control',
                'value' => set_value('survey_question_'.$value['question_id'], ((isset($value['questions_answer'])) ? returnDecodedString($value['questions_answer']) : ''))
            );
            if(isset($isDisabled) && !empty($isDisabled)) {
                $question_options_data['disabled'] = $isDisabled;
            }
            echo form_textarea($question_options_data);
            echo display_ckeditor($ckeditor);
            break;
        case 'radio':
            $options = explode('|', $value['question_options']);
            foreach ($options as $optionKey => $optionValue) {
                $isChecked = (isset($value['questions_answer']) && (returnDecodedString($value['questions_answer']) == returnDecodedString($optionValue))) ? "checked" : "" ;
               echo 
               '<input id="survey_question_'.$value['question_id'].'" name="survey_question_'.$value['question_id'].'" type="radio" class="" value="'.$optionValue.'" '.$isChecked.' '.$isDisabled.'/>
                <label for="survey_question_'.$value['question_id'].'" class="" style="display:inline;">'.returnDecodedString($optionValue).'</label>';
               echo '<br>';
            }
            break;
        case 'dropdown':
            $options = explode('|', $value['question_options']);
            $selectedValue = array_search(returnDecodedString($value['questions_answer']), $options);
            foreach ($options as $key => $option) {
                if(strlen($option) > 68) {
                    $options[$key] = returnDecodedString(substr($option,0,62)."....");
                } else {
                    $options[$key] = returnDecodedString($option);
                }
            }
            $inserted = ['' => 'None Selected'];
            array_splice( $options, 0, 0, $inserted );
            $id = 'survey_question_'.$value['question_id'];
            echo 
            '<div class="form-dropdown">'.
                form_dropdown('survey_question_'.$value['question_id'], $options, $selectedValue+1, 'id="'.$id.'" data-type="custom-dropdown" '.$isDisabled.'').
            '</div>';
            break;
        case 'checkbox':
            $options = explode('|', returnDecodedString($value['question_options']));
            $selectedValues = isset($value['questions_answer']) ? explode('|', returnDecodedString($value['questions_answer'])) : [] ;
            foreach ($options as $optionKey => $optionValue) {
                $isChecked = in_array($optionValue, $selectedValues) ? "checked" : "" ;
                echo 
                '<div class="row"><div class="form-col-1" style="width:30px"><input id="survey_question_'.$value['question_id'].'" name="survey_question_'.$value['question_id'].'[]" type="checkbox" class="" value="'.($optionValue).'" '.$isChecked.' '.$isDisabled.'/></div>
                <div class="form-col-*"><label for="survey_question_'.$value['question_id'].'" class="" style="display:inline;">'.html_entity_decode($optionValue).'</label></div></div>';
             }
            if (form_error('survey_question_'.$value['question_id'].'[]')) { echo '<label class="input-label validation_error">'.form_error('survey_question_'.$value['question_id'].'[]').'</label>'; }
             break;
        case 'multiselect':
            $options = explode('|', returnDecodedString($value['question_options']));
            if (isset($value['questions_answer'])) {
                foreach(explode('|', $value['questions_answer']) as $row) {
                    $selectedValue[] = array_search(returnDecodedString($row), $options);
                }
            }
            foreach ($options as $key => $option) {
                if(strlen($option) > 68) {
                    $options[$key] = returnDecodedString(substr($option,0,62)."....");
                } else {
                    $options[$key] = returnDecodedString($option);
                }
            }
            $id = 'multi_select_survey_question_'.$value['question_id'];
            echo 
            '<div class="form-dropdown-multiselect">'.
                form_multiselect('survey_question_'.$value['question_id'].'[]', $options, $selectedValue, 'id="'.$id.'" '.$isDisabled.'').
            '</div>';
            if (form_error('survey_question_'.$value['question_id'].'[]')) { echo '<label class="input-label validation_error">'.form_error('survey_question_'.$value['question_id'].'[]').'</label>'; }
            break;
        case 'file':
            echo form_upload('survey_question_'.$value['question_id'].'[]' , array('class' => 'form-control','id' => $id, 'multiple'=>"true",  $isDisabled));
            if(isset($value['questions_answer']) && !empty($value['questions_answer'])) {
                $image_path = site_url() . "assets/uploads/" .$value['questions_answer'];
                echo '<div class="form-col-2 survey_form_image_div">
                        <a class="close delete_survey_form_image" href="#" data-image="'.$value['questions_answer'].'" data-question-answer-id="'.$value['survey_questions_answer_id'].'" data-question-id="'.$value['question_id'].'"">×</a>
                        <a href="'.$image_path.'" target="_blank" >
                            <img class="survey_form_image " src="'.$image_path.'"/>
                        </a>
                    </div>';
            }
            break;
        default:
            echo "default";
            break;
    }
}
?>
<?php if(isset($survey_questions_form_render) && !empty($survey_questions_form_render)) { ?>
<article class="card">
    <div class="article-header">
            <div class="row">
                <div class="col-md-6"><?php echo lang('add_survey_form'); ?></div>
                <?php $export_archieve_survey_permission = check_user_permission_by_label('admin.survey.exportarchieveone'); ?>
                <?php if($export_archieve_survey_permission){ ?>
                    <div class="col-md-2 pull-right"><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>survey/exportarchieveone" class="btn btn-primary" style="width:100%;background-color:#D49947;"><?php echo lang('survey-report-archieve');?></a></div>
                <?php } ?>
                <?php $export_survey_permission = check_user_permission_by_label('admin.survey.exportone'); ?>
                <?php if($export_survey_permission){ ?>
                    <div class="col-md-2 pull-right"><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>survey/exportone" class="btn btn-primary" style="width:100%"><?php echo lang('survey-report');?></a></div>
                <?php } ?> 
                <?php $pdf_path = site_url() . "assets/uploads/SurveyInstructions.pdf";?>
                <!-- <div class="col-md-2"><a href="<?php echo $pdf_path; ?>" target="_blank" class="btn btn-primary" style="width: 100%;">Instructions</a></div> -->
            </div>    
        </div>
    <div class="card-wrap">
    <div id="survey-tabs" class="Tab-block">
        <?php echo form_open_multipart('', array('id' => 'saveform', 'name' => 'saveform')); ?>
        <ul class="resp-tabs-list hor_1 clearfix">
            <?php 
            $i = 1;
            foreach ($survey_questions_form_render as $key => $valueSection) { ?>
                <li class="tab-custom-id-<?php echo $i;?>"><?php echo ucwords(str_replace("and","&",$key)); ?></li>
            <?php 
            $i++; 
            } 
            ?>
        </ul>        
            <div class="form-btn-outer" style="margin-bottom: -20px;">
                <button type="submit" name="surveyFormSave" id="surveyFormSave" value="<?php echo lang('save'); ?>" class="btn btn-secondary btn-submit" style="padding-left: 10px;padding-right: 10px;background-color:#195444;width: 100px;"><?= lang('save');?></button>
                <button type="submit" name="surveyFormSubmit" id="surveyFormSubmit" value="<?php echo lang('submit'); ?>" class="btn btn-secondary btn-submit" style="padding-left: 10px;padding-right: 10px;background-color:#D49947;width: 100px;"><?= lang('submit');?></button>
            </div>
            <?php $i = 1; foreach ($survey_questions_form_render as $key => $valueSection) { ?>
            <div class="resp-tabs-container hor_1"> 
                <div id="tab-<?php echo $i;?>" data-tab-id="<?php echo $i;?>">
                    <div class="panel panel-primary">
                        <div class="panel-body">
                            <!-- <ul class="form-outer-block"> -->
                                <?php $number = 1; 
                                if(isset($valueSection) && !empty($valueSection)) { 
                                    foreach ($valueSection as $key => $value) { 
                                        if(isset($value['s']['question_text']) && isset($value['s']['question_id']) && !empty($value['s']['question_text']) && !empty($value['s']['question_id'])) { ?>
                                    <!-- <li> -->
                                    <div class="row">
                                        <div class="form-col-6">
                                            <?php 
                                            $nameHidden = "order_number_".$number.'_'.$value['s']['question_id']; 
                                            $questionLabelValue = isset($value['s']['order_number']) && !empty($value['s']['order_number'])? $value['s']['order_number'] : displayLabelOnSurveyForm($value['s']['section'], $number)
                                            ?>
                                            <div class="form-col-1" style="padding:0px;width: 45px;min-height: 60px;">
                                                <input type="hidden" name="<?php echo $nameHidden;?>" value="<?php echo $questionLabelValue;?>" />
                                                <span><h4 class="question-label"><?php echo $questionLabelValue; ?></h4></span>
                                            </div>
                                            <div class="form-col-*" style="padding:0px;min-height:40px;">
                                                <label style="font-weight:500px!important;overflow-wrap: break-word;display: block;"><?php echo $value['s']['question_text']; ?>  
                                                    <?php if($value['s']['required'] == 'Yes') { ?> <span class="asterisk">*</span><?php } ?>
                                                </label>
                                            </div>
                                            <?php 
                                            if(isset($value['s']['question_description']) && !empty($value['s']['question_description'])) {
                                                echo '<br>';
                                                echo '<label class="input-label description"><pre>'.html_entity_decode($value['s']['question_description']).'</pre></label>';
                                            }
                                            ?>
                                        </div>
                                        <div class="form-col-6">
                                            <?php
                                                render_element_by_type($value['s'], $if_survey_is_open); $number++;
                                            ?>
                                            <?php if (form_error('survey_question_'.$value['s']['question_id'])) { ?><label class="input-label validation_error"><?php echo form_error('survey_question_'.$value['s']['question_id']); ?></label> <?php } ?>
                                        </div>
                                    </div>
                                    <br/>
                                    <?php 
                                    if($value['s']['is_upload']) {
                                        $class = 'survey_form_image_div';
                                        $name = "survey_question_upload_".$value['s']['question_id'];
                                        echo '<div class="row">
                                            <div class="form-col-3">
                                                <label style="font-weight:500px!important;overflow-wrap: break-word;">'.lang('insert-invoice-scan').'</label>
                                            </div>
                                            <div class="form-col-6" style="padding-left: 17.7%;">
                                                <input name="'.$name.'[]" id="'.$name.'" type="file" class="custom-file-upload form " multiple="" >
                                            </div>';
                                            foreach (explode('|',$value['s']['questions_upload']) as $keyUpload => $valueUpload) {
                                                $extension = substr($valueUpload, -3);
                                                if($valueUpload != '') {
                                                    if($valueUpload != '') {
                                                        if($extension != 'pdf') {
                                                            $fileName = $image_path = site_url() . "/assets/uploads/". $valueUpload;
                                                        } else {
                                                            $fileName = site_url() . "/assets/uploads/". $valueUpload;
                                                            $image_path = site_url() . "/assets/uploads/pdf-image.png";
                                                        }
                                                    } else {
                                                        $image_path = site_url() . "/assets/uploads/no-image-available.jpg";
                                                    }
                                                } else {
                                                    $image_path = site_url() . "/assets/uploads/no-image-available.jpg";
                                                }
                                                echo '<div class="form-col-2 '.$class.'">
                                                    <a class="close delete_survey_form_image" href="#" data-image="'.$valueUpload.'" data-question-answer-id="'.$value['s']['survey_questions_answer_id'].'" data-question-id="'.$value['s']['question_id'].'">×</a>
                                                    <a href="'.$fileName.'" target="_blank" >
                                                        <img class="survey_form_image '.$name.'" src="'.$image_path.'"/>
                                                    </a>
                                                </div>';
                                            }
                                        echo '</div><br/>';
                                    } 
                                ?>
                                <!-- </li> -->
                                <?php } 
                                }
                                }?>
                            <!-- </ul> -->
                        </div>
                    </div>
                </div>
            </div>
            <?php $i++; } ?>
            <div class="form-btn-outer">
                <input type="hidden" name="nextTabPost" id="nextTabPost" value="0" />            
                <button id="submitNext" class="btn btn-secondary btn-submit" style="padding-left: 10px;padding-right: 10px;width: 100px;">Next</button>
                <button type="submit" name="surveyFormSave" id="surveyFormSave" value="<?php echo lang('save'); ?>" class="btn btn-secondary btn-submit" style="padding-left: 10px;padding-right: 10px;background-color:#195444;width: 100px;"><?= lang('save');?></button>
                <button type="submit" name="surveyFormSubmit" id="surveyFormSubmit" value="<?php echo lang('submit'); ?>" class="btn btn-secondary btn-submit" style="padding-left: 10px;padding-right: 10px;background-color:#D49947;width: 100px;"><?= lang('submit');?></button>
                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'survey/survey_form'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('cancel'); ?></a>
            </div>
        <?php echo form_close();?>
    </div>
</article>
<?php } else { ?>
    <div class="table-responsive">                  
        <table class="table table-striped" >
            <tr>
                <td><?php echo lang('no-records') ?></td>
            </tr>
        </table>
    </div>
<?php } ?>
<script type="text/javascript">
     $(document).ready(function () {
        var isSurveyOpen = "<?php echo $if_survey_is_open;?>";
        if(isSurveyOpen != 1) {
            $('input.file-upload-input').each(function (index, value) {
                $(this).prop('disabled', true);
            });
        }
        
        $('.survey_form_image').each(function() {
        	var isrc = $(this).attr('src');
        	if(!(isrc.includes("no-image-available.jpg")))
        	{
        		$(this).parents('.survey_form_image_div').find('.delete_survey_form_image').show();
        		$(this).parents('.survey_form_image_div').css('height', '120px');
        	}
        	else
            {
                $(this).parents('.survey_form_image_div').find('.delete_survey_form_image').hide();
        		$(this).parents('.survey_form_image_div').css('height', '100px');
            }
        });

        $('.Tab-block').easyResponsiveTabs({
            type: 'default',
            width: '100',
            fit: true,
            tabidentify: 'hor_1',
            activate: function (event) {
                // If need on tab change
            }
        });
        $('select[id^="multi_select_survey_question_"]').multiselect({
            maxHeight: 200,
            buttonWidth: '100%',
            numberDisplayed: 1
        });

        $('.delete_survey_form_image').click(function()
        {
            res = confirm('<?php echo lang('delete_confirm_survey_answer') ?>');
            if(res){
                var $this = $(this);
                var questionId = $this.attr("data-question-id");
                var questionAnswerId = $this.attr("data-question-answer-id");
                var imageName = $this.attr("data-image");

                $.ajax({
                    type:'POST',
                    url:'<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>survey/delete_survey_form_image',
                    data:{questionId:questionId,questionAnswerId:questionAnswerId,imageName:imageName},
                    error: function(){
                    // alert("Server problem. Please try again.");
                        return false;
                    },
                    complete: function(){
                    },
                    success: function(data) {
                        location.reload();
                    }
                });
            }else{
                return false;
            }            
        }); 
     });

    $('#submitNext').click(function()
    {
        var tabId = 0;
        $('.resp-tab-item').each(function() {
            var currentElement = $(this);
            if(currentElement.hasClass("resp-tab-active")) {
                var activetab = currentElement.attr('aria-controls');
                const stringArray = activetab.split("-");
                if(parseInt(stringArray[1]) == 6) {
                    tabId = 0;
                } else {
                    tabId = parseInt(stringArray[1]) + 1;
                }                
            }
        });
        // $('li[aria-controls="hor_1_tab_item-'+tabId+'"]').click();
        window.scrollTo(0, 0);
        $("#nextTabPost").val(tabId + 1);
        $("#surveyFormSave").click();
        return false;
    });

    $(function() {
        $('.description a[href]').attr('target', '_blank');
    });

</script>