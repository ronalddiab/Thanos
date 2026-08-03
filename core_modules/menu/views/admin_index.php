<div class="main-container" id="menu-grid">
    <div class="grid-data grid-data-table" >
        <?php
        if (isset($lang_id)) {
            ?>
            <div class="add-new">
                <span style="float: left;"><?php echo add_image(array('active.png')) . lang('menu-active') . add_image(array('inactive.png')) . " " . lang('menu-inactive'); ?></span>
                <a onclick="openlink('add')" style="text-align:center;width:100%;" title="Add Menu" href="#">Add Menu</a>
            </div>
<?php } ?>
        <div>
            <div class="tab-nav">
                <ul class="tab-headings clearfix">
        <?php
        if (!empty($languages)) {
            ?>
                        <?php
                        foreach ($languages as $key => $langval):
                            //take alias from an array
                            $alias = end(array_keys($langval));
                            ?>
                            <li class="<?php echo($lang_id == $langval[$alias]['id']) ? 'selected' : ''; ?>"><a href="javascript:;" id="<?php echo $langval[$alias]['id']; ?>" rel="#<?php echo $langval[$alias]['language_code']; ?>" title="<?php echo strtolower($langval[$alias]['language_name']); ?>"><?php echo $langval[$alias]['language_name']; ?></a></li>
                        <?php endforeach; ?>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="menu-content-box">
            <!-- load your ajax content here -->
<?php
if (!empty($languages)) {
    ?>
    <?php
    foreach ($languages as $key => $langval):
        //take alias from an array
        $alias = end(array_keys($langval));
        ?>
                    <div id="<?php echo $langval[$alias]['language_code']; ?>" class="profile-content grid-data" style="display:<?php echo($lang_id == $langval[$alias]['id']) ? 'block' : ''; ?>">
                    <?php
                    if (!empty($menu)) {
                        foreach ($menu as $menuname => $menuitem):
                            ?>
                                <fieldset>
                                    <legend><h3><?php echo $menuname; ?></h3></legend>
                                    <br/>
                                    <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
                                        <tbody bgcolor="#fff">
                                        <th style="width:35%"><?php echo lang("menu-title"); ?></th>
                                        <th style="width:40%"><?php echo lang("menu-slug"); ?></th>
                                        <th style="width:5%"><?php echo lang("menu-status"); ?></th>
                                        <th style="width:10%"><?php echo lang("menu-action"); ?></th>
                <?php
                $i = 1;
                foreach ($menuitem as $key => $val):
                    if ($i % 2 != 0) {
                        $class = "odd-row";
                    } else {
                        $class = "even-row";
                    }
                    $id = $val['id'];
                    $statuslink = ($val['status'] == 1) ? add_image(array('active.png'), '', '', array('title' => 'active', 'alt' => "active")) : add_image(array('inactive.png'), '', '', array('title' => 'inactive', 'alt' => "inactive"));
                    $language_name = strtolower($val['language_code']);
                    ?>
                                            <tr class="<?php echo $class; ?>" id="row-<?php echo $id; ?>">
                                                <td><?php echo $val['title']; ?></td>
                                                <td><?php $menulink = ($val['link'] == '/') ? '' : $val['link'];
                        echo site_url() . $menulink; ?>
                                                </td>
                                                <td><?php echo $statuslink; ?></td>
                                                <td>
                                            <?php
                                            $editlink = "<a href='" . base_url() . get_current_section($this) . "/menu/action/edit/" . $language_name . "/" . $id . "' title='Edit'>" . add_image(array('edit.png')) . "</a>";
                                            $deletelink = "<a href='javascript:;' title='Delete' onclick='deletemenu($id)'>" . add_image(array('delete.png')) . "</a>";
                                            echo "<div class='action'><div class='edit'>" . $editlink . "</div><div class='delete'>" . $deletelink . "</div></div>";
                                            ?>
                                                </td>
                                            </tr>
                    <?php
                    $i++;
                endforeach;
                ?>
                                        </tbody>
                                    </table>
                                </fieldset>
                                <br/>
                                        <?php
                                    endforeach;
                                } else {
                                    ?>
                                    <?php echo lang('menu-message-norec'); ?>
        <?php } ?>
                    </div>
    <?php endforeach; ?>
                <?php } ?>
            <!-- End load -->
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            $(".tab-headings li a").on('click',function()
            {
                var thisId = $(this).attr("rel");
                var langid = $(this).attr('id');
                $(".tab-headings li").removeClass("selected");
                $(this).parent('li').addClass("selected");
                $(".profile-content").hide();
                $(thisId).show();
                loadmenulist(langid);
            });
        });

        function openlink(type){
            //alert($(".tab-headings li.selected a").attr('rel'));
            lang_name = $(".tab-headings li.selected a").attr('rel');
            lang_name = (lang_name != '') ? lang_name.replace("#", ""): '';
            location.href= "<?php echo base_url() . get_current_section($this->theme->ci()); ?>/menu/action/add/"+lang_name;
        }

        function deletemenu (id){
            res = confirm('<?php echo lang('confirm-menu-delete-msg'); ?>');
            lang_id = $(".tab-headings li.selected a").attr('id');
            if(res){
                //succflag = false;
                $.ajax({
                    type:'POST',
                    url:'<?php echo base_url() . get_current_section($this); ?>/menu/delete',
                    data:{<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>:'<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>',
                        id:id,
                        lang_id:lang_id
                    },
                    success: function(data) {

                        $("#messages").show();
                        $("#messages").html(data);
                        $("#menu"+id).remove();
                        loadmenulist(lang_id); //reload menu items
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                    //location.reload();
                }
            });

        }else{
            return false;
        }
    }

    function loadmenulist(lang_id){
        blockUI();
        $.ajax({
            type:'POST',
            url:'<?php echo base_url() . get_current_section($this->theme->ci()); ?>/menu/index',
            data:{<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>:'<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>',
                lang_id:lang_id
            },
            success: function(data) {
                $("#menu-grid").html('');
                if(data == ''){
                    $("#menu-grid").hide();
                }else{
                    $("#menu-grid").html(data);
                    $("#menu-grid").show();
                }
                unblockUI();
            }
        });

    }
    </script>
</div>

