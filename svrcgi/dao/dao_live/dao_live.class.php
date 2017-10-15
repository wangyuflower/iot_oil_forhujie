<?php
require_once dirname(__FILE__).'/../dao_base/dao.class.php';

class dao_live extends Dao
{
    public function AddLiveUser($userid,$live_code,$groupid,$title,$array_userinfo,$push_url,$pull_url,$hls_play_url,$now_time)
    {
       try {
	       	$tmp_data = array(
	       			'userid' => $userid,
	       			'stream_id' => $live_code,
	       			'groupid'=> $groupid,
	       			'title'=> $title,
	       			'push_url' => $push_url,
	       			'play_url' => $pull_url,
	       			'hls_play_url' => $hls_play_url,
	       			'status' => 0,
	       			'create_time' => date('Y-m-d H:i:s',$now_time)
	       	);
	       	if(count($array_userinfo)!=0)
	       	{

				if(isset($array_userinfo['nickname']))
				{
					$tmp_data['nickname'] = $array_userinfo['nickname'];
				}
				if(isset($array_userinfo['headpic']))
				{
					$tmp_data['headpic'] = $array_userinfo['headpic'];
				}
				if(isset($array_userinfo['frontcover']))
				{
					$tmp_data['frontcover'] = $array_userinfo['frontcover'];
				}
				if(isset($array_userinfo['location']))
				{
					$tmp_data['location'] = $array_userinfo['location'];
				}
				if(isset($array_userinfo['desc']))
				{
					$tmp_data['desc'] = $array_userinfo['desc'];
				}
	       	}
       		$this->session_->ReplaceObject(
                'live_data', $tmp_data);
       }catch(Exception $e){
       		$error_message = $e->getMessage();
    		 mysql_log(ERROR, EC_OK, $userid . ":" . $error_message );
       		return self::ERROR_CODE_DB_ERROR;
       }
       return self::ERROR_CODE_SUCCESSFUL;

    }

    public function AddUGCData($userid, $file_id,$play_url, $title,$array_userinfo)
    {
    	try {
$now_time = time();
    		$tmp_data = array(
    				'userid' => $userid,
    				'file_id' => $file_id,
    				'play_url'=> $play_url,
    				'title'=> $title,
    				'create_time' => date('Y-m-d H:i:s',$now_time)
    		);
    		if(count($array_userinfo)!=0)
    		{
    	
    			if(isset($array_userinfo['nickname']))
    			{
    				$tmp_data['nickname'] = $array_userinfo['nickname'];
    			}
    			if(isset($array_userinfo['headpic']))
    			{
    				$tmp_data['headpic'] = $array_userinfo['headpic'];
    			}
    			if(isset($array_userinfo['frontcover']))
    			{
    				$tmp_data['frontcover'] = $array_userinfo['frontcover'];
    			}
    			if(isset($array_userinfo['location']))
    			{
    				$tmp_data['location'] = $array_userinfo['location'];
    			}
    		}
    		$this->session_->ReplaceObject(
    				'UGC_data', $tmp_data);
    	}catch(Exception $e){
    		$error_message = $e->getMessage();
    		mysql_log(ERROR, EC_OK, $userid . ":" . $error_message );
    		return self::ERROR_CODE_DB_ERROR;
    	}
    	return self::ERROR_CODE_SUCCESSFUL;
    }
    
    public function getUserStreamID($userid,&$stream_id,&$status)
    {
    	$query_sql = "select stream_id,forbid_status from live_data where userid = '" . $userid . "'";
    	try
    	{
    		$result = $this->session_->ExecuteSelectSql($query_sql);
    		if(!empty($result) && count($result)==1)
    		{
    			$stream_id = $result[0]['stream_id'];
    			$status = $result[0]['forbid_status'];
    		}
    		else
    		{
    			return self::ERROR_CODE_DB_ERROR;
    		}
    	}
    	catch (Exception $e)
    	{
    		$error_message = $e->getMessage();
    		mysql_log(ERROR, EC_OK, "getUserStreamID error :" . $error_message );
    		return self::ERROR_CODE_DB_ERROR;
    	}
    	return self::ERROR_CODE_SUCCESSFUL;
    }

