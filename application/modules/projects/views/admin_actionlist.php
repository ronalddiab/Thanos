<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>
<article id="ajax_table" class="card">
    <div class="article-header"><strong><?php echo $project['project_name']; ?></strong></div>
    <div class="card-wrap">
        <?php
            echo isset($project['project_description'])?$project['project_description']:'';
        ?>
        <?php /*if(!empty($project['todo_image']) && file_exists(BASE_PATH_CUSTOM."/assets/uploads/".$project['todo_image'])){ ?>
        <br/>
        <img width="20%" src="<?php echo site_url().'assets/uploads/'.$project['todo_image']; ?>">
        <?php }*/ ?>
        <?php if(!empty($project_todos)){ ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-center"><strong><?php echo lang('actions'); ?></strong></th>
                        <?php if($role_id!=1){ ?>
                            <th class="text-center"><?php echo lang('action-plan'); ?></th>
                        <?php } ?>
                        <th class="text-center digits-col"><strong><?php echo 'Color'; ?></strong></th>
                    </tr>
                </thead>
                <tbody>
                    <?php  foreach ($project_todos as $todo) { ?>
                    <tr>
                        <td class="col-sm-8">
                            <div class="col-sm-12 cls-data-less">
                                <div><strong><?php echo $todo['todo_name']; ?></strong></div>                                
                                <div><?php echo isset($todo['todo_value']) && strlen($todo['todo_value'])>150?substr($todo['todo_value'],0,150)." ... <a href='javascript:void(0);' type='button' class='btn btn-primary cls-more pull-right'>More</a>":$todo['todo_value']; ?></div>
                            </div>
                            <div class="col-sm-12 cls-data-more" style="display: none;">
                                <div><strong><?php echo $todo['todo_name']; ?></strong></div>
                                <div><?php echo $todo['todo_value']; ?></div>
                                <?php if(!empty($todo['todo_image']) && file_exists(BASE_PATH_CUSTOM."/assets/uploads/".$todo['todo_image'])){ ?>
                                <br/>
                                <img src="<?php echo site_url().'assets/uploads/'.$todo['todo_image']; ?>">
                                <?php } ?>
                                <br/><br/>
                                <a href='javascript:void(0);' type='button' class='btn btn-primary cls-less'>Less</a>
                            </div>
                        </td>
                        <?php if($role_id!=1){ ?>
                        <td class="col-sm-2 text-center">
                            <?php if(empty($todo['actionplan'])){ ?>
                                <a href="javascript:void(0);" data-id="<?php echo $todo['id']; ?>" type="button" class="btn btn-primary add_to_action_plan"><?php echo lang('add-to-action-plan'); ?></a>
                            <?php }else{ ?>
                                <a href="<?php echo site_url().BASE_ADMIN_URL_CUSTOM.'projects/actionplans';?>" type="button" class="btn btn-warning"><?php echo lang('view-to-action-plan'); ?></a>
                            <?php }?>
                        </td>
                        <?php } ?>
                        <td class="col-sm-2"><input type="text" name="target_date" disabled="disabled" class="input-control" style="background-color:<?php echo isset($todo['todo_color']) && $todo['todo_color'] != ''?$todo['todo_color']:'';?>"></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php }else{
            ?>
            <div class="table-responsive">                  
                <table class="table table-striped" >
                    <tr>
                        <td><?php echo lang('no-records') ?></td>
                    </tr>
                </table>
            </div>
            <?php
        } ?>
    </div>
</article>
<script type="text/javascript">
    $(document).ready(function(){
        $(".add_to_action_plan").click(function(){
            blockUI();
            $todo_id = $(this).attr('data-id');
            $.ajax({
                method: "POST",
                url: "<?php echo site_url().BASE_ADMIN_URL_CUSTOM.'projects/addactionplan'; ?>",
                data: {'todo_id':$todo_id}
            })
            .done(function( data ) {
                /*$("#messages").show();
                $("#messages").html(data);*/
                unblockUI();
                location.href='<?php echo site_url().BASE_ADMIN_URL_CUSTOM."projects/actionplans" ?>';
            });
        });
        
        $(".cls-more").click(function() {        
            $(".cls-data-less",$(this).closest("td")).hide();
            $(".cls-data-more",$(this).closest("td")).show();
        });
        
        $(".cls-less").click(function() {        
            $(".cls-data-less",$(this).closest("td")).show();
            $(".cls-data-more",$(this).closest("td")).hide();
        });
    });
</script>