<?php
if (!empty($pages))
{
    foreach ($pages as &$val)
    {
        $dropdown[$val['u']['id']] = $val['u']['title'];
    }
    echo form_dropdown('related_id', $dropdown, '', 'id=related_id onChange=change_related(this.value);');
}
?>