    public function getOnlineStreamID(&$result)
    {
    	$query_sql = "select stream_id from live_data where status = 1;";
    	try
    	{
    		$result = $this->session_->ExecuteSelectSql($query_sql);

    	}
    	catch (Exception $e)
    	{
    		$error_message = $e->getMessage();
    		mysql_log(ERROR, EC_OK, "get live_data count error :" . $error_message );
    		return self::ERROR_CODE_DB_ERROR;
    	}
    	return self::ERROR_CODE_SUCCESSFUL;
    }
    public function modifyLiveStatus($userid,$status)
    {
    	try {
    		   $task = array(
            		'status'=>$status
                );

            $this->session_->UpdateObject('live_data',
	                array('userid'=>$userid),
	                $task);
    	}catch(Exception $e){
    		$error_message = $e->getMessage();
    		mysql_log(ERROR, EC_OK, $userid . ":" . $error_message );
    		return self::ERROR_CODE_DB_ERROR;
    	}
    	return self::ERROR_CODE_SUCCESSFUL;
    }

    public function modifyForbidStatus($userid,$status)
    {
    	try {
		if($status==0)
		{	
    		$task = array(
    				'forbid_status'=>$status
    		);

    		$this->session_->UpdateObject('live_data',
    				array('userid'=>$userid),
    				$task);
		}
		else
{
                $task = array(
                                'forbid_status'=>$status,
				'status'=>0
                );

                $this->session_->UpdateObject('live_data',
                                array('userid'=>$userid),
                                $task);
}
    	}catch(Exception $e){
    		$error_message = $e->getMessage();
    		mysql_log(ERROR, EC_OK, $userid . ":" . $error_message );
    		return self::ERROR_CODE_DB_ERROR;
    	}
    	return self::ERROR_CODE_SUCCESSFUL;
    }

    public function callBackLiveStatus($stream_id,$status)
    {
    	try {
    		$task = array(
    				'status'=>$status
    		);

    		$this->session_->UpdateObject('live_data',
    				array('stream_id'=>$stream_id),
    				$task);
    	}catch(Exception $e){
    		$error_message = $e->getMessage();
    		mysql_log(ERROR, EC_OK, $stream_id . ":" . $error_message );
    		return self::ERROR_CODE_DB_ERROR;
    	}
    	return self::ERROR_CODE_SUCCESSFUL;
    }

    public function getUGCCount(&$live_count,&$error_message)
    {
    	$query_sql = "select count(*) as all_count from UGC_data";
    	try
    	{
    		$count_result = $this->session_->ExecuteSelectSql($query_sql);
    		$live_count = $count_result[0]['all_count'];
    	}
    	catch (Exception $e)
    	{
    		$error_message = $e->getMessage();
    		mysql_log(ERROR, EC_OK, "get getUGCCount count error :" . $error_message );
    		return self::ERROR_CODE_DB_ERROR;
    	}
    	return self::ERROR_CODE_SUCCESSFUL;
    }

	public function getLiveCount(&$live_count,&$error_message)
	{
		$query_sql = "select count(*) as all_count from live_data where status = 1";
		try
		{
			$count_result = $this->session_->ExecuteSelectSql($query_sql);
			$live_count = $count_result[0]['all_count'];
		}
		catch (Exception $e)
		{
			$error_message = $e->getMessage();
			mysql_log(ERROR, EC_OK, "get live_data count error :" . $error_message );
			return self::ERROR_CODE_DB_ERROR;
		}
		return self::ERROR_CODE_SUCCESSFUL;
	}

	public function getAllLiveCount(&$live_count,&$error_message)
	{
		$query_sql = "select count(*) as all_count from live_data ";
		try
		{
			$count_result = $this->session_->ExecuteSelectSql($query_sql);
			$live_count = $count_result[0]['all_count'];
		}
		catch (Exception $e)
		{
			$error_message = $e->getMessage();
			mysql_log(ERROR, EC_OK, "get live_data count error :" . $error_message );
			return self::ERROR_CODE_DB_ERROR;
		}
		return self::ERROR_CODE_SUCCESSFUL;
	}

	public function getTapeCount(&$tape_count,&$error_message)
	{
		$now_time = time();
		$interval = TAPE_FILE_VALID_TIME;
		$query_sql = "select count(*) as all_count from tape_data where (". $now_time . " - unix_timestamp(create_time)) < " .$interval;
		try
		{
			$count_result = $this->session_->ExecuteSelectSql($query_sql);
			$tape_count = $count_result[0]['all_count'];
		}
		catch (Exception $e)
		{
			$error_message = $e->getMessage();
			mysql_log(ERROR, EC_OK, "get tape_count count error :" . $error_message );
			return self::ERROR_CODE_DB_ERROR;
		}
		return self::ERROR_CODE_SUCCESSFUL;
	}

