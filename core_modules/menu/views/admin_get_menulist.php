<?php
//for creating menulist dropdown
if(isset($menulist))
{
    $dropdown = array('0' => lang('menu-root'));
    foreach ($menulist as &$val)
    {
        $dropdown[$val['id']] = $val['title'];
    }
    echo form_dropdown('parent_id', $dropdown, '', 'id=parent_id');
}
?>