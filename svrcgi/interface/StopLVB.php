<?php
require_once dirname(__FILE__) . '/../common/Common.php';
require_once dirname(__FILE__) . '/../dao/dao_live/dao_live.class.php';

class StopLVB extends AbstractInterface
{
    public function initialize()
    {
        return true;
    }
    
    public function verifyInput(&$args)
    {
        $req = $args['interface']['para'];
    
        $rules = array(
            'userid' => array('type' => 'string')
        );
    
        return $this->_verifyInput($args, $rules);
    }
    
    public function process()
    {
        interface_log(INFO, EC_OK,"StopLVB args=" . var_export($this->_args, true));
        
    	
        
        $config = getConf('ROUTE.DB');
        
        
        $dao_live = new dao_live($config['HOST'], $config['PORT'], $config['USER'], $config['PASSWD'], $config['DBNAME']);
        $error_message = "";
        
    
        $userid = $this->_args['userid'];
 
    	$ret = $dao_live->modifyLiveStatus($userid,0);
    	if($ret != 0)
    	{
    		$this->_retValue =$ret;
    		$error_message="db error:no permission";
    		$this->_retMsg = 'StopLVB::process() fail '.genErrMsg($this->_retValue. $error_message);
    		return false;
    	}   
        
        $this->_retValue = EC_OK;
        $this->_data=array("result"=>EC_OK);
        interface_log(INFO, EC_OK, 'StopLVB::process() succeed');
        return true;
    }
}

?>