	public function getUserInfo($userid,$type,$fileid,&$result)
	{
		/*begin:转义字符串，防止sql注入  added by alongchen 2017-01-04 */
        $userid = $this->session_->EscapeString($userid);
		$fileid = $this->session_->EscapeString($fileid);
		/*end  :转义字符串，防止sql注入  added by alongchen 2017-01-04 */
		if($type == 0)
		{
			$query_sql = "select * from live_data where userid = '" . $userid . "'";
		}
	    elseif ($type == 1)
        {
        	if(!isset($fileid))
			{
				return  EC_SYSTEM_INVALID_PARA;
			}
            $query_sql = "select * from tape_data where userid = '" . $userid . "' and file_id = '" . $fileid . "'";
		}
		elseif($type ==2)
		{
			if(!isset($fileid))
			{
				return  EC_SYSTEM_INVALID_PARA;
			}
			$query_sql = "select * from UGC_data where userid = '" . $userid . "' and file_id = '" . $fileid . "'";
		}
		else
		{
			return EC_SYSTEM_INVALID_PARA;
		}

		try
		{
			$alist = $this->session_->ExecuteSelectSql($query_sql);
			if(!empty($alist) && count($alist)==1)
			{
				$list = $alist[0];
			}
			else
			{
				return self::ERROR_CODE_DB_ERROR;
			}

			$groupid = '';
			$fileid = '';
			if($type == 0)
			{
				$groupid = $list['groupid'];
			}
			else
			{
				$fileid = $list['file_id'];
			}

			$result = array(
					'userid' => $list['userid'],
					'groupid' =>$groupid,
					'timestamp' => strtotime($list['create_time']),
					'type' => $type,
					'viewercount' => intval($list['viewer_count']),
					'likecount' => intval($list['like_count']),
					'title' => $list['title'],
					'playurl' => $list['play_url'],
					'hls_play_url' => $list['hls_play_url'],
					'status' => $list['status'],
					'fileid' => $fileid,
					'userinfo' => array(
							'nickname' => $list['nickname'],
							'headpic'  => $list['headpic'],
							'frontcover' => $list['frontcover'],
                            'location' => $list['location'],
							'desc' => $list['desc'],
					)
				);
		}
		catch (Exception $e)
		{
			$error_message = $e->getMessage();
			mysql_log(ERROR, EC_OK, "get list error :" . $error_message );
			return self::ERROR_CODE_DB_ERROR;
		}
		return self::ERROR_CODE_SUCCESSFUL;
	}

