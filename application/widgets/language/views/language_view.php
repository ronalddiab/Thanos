<?php
$attributes = array('class' => '', 'id' => 'change_language', 'name' => 'change_language', 'method' => 'post');
echo form_open('', $attributes);

$options = array();
for ($i = 0; $i < count($languages); $i++) {
    $options[$languages[$i]['l']['id']] = $languages[$i]['l']['language_name'];
}
$js = 'id="select_language" onChange="this.form.submit()"';

echo form_dropdown('select_language', $options, (isset($this->session->userdata[$section_name]['site_lang_id'])) ? $this->session->userdata[$section_name]['site_lang_id'] : '', $js);
//onchange="this.form.submit()"
echo form_close();
