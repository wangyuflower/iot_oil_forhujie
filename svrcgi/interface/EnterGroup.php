<?php
require_once dirname(__FILE__) . '/../dao/dao_live/dao_live.class.php';

class EnterGroup extends AbstractInterface
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
        	'liveuserid'=>array('type' => 'string'),
        	'groupid'=>array('type' => 'string'),
        	'nickname' => array( 'type' => 'string',"nullable"=>true, "emptyable"=>true),
        	'headpic' => array('type' => 'string',"nullable"=>true, "emptyable"=>true),
        	'flag' => array('type' => 'int')
        );
    
        return $this->_verifyInput($args, $rules);
    }
    
    public function process()
    {
        interface_log(INFO, EC_OK,"EnterGroup args=" . var_export($this->_args, true));        
    	
        
        $config = getConf('ROUTE.DB');
        
        
        $dao_live = new dao_live($config['HOST'], $config['PORT'], $config['USER'], $config['PASSWD'], $config['DBNAME']);
        $error_message = "";
        
    
      	 $ret = $dao_live->AddGroupInfo($this->_args['userid'],$this->_args['liveuserid'],$this->_args['groupid'],$this->_args['nickname'],$this->_args['headpic']);
 		 
 	
    	if($ret != 0)
    	{
    		$this->_retValue =$ret;
    		$error_message="db error:no permission";
    		$this->_retMsg = 'EnterGroup::process() fail '.genErrMsg($this->_retValue,$error_message);
    		return false;
    	}
    	//当flag==1的时候 groupid相当于是 fileid
    	$dao_live->changeLiveCount($this->_args['liveuserid'],$this->_args['flag'],0,0,$this->_args['groupid'],$update_result);
        
        $this->_retValue = EC_OK;
        $this->_data=array("result"=>EC_OK);
        interface_log(INFO, EC_OK, 'EnterGroup::process() succeed');
        return true;
    }
}


?>
