<?php

if (!defined('BASEPATH'))

    exit('No direct script access allowed');

?>



<article id="ajax_table" class="card actionplans-container">

    <div class="article-header"><strong><?php echo lang('actionplans'); ?></strong></div>

    <div class="card-wrap scrollable-project"> 
         
        <div class="row site-controls-outer projects-list-container1">

            <div class="col-sm-12">

                <div class="form-group col-md-3 col-sm-4 col-xs-12">

                    <div class="form-dropdown">

                        <select name="filter_site_id" data-type="custom-dropdown" id="filter_site_id" onchange="javascript:location.href = this.value;">

                            <?php

                            foreach ($sites as $site) {

                                ?>

                                <option <?php echo ($site['s']['id'] == $site_id) ? 'selected="selected"' : ''; ?> value="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/viewactionplans/' . $site['s']['id']; ?>"><?php echo $site['s']['site_location_name']; ?></option>

                                <?php

                            }

                            ?>

                        </select>

                    </div>

                </div>

            </div>

        </div>



        <?php if (!empty($projects_categories) && $is_actionplans) { ?>

            <div class="table-responsive category-table-block actionplan-table">

                <table class="table">

                    <thead>

                        <tr>

                            <th class="text-center action-category-col"><strong><?php echo lang('category'); ?></strong></th>                            

                            <th class="text-center"><strong><?php echo lang('actions'); ?></strong></th>

                            <th class="text-center digits-col"><strong><?php echo 'Code'; ?></strong></th>

                            <th class="text-center "><strong><?php echo lang('target-date'); ?></strong></th>

                            <th class="text-center "><strong><?php echo lang('completed-date'); ?></strong></th>

                            <th class="text-center action-col"><strong><?php echo lang('status'); ?></strong></th>

							<th class="text-center "><strong><?php echo lang('kwh-savings'); ?></strong></th>

							<th class="text-center "><strong><?php echo lang('cost-savings'); ?></strong></th>							

                            <th class="text-center"><strong><?php echo lang('details'); ?></strong></th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php

                        foreach ($projects_categories as $ckey => $category) {

                            if ($category['pc']['projects_todo_count'] > 0) {

                                $crow = true;                                

                                foreach ($category['pc']['projects'] as $pkey => $project) {                                    

                                    foreach ($project['p']['project_todos'] as $key => $todo) {

                                        ?>

                                        <tr>

                                            <?php if ($crow) { ?>

                                                <td rowspan="<?php echo $category['pc']['projects_todo_count']; ?>" class="category-title-td col-sm-3"><img src="<?php echo site_url() . 'themes/default/images/category/' . $category['pc']['category_static_image']; ?>"><strong><?php echo $project['pc']['name']; ?></strong></td>

                                            <?php } ?>

                                            <td class="col-sm-3"><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/actionlist/' . $project['p']['id']; ?>"><?php echo $todo['todo_name']; ?></a></td>

                                            <td style="background-color:<?php echo isset($todo['todo_color']) && $todo['todo_color'] != '' ? $todo['todo_color'] : ''; ?>"></td>

                                            <?php $targetdate = (!empty(strtotime($todo['target_date'])) && $todo['target_date'] != '0000-00-00 00:00:00') ? date('m/d/Y', strtotime($todo['target_date'])) : ''; ?>

                                            <td class="col-sm-1"><input type="text" name="target_date" class="input-control action-date-col" disabled="disabled" data-todo-id="<?php echo $todo['id']; ?>" value="<?php echo $targetdate; ?>" /></td>

                                            <?php $completeddate = (!empty(strtotime($todo['completed_date'])) && $todo['completed_date'] != '0000-00-00 00:00:00') ? date('m/d/Y', strtotime($todo['completed_date'])) : ''; ?>

                                            <td class="col-sm-1"><input type="text" name="completed_date" class="input-control action-date-col" disabled="disabled"  data-todo-id="<?php echo $todo['id']; ?>" value="<?php echo $completeddate; ?>" /></td>

                                            <td class="col-sm-2">

                                                <div class="form-dropdown">

                                                    <select data-type="custom-dropdown" class="actionstatusselect" data-todo-id="<?php echo $todo['id']; ?>">

                                                        <option <?php echo (isset($todo['astatus']) && $todo['astatus'] == '0') ? 'selected="select"' : ''; ?> value="0"><?php echo 'Select Status'; ?></option>

                                                        <option <?php echo (isset($todo['astatus']) && $todo['astatus'] == '1') ? 'selected="select"' : ''; ?> value="1"><?php echo lang('awaiting-approval'); ?></option>

                                                        <option <?php echo (isset($todo['astatus']) && $todo['astatus'] == '2') ? 'selected="select"' : ''; ?> value="2"><?php echo lang('on-hold'); ?></option>

                                                        <option <?php echo (isset($todo['astatus']) && $todo['astatus'] == '3') ? 'selected="select"' : ''; ?> value="3"><?php echo lang('in-progress'); ?></option>

                                                        <option <?php echo (isset($todo['astatus']) && $todo['astatus'] == '4') ? 'selected="select"' : ''; ?> value="4"><?php echo lang('completed'); ?></option>

                                                    </select>

                                                </div>

                                            </td>

											<td class="col-sm-1"><input type="text" id="kwh_savings" name="kwh_savings" class="input-control action-date-col kwh_savings"  data-todo-id="<?php echo $todo['id']; ?>" value="<?php echo $todo['kwh_savings']; ?>" /></td>

											<td class="col-sm-1"><div style="padding-right: 14px;margin-top: -18px;"><i style="color:#555;float: left; position: relative; left: 4px; top: 31px;font-size:13px;" aria-hidden="true"><?php echo $todo['local_currency']; ?></i><input style="padding: 12px 3px;padding-left: 32px;" type="text" id="cost_savings" name="cost_savings" class="input-control action-date-col cost_savings" data-todo-id="<?php echo $todo['id']; ?>" value="<?php echo str_replace("$","", $todo['cost_savings']);?>" /></div></td>

                                            <td class="col-sm-2">

                                                <button data-popup-id="project_todo_comments_container_<?php echo $todo['id']; ?>" type="button" class="btn btn-primary togglecomments"><?php echo lang('show-comments-and-files'); ?></button>

                                                <div id="project_todo_comments_container_<?php echo $todo['id']; ?>" class="project_todo_comments_container" style="display:none;">

                                                    <?php if (empty($todo['project_todo_comments']) && empty($todo['project_todo_files'])) { ?>

                                                        <br/>

                                                    <?php } ?>

                                                    <?php if (!empty($todo['project_todo_comments'])) { ?>

                                                        <div class="article-header">

                                                            <strong><?php echo lang('comments'); ?></strong>

                                                        </div>

                                                        <?php foreach ($todo['project_todo_comments'] as $comments) {

                                                            ?>

                                                            <div class="well well-sm">

                                                                <div class="row comment-list">

                                                                    <div class="col-sm-12">

                                                                        <div class="col-sm-9">

                                                                            <div><strong><?php echo $comments['u']['firstname']; ?> <?php echo $comments['u']['lastname'] . ' ' . lang('said-on') . ' ' . date('d M Y', strtotime($comments['c']['created_on'])); ?></strong></div>

                                                                            <div class="comment_list_text"><?php echo $comments['c']['comments']; ?></div>

                                                                        </div>

                                                                        <div class="col-sm-3">

                                                                            <a title="Edit" href="javascript:void(0)" data-todo-id="<?php echo $todo['id']; ?>" data-comment-id="<?php echo $comments['c']['id']; ?>" class="commentedit"><img alt="Edit" src="images/edit-icon.png"></a>

                                                                            <a title="Delete" href="javascript:void(0)" data-todo-id="<?php echo $todo['id']; ?>" data-comment-id="<?php echo $comments['c']['id']; ?>" class="commentdelete"><img alt="Delete" src="images/delete-icon.png"></a>

                                                                        </div>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                        <?php }

                                                        ?>

                                                    <?php } ?>

                                                    <?php if (!empty($todo['project_todo_files'])) { ?>

                                                        <div class="article-header">

                                                            <strong><?php echo lang('files'); ?></strong>

                                                        </div>

                                                        <?php foreach ($todo['project_todo_files'] as $file) {//echo "<pre>";print_r($file);echo "</pre>";die;

                                                            ?>

                                                            <div class="well well-sm">

                                                                <div class="row files-list">

                                                                    <div class="col-sm-12">

                                                                        <div class="col-sm-9 for_use">

                                                                            <div class="files_list_text"><a href="<?php echo site_url() . 'assets/uploads/' . $file['f']['file']; ?>" target="blank"><?php echo trim($file['f']['file']); ?></a></div>

                                                                        </div>

                                                                        <div class="col-sm-3">

                                                                            <a title="Delete" href="javascript:void(0)" data-todo-id="<?php echo $file['f']['project_todo_id']; ?>" data-comment-id="<?php echo $file['f']['id']; ?>"  class="comment_filedelete"><img alt="Delete" src="images/delete-icon.png"></a>

                                                                        </div>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                        <?php }

                                                        ?>

                                                    <?php } ?>

                                                    <button type="button" data-todo-id="<?php echo $todo['id']; ?>" class="btn btn-default add_comment_btn"><?php echo lang('add-comment'); ?></button>

                                                    <button type="button" data-todo-id="<?php echo $todo['id']; ?>" class="btn btn-default add_file_btn"><?php echo lang('add-file'); ?></button>

                                                </div>

                                            </td>

                                        </tr>

                                        <?php

                                        $crow = false;

                                    }

                                }

                            }

                        }

                        ?>

                    </tbody>

                </table>

            </div>

        <?php } else {

            ?>

            <div class="table-responsive">                  

                <table class="table table-striped" >

                    <tr>

                        <td><?php echo lang('no-records') ?></td>

                    </tr>

                </table>

            </div>

        <?php }

        ?>

    </div>



    <div id="comment_form_container" class="card-wrap" style="display:none;">

        <form id="comment_form" class="site-info-form" method="post">

            <ul class="form-outer-block">

                <li>

                    <label for="commentbox" class="main-label">Comment <span class="asterisk">*</span></label>

                    <div class="row">

                        <div class="form-col-12">

                            <textarea name="commentbox" id="commentbox" class="input-control textarea-control"></textarea>

                        </div>

                    </div>

                </li>

            </ul>

            <input type="hidden" id="todo_id" name="todo_id" value="" />

            <input type="hidden" id="comment_id" name="comment_id" value="" />

            <input type="hidden" id="site_id" name="site_id" value="<?php echo $site_id; ?>" />

            <div class="form-btn-outer">

                <button id="submitcomment" type="submit" name="submit" value="1" class="btn btn-secondary btn-submit"><?php echo lang('btn-save'); ?></button>

                <a href="javascript:void(0)" class="btn btn-secondary reset-btn btn-text-cancel btn-submit closeform"><?php echo lang('btn-cancel'); ?></a>

            </div>

        </form>

    </div>



    <div id="file_form_container" class="card-wrap" style="display:none;">

        <form id="file_form" enctype="multipart/form-data" class="site-info-form" method="post" action="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/add_project_todo_file' ?>">

            <ul class="form-outer-block">

                <li>

                    <label for="commentbox" class="main-label">Comment <span class="asterisk">*</span></label>

                    <div class="row">

                        <div class="form-col-12">

                            <input id="file" type="file" name="file" value="" />

                        </div>

                    </div>

                </li>

            </ul>

            <input type="hidden" id="file_todo_id" name="todo_id" value="" />

            <input type="hidden" id="site_id" name="site_id" value="<?php echo $site_id; ?>" />

            <input type="hidden" name="redirect_url" value="<?php echo uri_string(); ?>" />

            <div class="form-btn-outer">

                <button id="submitfile" type="submit" name="submit" value="1" class="btn btn-secondary btn-submit"><?php echo lang('btn-save'); ?></button>

                <a href="javascript:void(0)" class="btn btn-secondary reset-btn btn-text-cancel btn-submit closeform"><?php echo lang('btn-cancel'); ?></a>

            </div>

        </form>

    </div>

