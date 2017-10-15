<?php
require_once dirname(__FILE__) . '/../common/Common.php';
require_once dirname(__FILE__) . '/../conf/OutDefine.php';
class RequestLVBAddrForLinkMic extends AbstractInterface
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
        	'title' => array('type' => 'string'),
        	'userinfo' => array('type' => 'object',
        	                    "items"=>array(),"nullable"=>true, "emptyable"=>true)
        );
        return $this->_verifyInput($args, $rules);
    }
    public function process()
    {
        interface_log(INFO, EC_OK,"RequestLVBAddrForLinkMic args=" . var_export($this->_args, true));

        $bizid = APP_BIZID;
        $userid = $this->_args['userid'];
        $tmp_id = str_replace(array("@","#","-"),"_",$userid);
        $now_time = time();
        $txTime = createTxTime($now_time);
        $live_code = $bizid . "_" . $tmp_id . "_" . $txTime;
        $play_url = "http://" . $bizid . ".liveplay.myqcloud.com/live/" .  $live_code . ".flv";
        $safe_url = "&txSecret=" . ceatePushURLTxSecret($live_code,$txTime) ."&txTime=" .$txTime;
        $push_url = "rtmp://" . $bizid . ".livepush2.myqcloud.com/live/" .  $live_code . "?bizid=" . $bizid . "&record_interval=10800&record=flv|hls" .$safe_url;
        $play_url = "rtmp://" . $bizid . ".liveplay.myqcloud.com/live/".$live_code;

        $this->_retValue = EC_OK;
        $this->_data=array("pushurl" => $push_url,"timestamp" => $now_time, "playurl" => $play_url);
        interface_log(INFO, EC_OK, 'RequestLVBAddrForLinkMic::process() succeed');
        return true;
    }
}

?>
