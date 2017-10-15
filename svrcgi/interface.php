<?php

require_once dirname(__FILE__) . '/common/Common.php';
require_once dirname(__FILE__) . '/common/GlobalFunctions.php';

$request = file_get_contents("php://input");
interface_log(INFO, EC_OK, 'request:' . $request);


$mtime=explode(' ',microtime());

$start = $mtime[1] + $mtime[0];

Process($request,$interfaceName, $result, $retval);

interface_log(INFO, EC_OK, "response(".strlen($result)."):" . $result);

header("Content-Length:".strlen($result));
echo $result;


function Process($request,
						&$interfaceName,
						&$result,
						&$retval)
{	

	
	$data= json_decode($request, true);
	
	if (!$data)
	{
		interface_log(ERROR, EC_SYSTEM_INVALID_JSON_FORMAT, genErrMsg(EC_SYSTEM_INVALID_JSON_FORMAT));	
	    $result = genErrorResult(EC_SYSTEM_INVALID_JSON_FORMAT, genErrMsg(EC_SYSTEM_INVALID_JSON_FORMAT));
	    header("Content-Length:".strlen($result));
	    $retval = EC_SYSTEM_INVALID_JSON_FORMAT;	
	    return;
	}
	
	init_log($data);
	$interfaceName = parseInterfaceName($data);
	if (!$interfaceName) {
		interface_log(ERROR, EC_INVALID_INPUT, genErrMsg(EC_INVALID_INPUT));		
	    $result = genErrorResult(EC_INVALID_INPUT, genErrMsg(EC_INVALID_INPUT));
	    header("Content-Length:".strlen($result));
	    $retval = EC_INVALID_INPUT;	
		return;
	}
	
	
	$instance = instance($interfaceName);
	
	if(!$instance) {
		$errorMsg = "invalid interface name:" . ParaStrFilter($interfaceName)  ;
		interface_log(ERROR, EC_INVALID_INPUT, $errorMsg);
	    $result = genErrorResult(EC_INVALID_INPUT, $errorMsg);
	    header("Content-Length:".strlen($result));
	    $retval = EC_INVALID_INPUT;	
	    return;
	}
		
	if (!$instance->initialize()) {
	    $result = $instance->renderOutput();
	    header("Content-Length:".strlen($result));
	    $retval=$instance->getRetValue();	
	    return;
	}	
		
	if (!$instance->verifyInput($data)) {
	    $result = $instance->renderOutput();
	    header("Content-Length:".strlen($result));
	    $retval=$instance->getRetValue();		
	    return;
	}
	
	if (!$instance->process()) {
	    $result = $instance->renderOutput();
	    header("Content-Length:".strlen($result));
	    $retval=$instance->getRetValue();
	    return;
	}
	
	$result = $instance->renderOutput();
	$retval=0;		
	
	return;
}
?>
