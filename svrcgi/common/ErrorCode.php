<?php

define ('EC_OK', 0);
define('EC_INVALID_INPUT', 1000);
define('EC_GET_BIZID_ERROR', 1001);

define('EC_ACCESS_DB_ERROR', 2000);
define('EC_DIRTY_DB_DATA_ERROR', 2001);
define('EC_CONNECT_DB_ERROR', 2002);
define('EC_DB_OP_ERROR', 2003);


define('EC_NETWORK_OR_TIMEOUT_ERROR', 3000);

define('EC_SYSTEM_INTERNAL_ERROR', 4000);
define('EC_SYSTEM_INVALID_JSON_FORMAT', 4001);
define('EC_SYSTEM_INVALID_PARA', 4002);
define('EC_SYSTEM_FREQUECY',4003);


define('EC_UNKOWN_ERROR', 10000);
define('EC_SERVER_ERROR', 10001);



function genErrMsg($errCode, $errorMsg = "")
{
	$errMsg = array(
		EC_OK=>"return successfully!",
		EC_INVALID_INPUT=>"invalid param in json from client!",
		EC_CONNECT_DB_ERROR => "connect db failed",		
		EC_DB_OP_ERROR => "db operator error",
		EC_SYSTEM_INVALID_JSON_FORMAT => "http post body is empty or invalid json format in post body from client!",
		EC_SYSTEM_INVALID_PARA => "request para error",
		EC_SYSTEM_FREQUECY => "frequency control"
		
	);
	
	if($errorMsg == "")
	{
		return $errMsg[$errCode];
	}
	
	return $errMsg[$errCode] . " | " . $errorMsg;
}

?>
