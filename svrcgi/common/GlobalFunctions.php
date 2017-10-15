<?php
include_once dirname(__FILE__).'/GlobalDefine.php';
include_once dirname(__FILE__).'/ConfFactory.php';
include_once dirname(__FILE__).'/ParamChecker.php';
include_once dirname(__FILE__).'/MiniLog.php';
include_once dirname(__FILE__). '/../conf/OutDefine.php';

function checkParam($rules = array(), &$args) {
	return ParamChecker::getInstance()->checkParam($rules, $args);
}

define("DEBUG", "DEBUG");
define("INFO", "INFO");
define("ERROR", "ERROR");
define("STAT", "STAT");


function instance($interfaceName) {
	$tmp = explode("_", $interfaceName);
    $dir = '';
    for($i = 0; $i < count($tmp) -1; $i++) {
        $dir = $dir.'/'.strtolower($tmp[$i]);
    }

	$file_name = dirname ( __FILE__ ) . '/../interface/' . $dir . '/' . $interfaceName . '.php';
	if (file_exists ( $file_name )) {
		require_once ($file_name);
		if (! class_exists ( $interfaceName )) {
			interface_log ( ERROR, 0, "invalid interfaceName of $interfaceName" );
			return null;
		} else {
			return new $interfaceName ();
		}
	} else {
		interface_log ( ERROR, 0, "invalid fileName of $file_name" );
		return null;
	}
}

function isLogLevelOff($logLevel)
{
	$swithFile = ROOT_PATH . '/log/' . 'NO_' . $logLevel;
	if (file_exists($swithFile)){
		return true;
	}else {
		return false;
	}
}


function _log($confName ,$logLevel, $errorCode, $logMessage = "no error msg")
{
	if (isLogLevelOff($logLevel)){
		return;
	}
	
	$st = debug_backtrace();

	$function = ''; //璋冪敤interface_log/web_log鐨勫嚱鏁板悕
	$file = '';     //璋冪敤interface_log/web_log鐨勬枃浠跺悕
	$line = '';     //璋冪敤interface_log/web_log鐨勮鍙�
	foreach($st as $item) {
		if($file) {
			$function = $item['function'];
			break;
		}
		if(substr($item['function'], -4) == '_log' && strlen($item['function']) > 4) {
			$file = $item['file'];
			$line = $item['line'];
		}
	}
	
	$function = $function ? $function : 'main';
	

	$file = explode("/", rtrim($file, '/'));
	$file = $file[count($file)-1];
	$prefix = "[$file][$function][$line][$logLevel][$errorCode] ";
	//if($logLevel == INFO || $logLevel == STAT) {
	//	$prefix = "[$logLevel]" ;
	//}
	if($errorCode)
		$logMessage = genErrMsg($errorCode , $logMessage);
	$logFileName = $confName . "_" . strtolower($logLevel);
	MiniLog::instance(ROOT_PATH . "/log/")->log($logFileName, $prefix . $logMessage);
	if (isLogLevelOff("DEBUG") || $logLevel == "DEBUG"){
		return ;
	}else {
		MiniLog::instance(ROOT_PATH . "/log/")->log($confName . "_" . "debug", $prefix . $logMessage);
	}
}


function interface_log($logLevel, $errorCode, $logMessage = "no error msg")
{
	_log('interface', $logLevel, $errorCode, $logMessage);
}

function component_log($logLevel, $errorCode, $logMessage = "no error msg")
{
    _log('component', $logLevel, $errorCode, $logMessage);
}

function mysql_log($logLevel, $errorCode, $logMessage = "no error msg")
{
    _log('mysql', $logLevel, $errorCode, $logMessage);
}


function init_log($args) {
	if(array_key_exists("eventId", $args) && array_key_exists("timestamp", $args)) {
		MiniLog::instance(ROOT_PATH . "/log/")->setRequestInfo($args["eventId"], $args["timestamp"], $args["interface"]["interfaceName"]);
	}
}

function parseInterfaceName($args) {
    if(array_key_exists("Action", $args) )
        return $args["Action"];

    return NULL;
}

function getConf($key) {
	$conf = ConfFactory::getInstance ();
	$keyArr = explode ( '.', $key );
	if (false === $keyArr) {
		return '';
	} else {
		$keyStr = '';
		foreach ( $keyArr as $k => $v ) {
			if ($k >= 2) {
				unset ( $keyArr [0] );
				unset ( $keyArr [1] );
				$keyStr .= "['" . implode ( ".", $keyArr ) . "']";
				break;
			}
			$keyStr .= "['" . $v . "']";
		}
		eval ( "\$keyStr = \$conf$keyStr ;" );
		if (isset ( $keyStr )) {
			return $keyStr;
		}
	}
	return '';
}


function genErrorResult(
		$retValue, $retMsg, $retData=array()) {
	return json_encode(
		array(
			"returnValue"=>$retValue,
			"returnMsg"=>$retMsg,
			"returnData"=>$retData
		)
	);
}

function getCurrentTime() 
{   
	date_default_timezone_set('PRC');
	$secondTime = time();
	return date('Y-m-d H:i:s', $secondTime);	
}

function getMillisecond($startTime = false) {
	$endTime = microtime(true) * 1000;
		
	if($startTime !== false) {
		$consumed = $endTime - $startTime;
		return round($consumed);
	}
		
	return $endTime;
}

function getTimeSpan($startDateTime, $endDateTime)
{
	$startTime = strtotime($startDateTime);
	
	$endTime=strtotime($endDateTime);
	
	return $endTime - $startTime;
}

function createTxTime($now_time)
{
//	$now_time = time();
	$now_time += 3*60*60;
	return dechex($now_time);
}

function ceatePushURLTxSecret($streaid,$txTime)
{
	$md5_val = md5(PUSH_URL_KEY . $streaid . $txTime);
	return $md5_val;
}

function GetCallBackSign($txTime)
{
	$md5_val = md5(CALL_BACK_KEY . strval($txTime));
	return $md5_val;
}

function GetTmpCallBackSign($txTime)
{
	$md5_val = md5(PUSH_URL_KEY . strval($txTime));
	return $md5_val;
}

function ParaStrFilter($str){
	$str = str_replace('<', '', $str);
	$str = str_replace('>', '', $str);
	$str = str_replace('"', '', $str);
	$str = str_replace('&', '', $str);
	return trim($str);
}
?>
