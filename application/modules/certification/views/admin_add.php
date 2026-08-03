<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
?>
<style>
    /* Style for tabs */
    .tab-container {
        width: 100%;
        margin: 0 auto;
        font-family: Arial, sans-serif;
    }

    .tab-header {
        display: flex;
        background-color: #5ba65c;
        color: white;
    }

    .tab-header button {
        background-color: #5ba65c;
        color: white;
        padding: 10px 20px;
        font-size: 16px;
        border: none;
        cursor: pointer;
        flex: 1;
    }

    .tab-header button.active {
        background-color: #397A3E;
    }

    .tab-content {
        display: none;
        padding: 20px;
    }

    .tab-content.active {
        display: block;
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-family: Arial, sans-serif;
        margin-top:10px;
    }

    thead th {
        background-color: #397A3E;
        color: #ffffff;
        text-align: left;
        padding: 10px;
        font-size: 16px;
    }

    tbody td {
        border: 1px solid #d1d1d1;
        padding: 10px;
        font-size: 14px;
    }

    tbody td input[type="checkbox"] {
        width: 20px;
        height: 20px;
        margin-right: 10px;
    }

    tbody td input[type="text"] {
        width: 100%;
        padding: 8px;
        font-size: 14px;
        border: 1px solid #ccc;
    }

    tbody tr:nth-child(odd) {
        background-color: #0686522e;
    }

    tbody tr:nth-child(even) {
        background-color: #fff;
    }

    tbody td:first-child {
        width: 60%;
    }

    tbody td:nth-child(2),
    tbody td:nth-child(3),
    tbody td:nth-child(4) {
        width: 5%;
        text-align: center;
    }

    tbody td:last-child {
        width: 25%;
    }

    th, td {
        border: 1px solid #ccc;
    }

   

    .file-upload {
        margin-top: 20px;
    }
    .resp-tabs-list li {
        min-width: 776px !important;
    }
</style>
<article class="card">
    <div class="card-wrap">
        <div id="energy-tabs">
            <ul class="resp-tabs-list hor_1 clearfix">
                <li class="tab-btn tab-custom-id-1 resp-tab-active" onclick="openTab(event, 'OverviewTab')">Overview</li>
                <li class="tab-btn tab-custom-id-1"  <?=(!isset($data['is_upload_checked']) || $data['is_upload_checked']==0)?'disabled':''?> onclick="openTab(event, 'FileUploadTab')">File Upload</li>
            </ul>
            <div class="resp-tabs-container hor_1">
                <!-- Overview Tab Content -->
                <div id="OverviewTab" class="resp-tab-content hor_1 tab-content active">
                    <div class="panel panel-primary" style="border-color:#22a16d">
                        <div class="panel-body" style="padding:15px">
                                <?php echo form_open_multipart(BASE_ADMIN_URL_CUSTOM . 'certification/save', array('id' => 'saveform', 'name' => 'saveform')); ?>
                                <?=!empty($data['section_description']) ? html_entity_decode($data['section_description']) : ''?>
                                <?php 
                                if(isset($data['questions']) && count($data['questions'] > 0) && !empty($data['questions'])) {
                                ?>
                                    <table border="1" cellpadding="10" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Checklist Item</th>
                                                <th class="text-center">Y</th>
                                                <th class="text-center">N</th>
                                                <th class="text-center">n/a</th>
                                                <th>Comment/Explanation</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($data['questions'] as $question){ ?>
                                            <tr>
                                                <td><?=$question['section_question']?></td>
                                                <td><input type="radio" name="ans[<?=$question['id']?>][section_question_answer]" <?=(isset($question['section_question_answer']) && $question['section_question_answer']=='Y')?'checked':''?>  value="Y"/></td>
                                                <td><input type="radio" name="ans[<?=$question['id']?>][section_question_answer]" <?=(isset($question['section_question_answer']) && $question['section_question_answer']=='N')?'checked':''?> value="N"/></td>
                                                <td><input type="radio" name="ans[<?=$question['id']?>][section_question_answer]" <?=(isset($question['section_question_answer']) && $question['section_question_answer']=='NA')?'checked':''?>  value="NA"/></td>
                                                <td><input type="text" name="ans[<?=$question['id']?>][comments]" placeholder="Comment/Explanation" value="<?=(isset($question['section_question_comments']) && !empty($question['section_question_comments']))?$question['section_question_comments']:''?>"/></td>
                                                <input type="hidden" name="ans[<?=$question['id']?>][answer_id]" value="<?=(isset($question['answer_id']) && !empty($question['answer_id']))?$question['answer_id']:''?>">
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <button type="submit" name="mysubmit" value="<?php echo lang('btn-save'); ?>" class="btn btn-secondary btn-submit"><?php echo lang('btn-save'); ?></button>
                                <?php
                                }
                                else{
                                    echo '<p>No record found</p>';
                                }
                                ?>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- File Upload Tab Content -->
                <div id="FileUploadTab" class="resp-tab-content hor_1 tab-content" style="">
                    <div class="panel panel-primary" style="border-color:#007856 !important;">
                        <div class="panel-heading" style="background-image:linear-gradient(to bottom, #007856 0px, #007856 100%) !important;"><strong>Section Image</strong></div>
                        <div class="panel-body">
                            <?php if (isset($section_image) && $section_image != '') { ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/" . $section_image)) {
                                    ?>
                                        <img src='<?php echo site_url() . "assets/uploads/" . $section_image; ?> '>
                                    <?php } else { ?>
                                        <img class="siteImage" src='<?php echo site_url() . NOT_AVAILABLE_site_logo; ?> '>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="section_image" class="main-label" ><?php echo lang('section_image'); ?></label>
                                </div>
                                <div class="col-md-8">
                                    <div class="file-upload">
                                    <?php echo form_open_multipart(BASE_ADMIN_URL_CUSTOM . 'certification/upload', array('id' => 'saveform', 'name' => 'saveform')); ?>
                                    <input type="file" id="file" name="section_image" required><br><br>
                                    <input type="hidden" id="section_id" name="section_id" value="<?=$data['id']?>">
                                     
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" name="uploadBtn" value="<?php echo lang('btn-upload'); ?>" class="btn btn-secondary btn-submit"><?php echo lang('btn-upload'); ?></button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>
<script>
    function openTab(event, tabName) {
        var i, tabcontent, tabbtns;
        var upload_checked = '<?=$data['is_upload_checked']?>';
        if(upload_checked == 1)
        {
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
                tabcontent[i].classList.remove('active');
            }

            tabbtns = document.getElementsByClassName("tab-btn");
            for (i = 0; i < tabbtns.length; i++) {
                tabbtns[i].classList.remove("resp-tab-active");
            }

            document.getElementById(tabName).style.display = "block";
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add("resp-tab-active");
        }
    }

    document.getElementById("OverviewTab").style.display = "block";
</script>
