<?php
require_once dirname(__FILE__) . '/../dao/dao_live/dao_live.class.php';

class ChangeStatus extends AbstractInterface
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
        	'status'=>array('type' => 'int', 'range' => '[0,+)')
        );
    
        return $this->_verifyInput($args, $rules);
    }
    
    public function process()
    {
        interface_log(INFO, EC_OK,"ChangeStatus args=" . var_export($this->_args, true));
        
    	
        
        $config = getConf('ROUTE.DB');
        
        
        $dao_live = new dao_live($config['HOST'], $config['PORT'], $config['USER'], $config['PASSWD'], $config['DBNAME']);
        $error_message = "";
        
    
        $userid = $this->_args['userid'];
        $status = $this->_args['status'];
 		if($status == 0)
 		{
			$ret = $dao_live->modifyLiveStatus($userid,1);
 		}
 		elseif($status == 1)
 		{
 			$ret = $dao_live->modifyLiveStatus($userid,0);
 		}
 		else
 		{
 			$this->_retValue = EC_SYSTEM_INVALID_PARA;
 			$this->_retMsg = 'ChangeStatus::process() fail '.genErrMsg($this->_retValue);
 			return false;
 		}
    	if($ret != 0)
    	{
    		$this->_retValue =$ret;
    		$error_message="db error:no permission";
    		$this->_retMsg = 'ChangeStatus::process() fail '.genErrMsg($this->_retValue,$error_message);
    		return false;
    	}   
        
        $this->_retValue = EC_OK;
        $this->_data=array("result"=>EC_OK);
        interface_log(INFO, EC_OK, 'StopLVB::process() succeed');
        return true;
    }
}


?>
