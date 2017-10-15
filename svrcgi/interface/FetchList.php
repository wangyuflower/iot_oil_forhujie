<?php
require_once dirname(__FILE__) . '/../common/Common.php';
require_once dirname(__FILE__) . '/../dao/dao_live/dao_live.class.php';

class FetchList extends AbstractInterface
{
    public function initialize()
    {
        return true;
    }
    
    public function verifyInput(&$args)
    {
     //   $req = $args['interface']['para'];
    
        $rules = array(
            'flag' => array('type' => 'int', 'range' => '[0,+)'),        	
        	'pageno'=>array('type' => 'int', 'range' => '[0,+)'),
        	'pagesize' => array('type' => 'int', 'range' => '[0,+)')
        );
    
        return $this->_verifyInput($args, $rules);
    }
    
    public function process()
    {
        interface_log(INFO, EC_OK,"FetchList args=" . var_export($this->_args, true));
        
        $page_no = 1;
        $page_size = 10;
        $all_status =0;
        if(array_key_exists('pageno', $this->_args) && (int)$this->_args['pageno'] > 0)
        {
        	$page_no = (int)$this->_args['pageno'];
        }
        if(array_key_exists('pagesize', $this->_args) && (int)$this->_args['pagesize'] >= 10 && (int)$this->_args['pagesize'] <= 100)
        {
        	$page_size = (int)$this->_args['pagesize'];
        }      
        
        $config = getConf('ROUTE.DB');
        $dao_live = new dao_live($config['HOST'], $config['PORT'], $config['USER'], $config['PASSWD'], $config['DBNAME']);
        $error_message = "";  
        $ret = 0;
        
        $all_count =0;
        $result_list = array();
        $start_pos = ($page_no -1) * ($page_size);
        if($this->_args['flag'] == GET_LIST_TYPE_ONLINE)
        {
        	$ret = $dao_live->getLiveCount($all_count, $error_message);
        	if($ret == 0)
        	{
        		$ret = $dao_live->getDataList(GET_LIST_TYPE_ONLINE,$start_pos, $page_size, $result_list, $error_message);
        	}
        }
        elseif ($this->_args['flag'] == GET_LIST_TYPE_TAPE)
        {
        	$ret = $dao_live->getTapeCount($all_count, $error_message);
        	if($ret == 0)
        	{        	
        		$ret = $dao_live->getDataList(GET_LIST_TYPE_TAPE, $start_pos, $page_size,$result_list, $error_message);
        	}
        }
        elseif( $this->_args['flag'] == GET_LIST_LIVE_DATA_ALL )
        {
        	$ret = $dao_live->getAllLiveCount($all_count, $error_message);
        	if($ret == 0)
        	{
        		$ret = $dao_live->getDataList(GET_LIST_LIVE_DATA_ALL, $start_pos, $page_size,$result_list, $error_message);
        	}       	
        }
        elseif ($this->_args['flag'] == GET_LIST_TYPE_ALL)       
        {   
        	$ret = $dao_live->getLiveCount($live_count, $error_message);
        	if($ret == 0)
        	{
        		$ret = $dao_live->getTapeCount($tape_count, $error_message);
        		
        		//1、只需要查询live_data ；2、只需要查询tape_data;3、两个都需要查      ;4.拉取live_data所有数据
        		if($ret ==0 )
        		{        			
        			interface_log(DEBUG, EC_OK, 'FetchList all count:' .strval($live_count) . ":" . strval($tape_count). "startpos:". strval($start_pos) . "num:" .strval($page_size));        			 
        			$all_count = $live_count + $tape_count;
	        		if($live_count >= ($start_pos+$page_size))
	        		{
	        			$ret = $dao_live->getDataList(GET_LIST_TYPE_ONLINE,$start_pos, $page_size,$result_list, $error_message);	        			
	        		}        		
	        		elseif($live_count <= $start_pos)
	        		{           			
						$start_pos -=  $live_count ;      		
	        		    $ret = $dao_live->getDataList(GET_LIST_TYPE_TAPE,$start_pos, $page_size,$result_list, $error_message);
	        		}
	        		else 
	        		{
	        			$live_list = array();
	        			$tape_list = array();
	        			$tmp_live_num =  $live_count - $start_pos;
	        			$ret = $dao_live->getDataList(GET_LIST_TYPE_ONLINE,$start_pos, $tmp_live_num, $live_list, $error_message);
	        			if($ret ==0)
	        			{		        				
	        				$rest_num = $page_size - $tmp_live_num;
	        				$ret = $dao_live->getDataList(GET_LIST_TYPE_TAPE,0, $rest_num,$tape_list, $error_message);
	        				if($ret==0)
	        				{
	        					$result_list = array_merge($live_list,$tape_list);
	        				}
	        			}
        			}
        		}
        		
        	}
        }
        elseif($this->_args['flag'] ==GET_LIST_UGC_DATA)
        {
        	$ret = $dao_live->getUGCCount($all_count, $error_message);
        	if($ret == 0)
        	{
        		$ret = $dao_live->getUGCList( $start_pos, $page_size,$result_list, $error_message);
        	}       	
        }
    	
    	if($ret != 0)
    	{
    		$this->_retValue =$ret;
    		$error_message="db error:no permission";
    		$this->_retMsg = 'FetchList::process() fail '.genErrMsg($this->_retValue);
    		return false;
    	}

    	$this->_retValue = EC_OK;
    	$this->_data = array('totalcount' => $all_count,'pusherlist' => $result_list);
        interface_log(INFO, EC_OK, 'FetchList::process() succeed');
        return true;
    }
}

?>
