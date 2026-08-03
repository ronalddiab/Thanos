<pre>
<?php
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,SITE_BASE_URL."/knowledgebase/api.php");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch,CURLOPT_COOKIE,$_SERVER['HTTP_COOKIE']);
	curl_setopt($ch, CURLOPT_POSTFIELDS, 
					http_build_query(array('action' => 'createaccount',
										   'format' => 'json',
										   'name' => 'john456',
										   'password' => '111111',
										   'token' => '')
										  )
									);

	// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec ($ch);
	curl_close ($ch);
	$result = json_decode($response);
	print_r($result);
	if($result->createaccount->token)
	{
		$token = $result->createaccount->token;			
		
		$ch1 = curl_init();
		curl_setopt($ch1, CURLOPT_URL,SITE_BASE_URL."/api.php");
		curl_setopt($ch1, CURLOPT_POST, 1);
		curl_setopt($ch1,CURLOPT_COOKIE,$_SERVER['HTTP_COOKIE']);
		curl_setopt($ch1, CURLOPT_POSTFIELDS, 
						http_build_query(array('action' => 'createaccount',
											   'format' => 'json',
											   'name' => 'john456',
											   'password' => '111111',
											   'token' => $token)
											  )
										);

		// receive server response ...
		curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
		$finalResponse = curl_exec ($ch1);
		curl_close ($ch1);
		$finalResult = json_decode($finalResponse);
		echo 'Second response : ';
		print_r($finalResult);exit;
		
	}
?>
</pre>