<?php
//for creating pages dropdown
if(isset($pages))
{
    $dropdown = array('0' => lang('menu-select'));
    foreach ($pages as &$val)
    {
        $dropdown[$val['u']['id']] = $val['u']['value'];
    }
    echo form_dropdown('modulepages', $dropdown, '', 'id=modulepages onChange=change_subpages(this.value); class=validate[required]');
}
?>