	public function getUGCList($start_row,$row_number,&$live_list,&$error_message)
	{
		//7天内的
		$now_time = time();
		$interval = TAPE_FILE_VALID_TIME;
		$search_sql =  "select * from UGC_data where (" . $now_time . " - unix_timestamp(create_time)) < " .$interval 
		."  order by create_time desc limit ". strval($start_row) . "," . strval($row_number);
		try
		{
			$result = $this->session_->ExecuteSelectSql($search_sql);
		
			if(!empty($result))
			{
				foreach ($result as $list)
				{
			
		
					$one_record = array(
							'userid' => $list['userid'],
							'fileid' => $list['file_id'],
							'timestamp' => strtotime($list['create_time']) ? strtotime($list['create_time']) : 0,
							'title' => $list['title'],
							'type' => 2,
							'playurl' => $list['play_url'],							
							'userinfo' => array(
									'nickname' => $list['nickname'],
									'headpic'  => $list['headpic'],
									'frontcover' => $list['frontcover'],
									'location' => $list['location'],
							)
					);
					array_push($live_list,$one_record);
				}
			}
		}
		catch (Exception $e)
		{
			$error_message = $e->getMessage();
			mysql_log(ERROR, EC_OK, "get list error :" . $error_message );
			return self::ERROR_CODE_DB_ERROR;
		}
		return self::ERROR_CODE_SUCCESSFUL;		
		
	}
    public function getDataList($type,$start_row,$row_number,&$live_list,&$error_message)
    {
    	$table_name;
    	$search_sql;
    	if($type == GET_LIST_TYPE_ONLINE)
    	{
    		$table_name = "live_data";
    		$search_sql = " where status = 1";

    	}
    	elseif ($type == GET_LIST_LIVE_DATA_ALL)
    	{
    		$table_name = "live_data";
    		$search_sql = " ";
    	}
    	else
    	{
    		$table_name = "tape_data";
    		//7天内的
    		$now_time = time();
    		$interval = TAPE_FILE_VALID_TIME;
    		$search_sql =  " where (" . $now_time . " - unix_timestamp(create_time)) < " .$interval . " AND play_url !=''";
    	}
    	$base_sql = "select * from ". $table_name ;
    	$query_sql = $base_sql.$search_sql."  order by create_time desc limit ". strval($start_row) . "," . strval($row_number);

    	try
    	{
    		$result = $this->session_->ExecuteSelectSql($query_sql);

    		if(!empty($result))
    		{
    			foreach ($result as $list)
    			{
    				$groupid = '';
    				$fileid = '';
    				$record_type = 0;
				$desc = '';
				$forbid = '0';
    				if($type == GET_LIST_TYPE_ONLINE || $type == GET_LIST_LIVE_DATA_ALL)
    				{
    					$groupid = $list['groupid'];
					$desc = $list['desc'];
					$forbid = $list['forbid_status'];	
    				}
    				else
    				{
    					$fileid = $list['file_id'];
    					$record_type = 1;
    				}

    				$one_record = array(
    						'userid' => $list['userid'],
    						'groupid' =>$groupid,
    						'timestamp' => strtotime($list['create_time']) ? strtotime($list['create_time']) : 0,
    						'type' => $record_type,
    						'viewercount' => intval($list['viewer_count']),
    						'likecount' => intval($list['like_count']),
    						'title' => $list['title'],
			                'playurl' => $list['play_url'],
    						'hls_play_url' => $list['hls_play_url'],
                            'desc' => $desc,
 			                'forbid_status' => intval($forbid),
    						'status' => $list['status'],
    						'fileid' => $fileid,
    						'userinfo' => array(
    								'nickname' => $list['nickname'],
    								'headpic'  => $list['headpic'],
    								'frontcover' => $list['frontcover'],
    								'location' => $list['location'],
    						)
    				);
    				array_push($live_list,$one_record);
    			}
    		}
    	}
    	catch (Exception $e)
    	{
    		$error_message = $e->getMessage();
    		mysql_log(ERROR, EC_OK, "get list error :" . $error_message );
    		return self::ERROR_CODE_DB_ERROR;
    	}
    	return self::ERROR_CODE_SUCCESSFUL;

    }

    private function getLiveUser($stream_id,&$userinfo)
    {
    	try
    	{
	    	$query_sql = "select * from live_data where stream_id = '" . $stream_id ."'";
	    	$result = $this->session_->ExecuteSelectSql($query_sql);
	    	if(!empty($result) && count($result)==1)
	    	{
	    		$userinfo = $result[0];
	    	}
	    	else
	    	{
	    		return self::ERROR_CODE_DB_ERROR;
	    	}
    	}
    	catch (Exception $e)
    	{
    		$error_message = $e->getMessage();
    		mysql_log(ERROR, EC_OK, "getLiveUser error :" . $stream_id . ":" . $error_message );
    		return self::ERROR_CODE_DB_ERROR;
    	}
    	return self::ERROR_CODE_SUCCESSFUL;
    }
    
    private function getTheSameStreamTape($userid,$start_time,$end_time,&$file_id)
    {
		/*begin:转义字符串，防止sql注入  added by alongchen 2017-01-04 */
		$userid = $this->session_->EscapeString($userid);
		/*end  :转义字符串，防止sql注入  added by alongchen 2017-01-04 */
    	try
    	{    		
    		$query_sql = "select * from tape_data where userid = '" . $userid . 
      		 "' and ((unix_timestamp(start_time) > " . $start_time . " AND unix_timestamp(start_time) < " .$end_time .") OR " .
    				"(unix_timestamp(create_time) > " . $start_time . " AND unix_timestamp(create_time) < " .$end_time .") OR " .
    				"(unix_timestamp(start_time) <= " . $start_time . " AND unix_timestamp(create_time) >= " .$end_time .") OR " .    				
    				"(unix_timestamp(start_time) > " . $start_time . " AND unix_timestamp(create_time) < " .$end_time .") )" ;
    		$result = $this->session_->ExecuteSelectSql($query_sql);
    		if(!empty($result) && count($result)==1)
    		{
    			$file_id = $result[0]['file_id'];
    			return 1;
    		}
    		return -1;
    	}
    	catch (Exception $e)
    	{
    		$error_message = $e->getMessage();
    		mysql_log(ERROR, EC_OK, "getLiveUser error :" . $userid . ":" . $error_message );
    		return self::ERROR_CODE_DB_ERROR;
    	}
    //	return self::ERROR_CODE_SUCCESSFUL;
    }    

