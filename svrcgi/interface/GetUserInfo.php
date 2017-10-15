<?php
require_once dirname(__FILE__) . '/../common/Common.php';
require_once dirname(__FILE__) . '/../dao/dao_live/dao_live.class.php';

class GetUserInfo extends AbstractInterface
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
        	'type' => array('type' => 'int'),
        	'fileid' => array('type' => 'string',"nullable"=>true, "emptyable"=>true)      	
        	
        );
    
        return $this->_verifyInput($args, $rules);
    }
    
    public function process()
    {
        interface_log(INFO, EC_OK,"GetUserInfo args=" . var_export($this->_args, true));        
    	
        
        $config = getConf('ROUTE.DB');
        
        
        $dao_live = new dao_live($config['HOST'], $config['PORT'], $config['USER'], $config['PASSWD'], $config['DBNAME']);
        $error_message = "";       
       
        $userid = $this->_args['userid'];
        

 		$ret = $dao_live->getUserInfo($userid, $this->_args['type'],$this->_args['fileid'],$result);
 		
    	if($ret != 0)
    	{
    		$this->_retValue =$ret;
    		$this->_retMsg = 'GetUserInfo::process() fail '.genErrMsg($this->_retValue);
    		return false;
    	} 	
          
        $this->_retValue = EC_OK;
        $this->_data= $result;
        interface_log(INFO, EC_OK, 'GetUserInfo::process() succeed');
        return true;
    }
}

?>
