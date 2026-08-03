<?php
// Initial page num setup
$pagination_array = array();  // initialize pagination array

$stages = intval($num_links); // define number of links
// if page_number is not set then set default 1
if (!$page_number)
{
    $page_number = 1;
}

//$section_name = get_current_section($this);

//
//$section_name = get_section($this);
//
//if($section_name=="")
//{
//    $section_name = "front";
//}

// calculate total pages for pagination
$total_pages = ceil(intval($total_records) / $this->session->userdata[$section_name]['record_per_page']);
$pagination_array["PREVIOUS_PAGE_PARAM"] = '';
$pagination_array["NEXT_PAGE_PARAM"] = '';

$pagination_array["PREVIOUS_PAGE_URL"] = '';
$pagination_array["PREVIOUS_PAGE_ONCLICK"] = '';

$pagination_array["NEXT_PAGE_URL"] = '';
$pagination_array["NEXT_PAGE_ONCLICK"] = '';

$pagination_array["FIRST_PAGE_LINK"] = array('label' => '', 'link' => '', 'onclick' => '');
$pagination_array["LAST_PAGE_LINK"] = array('label' => '', 'link' => '', 'onclick' => '');

$pagination_array["PREV_DOTTED_LINK"] = 0;
$pagination_array["NEXT_DOTTED_LINK"] = 0;

$pagination_array["PAGING_LINKS"] = array();

$pagination_array["TOTAL_PAGES"] = $total_pages;
$pagination_array["TOTAL_RECORDS"] = $total_records;

$pagination_array["PAGING_PER_PAGE_RESULTS"] = $this->session->userdata[$section_name]['record_per_page'];

if (isset($index))
{
    $pagination_array["PAGING_INDEX"] = $index;
}
else
{
    $pagination_array["PAGING_INDEX"] = 1;
}

// this block for ajax pagination method to replace [PAGE_NUMBER] and [PER_PAGE_RESULT]
if (isset($isAjaxRequest) && $isAjaxRequest == 1)
{
    $target_page = $base_url;

    if (strpos($target_page, "[PAGE_NUMBER]") === false)
    {
        if (isset($params) && $params)
        {
            $params = $params . '&page_number=[PAGE_NUMBER]';
        }
        else
        {
            $params = 'page_number=[PAGE_NUMBER]';
        }
    }

    $params .= '&per_page_result=[PER_PAGE_RESULT]';

    $functionWithParams = "ajaxLink('" . $target_page . "','" . $element . "','" . $params . "');";

    $target_page = $functionWithParams;

    $paging_redirect_url = str_replace("[PAGE_NUMBER]", 1, $target_page);

    $target_page = str_replace("[PER_PAGE_RESULT]", $this->session->userdata[$section_name]['record_per_page'], $target_page);
} // this block for post method pagination to create redirect url for record per page drop down
elseif (isset($isFunctionRequest) && $functionWithParams)
{
    $target_page = $functionWithParams;

    $paging_redirect_url = str_replace("[PAGE_NUMBER]", 1, $target_page);

    $target_page = str_replace("[PER_PAGE_RESULT]", $this->session->userdata[$section_name]['record_per_page'], $target_page);
}  // this block for get method pagination to create redirect url for record per page drop down
else
{
    $target_page = $base_url;
    $target_page = str_replace("[PER_PAGE_RESULT]", $this->session->userdata[$section_name]['record_per_page'], $target_page);
    $paging_redirect_url = str_replace("[PAGE_NUMBER]", "", $target_page);
}

