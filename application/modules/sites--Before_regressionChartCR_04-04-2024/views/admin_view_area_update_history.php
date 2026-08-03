<article class="card">
    <div class="article-header">
        <?php echo lang('area_update_history').' for site <b>'.$site_detail['site_location_name'].'</b>' ; ?>
    </div>
    <div class="card-wrap">
        <?php if (!empty($site_area_update_history)) { 
            $unit = $site_area_update_history['unit'];
            unset($site_area_update_history['unit']);
            foreach ($site_area_update_history as $key => $value) { 
                $i = 1;
                if(!empty($value)) { ?>
                 <h3><?php echo $key.$unit; ?></h3>
                    <div class="table-responsive">          		
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="digits-col"><?php echo lang('no') ?></th>
                                    <th><?php echo 'Old Value'; ?></th>
                                    <th><?php echo 'Updated Value'; ?></th>
                                    <th><?php echo 'Revised By'; ?></th>
                                    <th><?php echo 'Revised Date'; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($value as $site) { ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $site['sa']['area_old_value']; ?></td>
                                        <td><?php echo $site['sa']['area_update_value']; ?></td>
                                        <td><?php echo $site['u']['firstname']; ?></td>
                                        <td><?php echo $site['sa']['area_update_date']; ?></td>
                                    </tr>
                                    <?php $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
            <?php 
                } 
            } ?>
        <?php } else { ?>
            <div class="table-responsive">                  
                <table class="table table-striped" >
                    <tr>
                        <td><?php echo lang('no-records') ?></td>
                    </tr>
                </table>
            </div>
        <?php } ?>
    </div>
</article>