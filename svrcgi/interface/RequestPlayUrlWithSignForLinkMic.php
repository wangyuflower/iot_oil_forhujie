<?php
require_once dirname(__FILE__) . '/../common/Common.php';
require_once dirname(__FILE__) . '/../conf/OutDefine.php';
class RequestPlayUrlWithSignForLinkMic extends AbstractInterface
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
            'originStreamUrl' => array('type' => 'string'),
        );

        return $this->_verifyInput($args, $rules);
    }

    public function process()
    {
        interface_log(INFO, EC_OK,"RequestPlayUrlWithSignForLinkMic args=" . var_export($this->_args, true));

        $bizid = APP_BIZID;
        $userid = $this->_args['userid'];
        $originalUrl = $this->_args['originStreamUrl'];
        $list = split('/', $originalUrl);
        $length = count($list);
        $url = split('\.', $list[$length - 1]);
        $now_time = time();
        $txTime = createTxTime($now_time);
        $safe_url = $originalUrl."?txSecret=" . ceatePushURLTxSecret($url[0],$txTime) ."&txTime=" .$txTime ."&bizid=".$bizid;
        $this->_retValue = EC_OK;
        $this->_data=array("streamUrlWithSignature" => $safe_url,"timestamp" => $now_time);
        interface_log(INFO, EC_OK, 'RequestPlayUrlWithSignForLinkMic::process() succeed');
        return true;
    }
}

?>