// start pagination logic
if ($total_pages > 1)
{
    // Previous
    if ($page_number > 1)
    {
        $temp_url = str_replace("[PAGE_NUMBER]", ($page_number - 1), $target_page);
        $pagination_array["PREVIOUS_PAGE_PARAM"] = $temp_url;
    }
    // FOR FIRST LINK
    if ($page_number == 1)
    {
        $pagination_array["FIRST_PAGE_LINK"]["label"] = 1;
        $pagination_array["FIRST_PAGE_LINK"]["link"] = '';
        $pagination_array["FIRST_PAGE_LINK"]["onclick"] = '';
    }
    else
    {
        if (2 < ($page_number - $stages))
        {
            $temp_url = str_replace("[PAGE_NUMBER]", 1, $target_page);

            $pagination_array["FIRST_PAGE_LINK"]["label"] = 'First';

            if ((isset($isFunctionRequest) || isset($isAjaxRequest) ) && $functionWithParams)
            {
                $pagination_array["FIRST_PAGE_LINK"]["link"] = 'javascript:void(0);';
                $pagination_array["FIRST_PAGE_LINK"]["onclick"] = $temp_url;
            }
            else
            {
                $pagination_array["FIRST_PAGE_LINK"]["link"] = $temp_url;
                $pagination_array["FIRST_PAGE_LINK"]["onclick"] = '';
            }
        }
        else
        {
            $temp_url = str_replace("[PAGE_NUMBER]", 1, $target_page);
            $pagination_array["FIRST_PAGE_LINK"]["label"] = 1;

            if ((isset($isFunctionRequest) || isset($isAjaxRequest) ) && $functionWithParams)
            {
                $pagination_array["FIRST_PAGE_LINK"]["link"] = 'javascript:void(0);';
                $pagination_array["FIRST_PAGE_LINK"]["onclick"] = $temp_url;
            }
            else
            {
                $pagination_array["FIRST_PAGE_LINK"]["link"] = $temp_url;
                $pagination_array["FIRST_PAGE_LINK"]["onclick"] = '';
            }
        }
    }
    // FOR ....    
    if (2 < ($page_number - $stages))
    {
        $pagination_array["PREV_DOTTED_LINK"] = 1;
    }

    for ($counter = 2; $counter < $total_pages; $counter++)
    {
        if ($counter >= ($page_number - $stages) && $counter <= ($page_number + $stages))
        {
            if ($counter == $page_number)
            {
                $pagination_array["PAGING_LINKS"][$counter] = array("link" => "", "onclick" => "");
            }
            else
            {
                $temp_url = str_replace("[PAGE_NUMBER]", $counter, $target_page);

                if ((isset($isFunctionRequest) || isset($isAjaxRequest) ) && $functionWithParams)
                {
                    $pagination_array["PAGING_LINKS"][$counter] = array("link" => "javascript:void(0);", "onclick" => $temp_url);
                }
                else
                {
                    $pagination_array["PAGING_LINKS"][$counter] = array("link" => $temp_url, "onclick" => "");
                }
            }
        }
    }

    if ($total_pages > ($page_number + $stages + 1))
    {
        $pagination_array["NEXT_DOTTED_LINK"] = 1;
    }

    if ($page_number == $total_pages)
    {
        $pagination_array["LAST_PAGE_LINK"]["label"] = $total_pages;
        $pagination_array["LAST_PAGE_LINK"]["link"] = '';
        $pagination_array["LAST_PAGE_LINK"]["onclick"] = '';
    }
    else
    {
        if ($total_pages > ($page_number + $stages + 1))
        {
            $temp_url = str_replace("[PAGE_NUMBER]", $total_pages, $target_page);

            $pagination_array["LAST_PAGE_LINK"]["label"] = 'Last';

            if ((isset($isFunctionRequest) || isset($isAjaxRequest) ) && $functionWithParams)
            {
                $pagination_array["LAST_PAGE_LINK"]["link"] = 'javascript:void(0);';
                $pagination_array["LAST_PAGE_LINK"]["onclick"] = $temp_url;
            }
            else
            {
                $pagination_array["LAST_PAGE_LINK"]["link"] = $temp_url;
                $pagination_array["LAST_PAGE_LINK"]["onclick"] = '';
            }
        }
        else
        {
            $temp_url = str_replace("[PAGE_NUMBER]", $total_pages, $target_page);

            $pagination_array["LAST_PAGE_LINK"]["label"] = $total_pages;

            if ((isset($isFunctionRequest) || isset($isAjaxRequest) ) && $functionWithParams)
            {
                $pagination_array["LAST_PAGE_LINK"]["link"] = 'javascript:void(0);';
                $pagination_array["LAST_PAGE_LINK"]["onclick"] = $temp_url;
            }
            else
            {
                $pagination_array["LAST_PAGE_LINK"]["link"] = $temp_url;
                $pagination_array["LAST_PAGE_LINK"]["onclick"] = '';
            }
        }
    }

    // Next
    if ($page_number < $total_pages)
    {
        $temp_url = str_replace("[PAGE_NUMBER]", ($page_number + 1), $target_page);
        $pagination_array["NEXT_PAGE_PARAM"] = $temp_url;
    }




    if ((isset($isFunctionRequest) || isset($isAjaxRequest) ) && $functionWithParams)
    {
        if ($pagination_array["PREVIOUS_PAGE_PARAM"])
        {
            $pagination_array["PREVIOUS_PAGE_URL"] = 'javascript:void(0);';
            $pagination_array["PREVIOUS_PAGE_ONCLICK"] = $pagination_array["PREVIOUS_PAGE_PARAM"];
        }

        if ($pagination_array["NEXT_PAGE_PARAM"])
        {
            $pagination_array["NEXT_PAGE_URL"] = 'javascript:void(0);';
            $pagination_array["NEXT_PAGE_ONCLICK"] = $pagination_array["NEXT_PAGE_PARAM"];
        }
    }
    else
    {
        if ($pagination_array["PREVIOUS_PAGE_PARAM"])
        {
            $pagination_array["PREVIOUS_PAGE_URL"] = $pagination_array["PREVIOUS_PAGE_PARAM"];
            $pagination_array["PREVIOUS_PAGE_ONCLICK"] = '';
        }

        if ($pagination_array["NEXT_PAGE_PARAM"])
        {
            $pagination_array["NEXT_PAGE_URL"] = $pagination_array["NEXT_PAGE_PARAM"];
            $pagination_array["NEXT_PAGE_ONCLICK"] = '';
        }
    }
}
if ($page_number > 1 && $page_number <= $total_pages)
{
    $pagination_array["START"] = (($page_number - 1) * $this->session->userdata[$section_name]['record_per_page']) + 1;
}
else
{
    // error - show first set of results
    $pagination_array["START"] = 1;
}
if ($pagination_array["START"] == 1)
{
    if ($total_records < $this->session->userdata[$section_name]['record_per_page'])
    {
        $pagination_array["END"] = $total_records;
    }
    else
    {
        $pagination_array["END"] = $this->session->userdata[$section_name]['record_per_page'];
    }
}
else
{
    if ($page_number == $total_pages)
        $pagination_array["END"] = $total_records;
    else
        $pagination_array["END"] = $page_number * $this->session->userdata[$section_name]['record_per_page'];
}
// end pagination logic
// set layout for pagination

$this->theme->paging_layout($pagination_array);
?>
<script type="text/javascript">

    
<?php
if ((isset($isFunctionRequest) || isset($isAjaxRequest) ) && $functionWithParams)
{
    ?>
                $("#paging_per_page_records_<?php echo $pagination_array['PAGING_INDEX']; ?>").change(function ()
                {

                    var redirect_function= "<?php echo str_replace("'", "\'", $paging_redirect_url); ?>";

                    redirect_function = redirect_function.replace("[PER_PAGE_RESULT]",encodeURIComponent($(this).val()));

                    eval(redirect_function);
                });
    <?php
}
else
{
    ?>
                $("#paging_per_page_records_<?php echo $pagination_array['PAGING_INDEX']; ?>").change(function (){

                    <?php
                    if (strpos($paging_redirect_url, "?") !== false)
                    {
                        $redirect_url = $paging_redirect_url . "&";
                    }
                    else
                    {
                        $redirect_url = $paging_redirect_url . "?";
                    }
                    ?>

                    document.location = '<?php echo $redirect_url; ?>'+'per_page_result=' + encodeURIComponent($(this).val());
                });
<?php }
?>
</script>