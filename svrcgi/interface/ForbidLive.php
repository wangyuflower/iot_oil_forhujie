<?php
require_once dirname(__FILE__) . '/../dao/dao_live/dao_live.class.php';
require_once dirname(__FILE__) . '/../conf/OutDefine.php';

class ForbidLive extends AbstractInterface
{
    public function initialize()
    {
        return true;
    }
    
    public function verifyInput(&$args)
    {
        $req = $args['interface']['para'];
    
        $rules = array(
            'userid' => array('type' => 'string'),
        	'forbidflag'=>array('type' => 'int', 'range' => '[0,+)')
        );
    
        return $this->_verifyInput($args, $rules);
    }
    
    public function process()
    {
        interface_log(INFO, EC_OK,"ForbidLive args=" . var_export($this->_args, true));
        
    	
        
        $config = getConf('ROUTE.DB');
        
        
        $dao_live = new dao_live($config['HOST'], $config['PORT'], $config['USER'], $config['PASSWD'], $config['DBNAME']);
        $error_message = "";
        
        $userid = $this->_args['userid'];
        $query_status = $this->_args['forbidflag'];
        $ret = $dao_live->getUserStreamID($userid,$stream_id,$cur_status);
        if($ret != 0)
        {
        	$this->_retValue =$ret;
        	$error_message="db error";
        	$this->_retMsg = 'ForbidLive::process() fail '. genErrMsg($this->_retValue,$error_message);
        	return false;
        }
    
        //如果现有状态和请求的一样，直接返回。否则调用后台接口设置并更新db
		if( ($cur_status==0 && $query_status==0) || ($cur_status==1 && $query_status==1) )
		{
			$this->_retValue = EC_OK;
			$this->_data=array("result"=>EC_OK);
			interface_log(INFO, EC_OK, 'ForbidLive::process() succeed');
			return true;
		}
//
		if($query_status==0)	
	 		$ret = $this->callLiveInterface($stream_id,1);
		else
			$ret = $this->callLiveInterface($stream_id,0);
 		if($ret)
 		{
			$ret = $dao_live->modifyForbidStatus($userid,$query_status); 		 		
	    	if($ret != 0)
	    	{
	    		$this->_retValue =$ret;
	    		$error_message="db error";
	    		$this->_retMsg = 'ChangeStatus::process() fail '.genErrMsg($this->_retValue,$error_message);
	    		return false;
	    	}   
	        
	        $this->_retValue = EC_OK;
	        $this->_data=array("result"=>EC_OK);
	        interface_log(INFO, EC_OK, 'ForbidLive::process() succeed');
	        return true;
 		}
 		else 
 		{
 			$this->_retValue =$ret;
 			$error_message="live service interface error";
 			$this->_retMsg = 'ForbidLive::process() fail '. genErrMsg($this->_retValue,$error_message);
 			return false;
 		}
    }
    
    private function callLiveInterface($stream_id,$status)
    {
    	$config = getConf('ROUTE.LIVE_WEBSERVICE');
    	$component_proxy=new Component_Video_Proxy(1,$config);
    	$time_now = time();
    	$md5_sign = GetCallBackSign($time_now); //1252463788
    	$para = "cmd=". APP_ID . "&interface=Live_Channel_SetStatus&Param.s.channel_id=".$stream_id ."&t=".strval($time_now)."&sign=".strval($md5_sign) . "&Param.n.status=" . strval($status);
    	$ret = $component_proxy->call($para, $result, $error_message, "GET");    
    	component_log(INFO,$err_code, "forbid_status_call::ret=".$err_code."call success.|returnData=".$result);    		
    
    	return $ret;
    }
}


?>
