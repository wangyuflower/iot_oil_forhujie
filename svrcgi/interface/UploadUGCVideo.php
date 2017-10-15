<?php
require_once dirname(__FILE__) . '/../common/Common.php';
require_once dirname(__FILE__) . '/../dao/dao_live/dao_live.class.php';
require_once dirname(__FILE__) . '/../conf/OutDefine.php';
class UploadUGCVideo extends AbstractInterface
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
        	'file_id'=>array('type' => 'string'),
        	'play_url'=>array('type' => 'string'),
        	'title' => array('type' => 'string'),
        	'userinfo' => array('type' => 'object',
        	                    "items"=>array(),"nullable"=>true, "emptyable"=>true)
        );

        return $this->_verifyInput($args, $rules);
    }

    public function process()
    {
        interface_log(INFO, EC_OK,"RequstLVBAddr args=" . var_export($this->_args, true));



        $config = getConf('ROUTE.DB');


        $dao_live = new dao_live($config['HOST'], $config['PORT'], $config['USER'], $config['PASSWD'], $config['DBNAME']);
        $error_message = "";

//         $bizid = APP_BIZID;
         $userid = $this->_args['userid'];
//         $tmp_id = str_replace(array("@","#","-"),"_",$userid);
//         $live_code = $bizid . "_" . $tmp_id ;
//         $play_url = "http://" . $bizid . ".liveplay.myqcloud.com/live/" .  $live_code . ".flv";
     
//         $safe_url = "&txSecret=" . ceatePushURLTxSecret($live_code,$txTime) ."&txTime=" .$txTime;
//         interface_log(INFO,EC_OK,$safe_url . ":" . ":" .$txTime);
//         $push_url = "rtmp://" . $bizid . ".livepush2.myqcloud.com/live/" .  $live_code . "?bizid=" . $bizid . "&record_interval=10800&record=flv|hls" .$safe_url;
//         $hls_play_url = "http://" . $bizid . ".liveplay.myqcloud.com/live/" .  $live_code . ".m3u8";


    	$ret = $dao_live->AddUGCData($userid, $this->_args['file_id'],$this->_args['play_url'], $this->_args['title'],$this->_args['userinfo']);
    	if($ret != 0)
    	{
    		$this->_retValue =$ret;
    		$error_message="db error:no permission";
    		$this->_retMsg = 'RequstLVBAddr::process() fail '.genErrMsg($this->_retValue);
    		return false;
    	}

        $this->_retValue = EC_OK;
        //$this->_data=array("pushurl" => $push_url,"timestamp" => $now_time);
        interface_log(INFO, EC_OK, 'RequstLVBAddr::process() succeed');
        return true;
    }
}

?>
