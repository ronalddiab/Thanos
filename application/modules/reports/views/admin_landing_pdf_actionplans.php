<?php if (!empty($projects_categories)) { ?>

    <div style="float:left;">

        <h4>Efficiency Measures Actions</h4>

    </div>

    <div class="table-responsive category-table-block actionplan-table">

        <table border="1" width="100%" cellpadding="5" cellspacing="0">

            <thead>

                <tr>

                    <th style="background-color:#d8e1f2;"><strong><?php echo 'Category'; ?></strong></th>                            

                    <th style="background-color:#d8e1f2;"><strong><?php echo 'Actions'; ?></strong></th>

                    <th style="background-color:#d8e1f2;"><strong><?php echo 'Target Date'; ?></strong></th>

                    <th style="background-color:#d8e1f2;"><strong><?php echo 'Completed Date'; ?></strong></th>

                    <th style="background-color:#d8e1f2;"><strong><?php echo 'Status'; ?></strong></th>

                    <th style="background-color:#d8e1f2;"><strong><?php echo 'kWh Savings'; ?></strong></th>

                    <th style="background-color:#d8e1f2;"><strong><?php echo 'Cost Savings'; ?></strong></th>

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

                                    <td>

                                        <strong><?php echo $project['pc']['name']; ?></strong>

                                    </td>

                                    <td>

                                        <?php echo $todo['todo_name']; ?>

                                    </td>

                                    <?php $targetdate = (!empty(strtotime($todo['target_date'])) && $todo['target_date'] != '0000-00-00 00:00:00') ? date('m/d/Y', strtotime($todo['target_date'])) : ''; ?>

                                    <td>

                                        <?php echo $targetdate; ?>

                                    </td>

                                    <?php $completeddate = (!empty(strtotime($todo['completed_date'])) && $todo['completed_date'] != '0000-00-00 00:00:00') ? date('m/d/Y', strtotime($todo['completed_date'])) : ''; ?>

                                    <td>

                                        <?php echo $completeddate; ?>

                                    </td>

                                    <td >

                                        <?php

                                        switch ($todo['astatus']) {

                                            case '1':

                                                echo lang('awaiting-approval');

                                                break;



                                            case '2':

                                                echo lang('on-hold');

                                                break;



                                            case '3':

                                                echo lang('in-progress');

                                                break;



                                            case '4':

                                                echo lang('completed');

                                                break;



                                            default:

                                                # code...

                                                break;

                                        }

                                        ?>

                                    </td>

                                    <td>

                                        <?php echo $todo['kwh_savings']; ?>

                                    </td>

                                    <td>

                                        <?php echo $todo['cost_savings']; ?>

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

<?php

}
