<?php
require_once dirname(__FILE__) . '/../common/Common.php';
require_once dirname(__FILE__) . '/../dao/dao_live/dao_live.class.php';
require_once dirname(__FILE__) . '/../common/GlobalDefine.php';
require_once dirname(__FILE__) . '/../conf/OutDefine.php';


class GetCOSSign extends AbstractInterface
{
    public function initialize()
    {
        return true;
    }
    
    public function verifyInput(&$args)
    {
        return true;
    }
    
    public function process()
    {
        interface_log(INFO, EC_OK,"GetCOSSign args=" . var_export($this->_args, true)); 
   
        $currentTime = time();
        $expiredTime = $currentTime + COSKEY_EXPIRED_TIME;
        
        $srcStr = 'a=' . strval(COSKEY_APPID) .
        '&b=' . COSKEY_BUCKET .
        '&k=' . COSKEY_SECRECTID .
        '&e=' . $expiredTime .
        '&t=' . $currentTime .
        '&r=' .rand() .
        '&f=';
        $signStr = base64_encode(hash_hmac('SHA1', $srcStr, COSKEY_SECRECTKEY, true). $srcStr);

  		
        $this->_retValue = EC_OK;
        $this->_data=array("sign"=>$signStr);
        interface_log(INFO, EC_OK, 'GetCOSSign::process() succeed.' .  $srcStr . '::' . $signStr);        
        return true;
    }
    
}

?>
