$(document).ready(function() {
    // Custom Dropdown
    $("select[data-type='custom-dropdown']").dropkick({
        mobile: true
    });

    $('#btnLogin').click(function(){
        //$('#login_form_inner').unbind('submit');
        // Validate Form
        var validator = $('#login_form_inner').data('bootstrapValidator');
        validator.validate();
        if (!validator.isValid()) {
            return false;   
        }

        var userName = $('#username').val();
        var passWord = $('#password').val();

        blockUI();
        //check in code igniter - if username and password exists in db
        $.ajax({
            type: 'POST',
            url:  base_url+"users/ajax_login",
            data: {'username':userName,'password':passWord,'Login':'Login','ajax_login':'yes'},
            dataType: "text",
            success: function (result) {
                var data = JSON.parse(result);
                if(data.status == 1)
                {
                    MediaWikiFormSubmit(data.redirect);    
                }else{
                   window.location.href = data.redirect; 
                }
            }
        });

        return false;

    });
});
function MediaWikiFormSubmit(redirect)
{
    var userName = $('#username').val();
    var passWord = $('#password').val();
    $.ajax({
        type: 'POST',
        url: base_url+"knowledgebase/index.php?title=Special:UserLogin&action=submitlogin",
        data: {'wpName':userName,'wpRemember':1},
        dataType: "text",
        complete : function(response) { 
            window.location.href = redirect;
        }
    });
    
    
}