    public function updateCheckStatus($stream_id,$status)
    {
    	try
    	{
	    	//0:断流；1:开启；3:关闭
	    	if($status != 1)
	    	{
	    		$query_sql = "select check_status from live_data where stream_id = '" . $stream_id ."'";
	    		$result = $this->session_->ExecuteSelectSql($query_sql);
	    		if(!empty($result) && count($result)==1)
	    		{
	    			$check_status = $result[0]['check_status'];
	    			if($check_status == 2 )
	    			{
	    				$check_status = 0;
	    				$q_sql = "update live_data set status = 0, check_status = 0".  " where stream_id = '" . $stream_id ."'"  ;
	    				$this->session_->ExecuteSelectSql($q_sql);
	    			}
	    			else
	    			{
	    				$check_status = $check_status + 1;
	    				$q_sql = "update live_data set check_status = ". $check_status . " where stream_id = '" . $stream_id ."'"  ;
	    				$this->session_->ExecuteSelectSql($q_sql);
	    			}
	    		}
	    		else
	    		{
	    			return self::ERROR_CODE_DB_ERROR;
	    		}
	    	}
	    	else
	    	{
	    		//$query_sql = "select check_status from live_data where stream_id = '" . $stream_id ."'";
	    	}
    	}
    	catch (Exception $e)
    	{
    		$error_message = $e->getMessage();
    		mysql_log(ERROR, EC_OK, "getLiveUser error :" . $stream_id . ":" . $error_message );
    		return self::ERROR_CODE_DB_ERROR;
    	}
    	return self::ERROR_CODE_SUCCESSFUL;
    }

    public function addTapeFile($stream_id,$video_id,$video_url,$start_time,$end_time,$format_type)
    {
    	try {
    		$ret = $this->getLiveUser($stream_id,$userinfo);
    		if($ret != self::ERROR_CODE_SUCCESSFUL)
    		{
    			mysql_log(ERROR, EC_OK, "getLiveUser Error:" . $stream_id);
    			return self::ERROR_CODE_DB_ERROR;
    		}
    		
    		//先查询db中是否已经有同一个stream_id的一种格式的记录了，如果有，更新。如果没有，新增记录
    		$sel_ret = $this->getTheSameStreamTape($userinfo['userid'],$start_time,$end_time,$sel_file_id); 
    		if(self::ERROR_CODE_DB_ERROR == $sel_ret)
    		{
    			mysql_log(ERROR, EC_OK, " getTheSameStreamTape Error:" . $userid);
    			return self::ERROR_CODE_DB_ERROR;
    		}
    		elseif(1 == $sel_ret)
    		{
    			$tmp_data = array();
    			if($format_type === "flv")
    			{
      				$tmp_data = array('play_url'=>$video_url);
    			}
    			else 
    			{
      				$tmp_data = array('hls_play_url'=>$video_url);
    			}   	
    			$this->session_->UpdateObject('tape_data',
    					array('userid'=>$userinfo['userid'],'file_id'=>$sel_file_id),
    					$tmp_data);
    		}
    		//老的新增逻辑
    		elseif (-1 == $sel_ret)
    		{
	    		$tmp_data = array(
	    				'userid' => $userinfo['userid'],
	    				'title'=> $userinfo['title'],
//	    				'play_url' => $video_url,
	    				'file_id' => $video_id,
	    				'start_time' => date('Y-m-d H:i:s',$start_time),
	    				'create_time' => date('Y-m-d H:i:s',$end_time)
	    		);
	    		if($format_type === "flv")
	    		{
	    			$tmp_data['play_url'] = $video_url;
	    		}
	    		else
	    		{
	    			$tmp_data['hls_play_url'] = $video_url;
	    		}
	
	    	      if(isset($userinfo['nickname']))
	              {
	              		$tmp_data['nickname'] = $userinfo['nickname'];
	              }
	              if(isset($userinfo['headpic']))
	              {
	                     $tmp_data['headpic'] = $userinfo['headpic'];
	              }
	              if(isset($userinfo['frontcover']))
	              {
	                     $tmp_data['frontcover'] = $userinfo['frontcover'];
	              }
	              if(isset($userinfo['location']))
	              {
	                     $tmp_data['location'] = $userinfo['location'];
	              }
	
	              if(isset($userinfo['desc']))
	              {
	                      $tmp_data['desc'] = $userinfo['desc'];
	              }
			
	    		$this->session_->AddObject('tape_data', $tmp_data);
    		}
    	}catch(Exception $e){
    		$error_message = $e->getMessage();
    		mysql_log(ERROR, EC_OK, "add tape data:" . $stream_id .":" . $error_message);
    		return self::ERROR_CODE_DB_ERROR;
    	}
    	return self::ERROR_CODE_SUCCESSFUL;
    }

