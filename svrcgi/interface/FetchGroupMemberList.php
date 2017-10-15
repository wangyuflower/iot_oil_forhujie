<?php
require_once dirname(__FILE__) . '/../dao/dao_live/dao_live.class.php';

class FetchGroupMemberList extends AbstractInterface
{
    public function initialize()
    {
        return true;
    }
    
    public function verifyInput(&$args)
    {
        $req = $args['interface']['para'];
    
        $rules = array(     
        	'liveuserid'=>array('type' => 'string'),
        	'groupid'=>array('type' => 'string'),
        	'pageno'=>array('type' => 'int', 'range' => '[0,+)'),
        	'pagesize' => array('type' => 'int', 'range' => '[0,+)')        		
        );
    
        return $this->_verifyInput($args, $rules);
    }
    
    public function process()
    {
        interface_log(INFO, EC_OK,"FetchGroupMemberList args=" . var_export($this->_args, true));        
    	
        
        $config = getConf('ROUTE.DB');
        
        
        $dao_live = new dao_live($config['HOST'], $config['PORT'], $config['USER'], $config['PASSWD'], $config['DBNAME']);
        $error_message = "";
        
        $page_no = 1;
        $page_size = 10;
        if(array_key_exists('pageno', $this->_args) && (int)$this->_args['pageno'] > 0)
        {
        	$page_no = (int)$this->_args['pageno'];
        }
        if(array_key_exists('pagesize', $this->_args) && (int)$this->_args['pagesize'] >= 10 && (int)$this->_args['pagesize'] <= 100)
        {
        	$page_size = (int)$this->_args['pagesize'];
        }        
        $start_pos = ($page_no -1) * ($page_size);
        $ret = $dao_live->GetGroupListCount($this->_args['liveuserid'],$this->_args['groupid'],$count,$error_message);
        $result_list = array();
        if($ret == 0)
        {
        	$ret = $dao_live->GetGroupList($this->_args['liveuserid'],$this->_args['groupid'],$start_pos, $page_size, $result_list, $error_message);
        }
    	if($ret != 0)
    	{
    		$this->_retValue =$ret;
    		$error_message="db error:no permission";
    		$this->_retMsg = 'FetchGroupMemberList::process() fail '.genErrMsg($this->_retValue,$error_message);
    		return false;
    	}   
        
        $this->_retValue = EC_OK;
        $this->_data = array('totalcount' => $count,'memberlist' => $result_list);
        interface_log(INFO, EC_OK, 'FetchGroupMemberList::process() succeed');
        return true;
    }
}


?>