</article>



<script type="text/javascript">

    $(document).ready(function() {

        $(".datepicker").datepicker({

            onSelect: function(selectedDate, obj) {

                var todo_id = $(this).attr('data-todo-id');

                var target_date = selectedDate;



                $.ajax({

                    method: "POST",

                    url: "<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/addactionplan'; ?>",

                    data: {todo_id: todo_id, target_date: target_date}

                })

                        .done(function(data) {

                            ajaxLink('<?php echo current_url(); ?>', 'ajax_table');

                            $("#messages").show();

                            $("#messages").html(data);

                        });

            }

        });



        $(".actionstatusselect").change(function() {

            var todo_id = $(this).attr('data-todo-id');

            var status = $(this).val();

            var siteId = <?php echo $site_id; ?>;



            $.ajax({

                method: "POST",

                url: "<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/addactionplan'; ?>",

                data: {todo_id: todo_id, status: status, siteId: siteId}

            })

                    .done(function(data) {

                        // console.log(data);

                        // console.log($('.actionstatusselect').attr('selectedIndex', '2');

                        ajaxLink('<?php echo current_url(); ?>', 'ajax_table');

                        $(".actionstatusselect").val(data.status);

                        $("#messages").show();

                        $("#messages").html(data);

                    });

        });



        $(".togglecomments").click(function(){

            var popupid = $(this).attr('data-popup-id');



            $.blockUI({

                css: {cursor: 'default','top':'20%'},

                blockMsgClass: 'formblockui',

                overlayCSS: {cursor: 'default', 'border-radius': '10px'},

                message: $('#'+popupid),

                onUnblock: function() {

                }

            });



            $('.blockOverlay').click($.unblockUI);

            

        });



        /*$(".togglecomments").click(function() {

            $that = $(this);

            $(this).siblings('.project_todo_comments_container').toggle(200, function() {

                if ($that.html() == '<?php echo lang("hide-comments-and-files"); ?>') {

                    $that.html('<?php echo lang("show-comments-and-files"); ?>');

                } else {

                    $that.html('<?php echo lang("hide-comments-and-files"); ?>');

                }

            });

        });*/



        $(".add_comment_btn").click(function() {

            $todo_id = $(this).attr('data-todo-id');

            $("#todo_id").val($todo_id);



            $.blockUI({

                css: {cursor: 'default'},

                blockMsgClass: 'formblockui',

                overlayCSS: {cursor: 'default', 'border-radius': '10px'},

                message: $('#comment_form_container'),

                onUnblock: function() {

                    $("#commentbox").val('');

                    $("#todo_id").val('');

                    $("#comment_id").val('');

                }

            });



            $('.blockOverlay').click($.unblockUI);

        });



        $(".commentedit").click(function() {

            $todo_id = $(this).attr('data-todo-id');

            $comment_id = $(this).attr('data-comment-id');

            $commenttext = $(this).parent().siblings().children('.comment_list_text').html();



            $("#commentbox").val($commenttext);

            $("#todo_id").val($todo_id);

            $("#comment_id").val($comment_id);



            $.blockUI({

                css: {cursor: 'default'},

                blockMsgClass: 'formblockui',

                overlayCSS: {cursor: 'default', 'border-radius': '10px'},

                message: $('#comment_form_container'),

                onUnblock: function() {

                    $("#commentbox").val('');

                    $("#todo_id").val('');

                    $("#comment_id").val('');

                }

            });



            $('.blockOverlay').click($.unblockUI);

        });



        $(".commentdelete").click(function() {

            $todo_id = $(this).attr('data-todo-id');

            $comment_id = $(this).attr('data-comment-id');

            $.ajax({

                method: "POST",

                url: "<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/delete_project_todo_comment'; ?>",

                data: {todo_id: $todo_id, comment_id: $comment_id,is_file: 0}

            })

                    .done(function(data) {

                        ajaxLink('<?php echo current_url(); ?>', 'ajax_table');

                        $("#messages").show();

                        $("#messages").html(data);

                    });

            return false;

        });







        $(".comment_filedelete").click(function() {

            var file_name = $.trim($(this).parent().parent().find('.for_use').text());

            var todo_id = $(this).attr('data-todo-id');

            var comment_id = $(this).attr('data-comment-id');

            $.ajax({

                method: "POST",

                url: "<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/delete_project_todo_comment'; ?>",

                data: {todo_id: todo_id, comment_id: comment_id,file_name: file_name,is_file: 1}

            })

                    .done(function(data) {

                        ajaxLink('<?php echo current_url(); ?>', 'ajax_table');

                        $("#messages").show();

                        $("#messages").html(data);

                    });

            return false;

        });



        $("#submitcomment").click(function() {

            $validate = $("#comment_form").validate({

                rules: {

                    commentbox: {

                        required: true

                    }

                }

            }).form();



            if ($validate) {

                $data = $('.blockUI #comment_form').serializeArray();

                $.ajax({

                    method: "POST",

                    url: "<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/add_project_todo_comment'; ?>",

                    data: $data

                })

                        .done(function(data) {

                            $.unblockUI({fadeOut: 200});

                            ajaxLink('<?php echo current_url(); ?>', 'ajax_table');

                            $("#messages").show();

                            $("#messages").html(data);

                            //$.unblockUI({fadeOut:200});

                        });

            }

            return false;

        });



        $(".add_file_btn").click(function() {

            $todo_id = $(this).attr('data-todo-id');

            $("#file_todo_id").val($todo_id);



            $.blockUI({

                css: {cursor: 'default'},

                blockMsgClass: 'formblockui',

                overlayCSS: {cursor: 'default', 'border-radius': '10px'},

                message: $('#file_form_container'),

                onUnblock: function() {

                    $("#file_form").trigger('reset');

                    $("#file_todo_id").val('');

                }

            });



            $('.blockOverlay').click($.unblockUI);

        });



        $("#file").change(function() {

            $validate = $("#file_form").validate({

                rules: {

                    file: {

                        required: true

                    }

                }

            }).form();

        });



        $("#submitfile").click(function() {

            $validate = $("#file_form").validate({

                rules: {

                    file: {

                        required: true

                    }

                }

            }).form();



            if ($validate) {

                return true;

            } else {

                return false;

            }

        });



        $('#action_category_id').change(function() {

            $category_id = $(this).val();

            $.ajax({

                method: "POST",

                url: "<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/ajax_action_projects'; ?>",

                data: {'category_id': $category_id},

                dataType: 'json',

            })

                    .done(function(json) {

                        var options = '<select id="action_project_id" name="action_project_id" data-type="custom-dropdown">';

                        options += '<option value=""><?php echo lang("select-action-project"); ?></option>';

                        $.each(json, function(key, data) {

                            options += '<option value="' + data.p.id + '">' + data.p.project_name + '</option>';

                        });

                        options += '</select>';



                        $("#action_project_id_div").html($(options));

                        $("select[data-type='custom-dropdown']").dropkick({

                            mobile: true

                        });

                    });

        });



        $('#action_project_id_div').on('change', '#action_project_id', function() {

            $project_id = $(this).val();

            $.ajax({

                method: "POST",

                url: "<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/ajax_action_todos'; ?>",

                data: {'project_id': $project_id},

                dataType: 'json',

            })

                    .done(function(json) {

                        var options = '<select id="action_todo_id" name="action_todo_id" data-type="custom-dropdown">';

                        options += '<option value=""><?php echo lang("select-action-todo"); ?></option>';

                        $.each(json, function(key, data) {

                            options += '<option value="' + data.id + '">' + data.todo_name + '</option>';

                        });

                        options += '</select>';



                        $("#action_todo_id_div").html($(options));

                        $("select[data-type='custom-dropdown']").dropkick({

                            mobile: true

                        });

                    });

        });



        $("#addselectedactionplan").click(function() {

            $.validator.setDefaults({

                ignore: []

            });



            $validate = $("#add-action-plan-form").validate({

                rules: {

                    action_category_id: {

                        required: true

                    },

                    action_project_id: {

                        required: true

                    },

                    action_todo_id: {

                        required: true

                    }

                }

            }).form();



            if ($validate) {

                // Add action plan here

                $todo_id = $('#action_todo_id').val();

                $.ajax({

                    method: "POST",

                    url: "<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/addactionplan'; ?>",

                    data: {'todo_id': $todo_id}

                })

                        .done(function(data) {

                            ajaxLink('<?php echo current_url(); ?>', 'ajax_table');

                            $("#messages").show();

                            $("#messages").html(data);

                        });

                return false;

            } else {

                return false;

            }

        });



        $(".closeform").click(function() {

            $.unblockUI({fadeOut: 200});

        });



        $("select[data-type='custom-dropdown']").dropkick({

            mobile: true

        });

    });



    function single_delete(id) {

        var val = [];

        val[0] = id;



        res = confirm('<?php echo lang('delete-alert') ?>');

        if (res) {

            $.ajax({

                type: 'POST',

                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/actionplans',

                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'delete', ids: val},

                success: function(data) {

                    //for managing same state while record delete

                    if ($('.rows') && $('.rows').length > 1) {

                        pageno = "&page_number=<?php echo $page_number; ?>";

                    } else {

                        pageno = "&page_number=<?php echo $page_number - 1; ?>";

                    }

                    ajaxLink('<?php echo current_url(); ?>', 'ajax_table', '<?php echo $querystr; ?>' + pageno);

                    $("#messages").show();

                    $("#messages").html(data);

                }

            });

        } else {

            return false;

        }

    }

	

	$(".kwh_savings").change(function(){

		

		var todo_id = $(this).attr('data-todo-id');

        var kwh_savings = $(this).val();

        $.ajax({

               type: "POST",

               url: "<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/addactionplan'; ?>",

               data: {todo_id: todo_id, kwh_savings: kwh_savings},

			   success: function(data) {

					$("#messages").hide();

					$("#messages").show();

					$("#messages").html(data);

			   }

        });

    });

	$(".cost_savings").change(function(){

		

		var todo_id = $(this).attr('data-todo-id');

        var cost_savings = '$'+$(this).val();				

        $.ajax({

               type: "POST",

               url: "<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/addactionplan'; ?>",

               data: {todo_id: todo_id, cost_savings: cost_savings},

			   success: function(data) {

					$("#messages").hide();

					$("#messages").show();

					$("#messages").html(data);

			   }

        });

    });

	$('input.kwh_savings').keyup(function(event) {



	  // skip for arrow keys

	  if(event.which >= 37 && event.which <= 40) return;



	  // format number

	  $(this).val(function(index, value) {

		return value

		.replace(/\D/g, "")

		.replace(/\B(?=(\d{3})+(?!\d))/g, ",")

		;

	  });

	});

	$('input.cost_savings').keyup(function(event) {



	  // skip for arrow keys

	  if(event.which >= 37 && event.which <= 40) return;



	  // format number

	  $(this).val(function(index, value) {

		return value

		.replace(/\D/g, "")

		.replace(/\B(?=(\d{3})+(?!\d))/g, ",")

		;

	  });

	});

	

</script>