    public function changeLiveCount($userid,$flag,$count_type,$optype,$file_id,&$update_result)
    {
		/*begin:转义字符串，防止sql注入  added by alongchen 2017-01-04 */
		$userid = $this->session_->EscapeString($userid);
		$file_id = $this->session_->EscapeString($file_id);
		/*end  :转义字符串，防止sql注入  added by alongchen 2017-01-04 */
    	if($count_type == VIEWER_COUNT)
    	{
    		$str_count_type  = "viewer_count";
    	}
    	elseif($count_type == LIKE_COUNT)
    	{
    		$str_count_type = "like_count";
    	}
    	else
    	{
    		return EC_SYSTEM_INVALID_PARA;
    	}

    	if($flag == 0 )
    	{
    		if($optype == COUNT_ADD)
    		{
    			$query_sql = "UPDATE live_data " . "set " . $str_count_type . " = " . $str_count_type . " + 1 where userid = '" . $userid . "'";
    		}
    		elseif ($optype == COUNT_DELETE)
    		{
    			$query_sql = "UPDATE live_data " . "set " . $str_count_type . " = " . $str_count_type .
    						 " - 1 where userid = '" . $userid . "' and " . $str_count_type . ">0";
    		}
    		else
    		{
    			return EC_SYSTEM_INVALID_PARA;
    		}
    	}
    	elseif($flag == 1 )
    	{
    		if($optype == COUNT_ADD)
    		{
    			$query_sql = "UPDATE tape_data " . "set " . $str_count_type . " = " . $str_count_type .
    						 " + 1 where userid = '" . $userid .  "' and file_id = '" . $file_id ."'";
    		}
    		elseif ($optype == COUNT_DELETE)
    		{
    			$query_sql = "UPDATE tape_data " . "set " . $str_count_type . " = " . $str_count_type .
    						" - 1 where userid = '" . $userid .  "' and file_id = '" . $file_id . "' and " . $str_count_type . ">0";
    		}
    		else
    		{
    			return EC_SYSTEM_INVALID_PARA;
    		}

    	}
    	else
    	{
    		return EC_SYSTEM_INVALID_PARA;
    	}

		try
		{
	    	$update_result = $this->session_->ExecuteSelectSql($query_sql);
	    }catch(Exception $e){
	    	$error_message = $e->getMessage();
	    	mysql_log(ERROR, EC_OK, "changeLiveCount:" . $userid .":" . $error_message);
	    	return self::ERROR_CODE_DB_ERROR;
	    }
	    return self::ERROR_CODE_SUCCESSFUL;
    }
    
    public function AddGroupInfo($userid,$liveuserid,$groupid,$nickname,$headpic)
    {
		/*begin:转义字符串，防止sql注入  added by alongchen 2017-01-04 */
		$userid = $this->session_->EscapeString($userid);
		$groupid = $this->session_->EscapeString($groupid);
		$liveuserid=$this->session_->EscapeString($liveuserid);

		$liveuserid=$this->session_->EscapeString($liveuserid);
		/*end  :转义字符串，防止sql注入  added by alongchen 2017-01-04 */
    	try {
    		$tmp_data = array(
    				'userid'=>$userid,
    				'liveuserid'=>$liveuserid,
    				'groupid'=>$groupid
    		);
    		if(isset($nickname))
    		{
				$nickname=$this->session_->EscapeString($nickname);//added by alongchen 2017-01-04防sql注入
    			$tmp_data['nickname'] = $nickname;
    		}
    		if(isset($headpic))
    		{
				$headpic=$this->session_->EscapeString($headpic);//added by alongchen 2017-01-04防sql注入
    			$tmp_data['headpic'] = $headpic;
    		}
    		    		
    
       		$this->session_->ReplaceObject(
                'group_info', $tmp_data);
    	}catch(Exception $e){
    		$error_message = $e->getMessage();
    		mysql_log(ERROR, EC_OK, $userid . ":" . $error_message );
    		return self::ERROR_CODE_DB_ERROR;
    	}
    	return self::ERROR_CODE_SUCCESSFUL;
    }

