function blockUI(options){
    var default_options = {
        message: null,
        css: { 
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#000', 
            '-webkit-border-radius': '10px', 
            '-moz-border-radius': '10px', 
            opacity: .5, 
            color: '#fff' 
        }
    };
    if(options ==  'undefined'){
        options = default_options;
    }else if(options == 'removeError'){
        $('#error_msg').fadeOut(1000); //hide error message it shown up while search
        options = default_options;
    }
    $.blockUI(options); 
}

function unblockUI(options){
    defaultOptions = {};
    if(options == 'undefined'){
        options = defaultOptions;
        4
    }
    setTimeout(function() { 
        $.unblockUI(options); 
    }, 1000); 
}

function pagingFunction(form_name,page_number,per_page_result,page_number_element)
{
    
    if(page_number_element != undefined)
        document.getElementById(page_number_element).value = encodeURIComponent(page_number);
    else if(document.getElementById("page_number") != null)
        document.getElementById("page_number").value = encodeURIComponent(page_number);
    
    
    if(per_page_result!="")
        document.getElementById("per_page_result").value = encodeURIComponent(per_page_result);
    
    document.forms[form_name].submit();   
}
function ajaxLink(path,elm,params) 
{
    blockUI();
    $.ajax({
        type: "POST",
        url: path,
        data: params,
        success: function(list){
                
            $('#'+elm).html(list);
            //            if(elm != undefined)
            //            {
            //                $("#"+elm).show();
            //                $("#"+elm).html(list);
            //                try {
            //                    if(scrollFlag == 0)
            //                    {
            //                        $("html,body").animate({
            //                            scrollTop:0
            //                        },'slow');
            //                        window.parent.$("html,body").animate({
            //                            scrollTop:0
            //                        },'slow');
            //                    }else
            //                    {
            //                        scrollFlag = 0;
            //                    }
            //                }catch(ee){}
            //            }
            unblockUI();
        }
    });
 
}
//remove dynamically populate error
function attach_error_event(){
    $('div.formError').bind('click',function(){
        $(this).fadeOut(1000, removeError); 
    });
}    
function removeError() 
{
    jQuery(this).remove();
}