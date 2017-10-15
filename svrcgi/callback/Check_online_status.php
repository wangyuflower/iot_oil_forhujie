<?php
require_once dirname(__FILE__) . '/../common/CommonProxy.class.php';
require_once dirname(__FILE__) . '/../common/GlobalFunctions.php';
require_once dirname(__FILE__) . '/../dao/dao_live/dao_live.class.php';
require_once dirname(__FILE__) . '/../conf/OutDefine.php';

class Check_online_status
{
 	private $_component_video_proxy;
    private $_report_url;
    public $report_host;
    
   
    private $_eventId;
    
    public function __construct($eventId, $conf='ROUTE.LIVE_WEBSERVICE')
    {
    	$config = getConf($conf);
    	
    	component_log(INFO,$err_code, "Vod_Call_Cgw::config=".var_export($config, true));
    	
        $this->_component_video_proxy=new Component_Video_Proxy($eventId,$config);
    
        $this->_eventId = $eventId;
    }
    
    private function check_call($stream_id)
    {
    	$time_now = time(); 
    	//$md5_sign = md5(PUSH_URL_KEY . strval($time_now));
    	$md5_sign = GetCallBackSign($time_now);
     	$para = "cmd=" . APP_ID ."&interface=Live_Channel_GetStatus&Param.s.channel_id=".$stream_id ."&t=".strval($time_now)."&sign=".strval($md5_sign);
    	
        $ret = $this->_component_video_proxy->call($para, $result, $error_message, "GET");
        
    	$err_code = 0;
    	if ($ret)
    	{
    		component_log(INFO,$err_code, "check_call::ret=".$err_code."call success.|returnData=".$result);
    		if($result['ret'] == 20601)
    		{
    			$config = getConf('ROUTE.DB');
    			$dao_live = new dao_live($config['HOST'], $config['PORT'], $config['USER'], $config['PASSWD'], $config['DBNAME']);
    			$dao_live->updateCheckStatus($stream_id,0);
    			return ture;
    		}
    		$content = $result['output'];
    		foreach ($content as $value)
    		{
    			if($value['recv_type'] == 1) 
    			{//rtmp
	    			$status = $value['status'];
	    			$config = getConf('ROUTE.DB');    			    			
	    			$dao_live = new dao_live($config['HOST'], $config['PORT'], $config['USER'], $config['PASSWD'], $config['DBNAME']);
	    			$dao_live->updateCheckStatus($stream_id,$status);
	    			break;
    			}
    		}
    	} 
    	else
    	{       		
    		$err_code = (!$result)?EC_NETWORK_OR_TIMEOUT_ERROR:$result["ret"];	// 如果没有返回，则认为超时
    		$error_message=$result["ret"];
    		component_log(ERROR,$err_code, "check_call::ret=".$err_code."call failed.|returnMsg=".$error_message);
    	}
    	
    	return $err_code;
    }
    
    public function check_status()
    {
    	$config = getConf('ROUTE.DB');   	
    	
    	$dao_live = new dao_live($config['HOST'], $config['PORT'], $config['USER'], $config['PASSWD'], $config['DBNAME']);
    	$dao_live->getOnlineStreamID($result);    	
    	foreach ($result as $value)
    	{
    		$this->check_call($value['stream_id']);
    	}
    	
    }
    
}

// ignore_user_abort();
// set_time_limit(0);
// $interval=60;
// do{
	component_log(DEBUG,EC_OK, "ready check_status ");
	$check_call = new Check_online_status(time());
	$check_call->check_status();
// 	sleep($interval);
// }while(true);

?>