    public function RemoveGroupInfo($userid,$liveuserid,$groupid)
    {
		/*begin:转义字符串，防止sql注入  added by alongchen 2017-01-04 */
		$userid = $this->session_->EscapeString($userid);
		/*end  :转义字符串，防止sql注入  added by alongchen 2017-01-04 */
    	try {
    		$tmp_data = array(
    				'userid'=>$userid,
    				'liveuserid'=>$liveuserid,
    				'groupid'=>$groupid,
    		);
    
    		$this->session_->DeleteObject(
    				'group_info', $tmp_data);
    	}catch(Exception $e){
    		$error_message = $e->getMessage();
    		mysql_log(ERROR, EC_OK, $userid . ":" . $error_message );
    		return self::ERROR_CODE_DB_ERROR;
    	}
    	return self::ERROR_CODE_SUCCESSFUL;
    }  
    
    public function GetGroupList($liveuserid,$groupid,$start_row,$row_number,&$group_list,&$error_message)
    {
		/*begin:转义字符串，防止sql注入  added by alongchen 2017-01-04 */
		$groupid = $this->session_->EscapeString($groupid);
		$liveuserid=$this->session_->EscapeString($liveuserid);
		/*end  :转义字符串，防止sql注入  added by alongchen 2017-01-04 */
    	$query_sql = "select * from group_info where groupid = '". $groupid."' and liveuserid = '".$liveuserid ."' limit ". strval($start_row) . "," . strval($row_number);
    	try
    	{
    		$result = $this->session_->ExecuteSelectSql($query_sql);
    	
    		if(!empty($result))
    		{
    			foreach ($result as $list)
    			{
    				$one_record = array(
    						'userid' => $list['userid'],
    						'nickname' =>$list['nickname'],
    						'headpic' => $list['headpic']
    				);
    				array_push($group_list,$one_record);
    			}
    		}
    	}
    	catch (Exception $e)
    	{
    		$error_message = $e->getMessage();
    		mysql_log(ERROR, EC_OK, "get list error :" . $error_message );
    		return self::ERROR_CODE_DB_ERROR;
    	}
    	return self::ERROR_CODE_SUCCESSFUL;
    }
    public function GetGroupListCount($liveuserid,$groupid,&$info_count,&$error_message)
    {
		/*begin:转义字符串，防止sql注入  added by alongchen 2017-01-04 */
		$groupid = $this->session_->EscapeString($groupid);
		$liveuserid=$this->session_->EscapeString($liveuserid);
		/*end  :转义字符串，防止sql注入  added by alongchen 2017-01-04 */
		$query_sql = "select count(*) as all_count from group_info where groupid = '". $groupid."' and liveuserid = '".$liveuserid ."'";
		try
		{
			$count_result = $this->session_->ExecuteSelectSql($query_sql);
			$info_count = $count_result[0]['all_count'];
		}
		catch (Exception $e)
		{
			$error_message = $e->getMessage();
			mysql_log(ERROR, EC_OK, "get tape_count count error :" . $error_message );
			return self::ERROR_CODE_DB_ERROR;
		}
		return self::ERROR_CODE_SUCCESSFUL;
    }    

    public function deteteReportUserRecord($userid)
    {
    	$liveuserid=$this->session_->EscapeString($userid);
    	try
    	{
    		$delete_live_sql = "delete from live_data where userid = '" . $userid . "'";
    		$delete_tape_sql = "delete from tape_data where userid = '" . $userid . "'";
    		$this->session_->ExecuteSelectSql($delete_live_sql);
    		$this->session_->ExecuteSelectSql($delete_tape_sql);
    	}
    	catch (Exception $e)
    	{
    		$error_message = $e->getMessage();
    		mysql_log(ERROR, EC_OK, "deteteReportUserRecord error :" . $error_message );
    		return self::ERROR_CODE_DB_ERROR;
    	}
    	return self::ERROR_CODE_SUCCESSFUL;
    }    
}
?>
