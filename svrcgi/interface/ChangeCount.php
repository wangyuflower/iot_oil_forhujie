<?php
require_once dirname(__FILE__) . '/../common/Common.php';
require_once dirname(__FILE__) . '/../dao/dao_live/dao_live.class.php';

class ChangeCount extends AbstractInterface
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
        	'optype' => array('type' => 'int'),
        	'flag' => array('type' => 'int'),
        	'fileid' => array('type' => 'string', 'nullable' => true, "emptyable"=>true)
        );
    
        return $this->_verifyInput($args, $rules);
    }
    
    public function process()
    {
        interface_log(INFO, EC_OK,"ChangeCount args=" . var_export($this->_args, true));
        
    	
        
        $config = getConf('ROUTE.DB');
        
        
        $dao_live = new dao_live($config['HOST'], $config['PORT'], $config['USER'], $config['PASSWD'], $config['DBNAME']);       
        
    
        $userid = $this->_args['userid'];
        if($this->_args['flag'] == 1)
        {
        	if(!isset($this->_args['fileid']))
        	{
        		$this->_retValue = EC_SYSTEM_INVALID_PARA;
        		$this->_retMsg = 'RequstLVBAddr::process() fail '.genErrMsg($this->_retValue);    
        		interface_log(ERROR, EC_SYSTEM_INVALID_PARA, $errorMsg);
        		return false;
        	}
        }
        
        $ret = $dao_live->changeLiveCount($userid, $this->_args['flag'], $this->_args['type'], 
        							$this->_args['optype'], $this->_args['fileid'], $update_result);
        if($ret != 0)
        {
        	$this->_retValue =$ret;
        	$error_message="db error:no permission";
        	$this->_retMsg = 'RequstLVBAddr::process() fail '.genErrMsg($this->_retValue);
        	return false;
        }   
        
        $this->_retValue = EC_OK;
        $this->_data=array("result"=>EC_OK);
        interface_log(INFO, EC_OK, 'ChangeCount::process() succeed');
        return true;
    }
}

?>
