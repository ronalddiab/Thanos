<form action="api.php" method="post" name="form">
<input type="button" name="submit" value="Click" id="submit">
<input type="hidden" name="action" value="createaccount">
<input type="hidden" name="format" value="json">
<input type="hidden" name="name" value="harvey">
<input type="hidden" name="password" value="demo@123">
<input type="hidden" name="token" value="146e822d29a2237cc30dae274ca37880">

</form>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $("#submit").click(function(){
        $.ajax({
			url: '<?php echo SITE_BASE_URL; ?>'+'/knowledgebase/api.php',
			type: 'POST',
			dataType:"text",
			data: {action:'createaccount',format:'json',name:'workForce123'},
			error: function() {
			  $('#info').html('<p>An error has occurred</p>');
			},
			success: function(data) {
				var json = $.parseJSON(data);
				var token = json.createaccount['token'];
				if(token)
				{
					createAccountMediaWiki(token);
				}
			},
		});
    });
});
function createAccountMediaWiki(token)
{ 
	$.ajax({
			url: '<?php echo SITE_BASE_URL; ?>'+'/knowledgebase/api.php',
			type: 'POST',
			dataType:"text",
			data: {action:'createaccount',format:'json',name:'workForce123','password':'111111',token:token},
			error: function() {
			  $('#info').html('<p>An error has occurred</p>');
			},
			success: function(data) {
				console.log(data);
			},
		});
}
</script>