<?php
require_once dirname(__FILE__) . '/../common/Common.php';
require_once dirname(__FILE__) . '/../conf/OutDefine.php';

class GetVodSignature extends AbstractInterface
{
    public function initialize()
    {
        return true;
    }
    
    public function verifyInput(&$args)
    {
        $req = $args['interface']['para'];
    
        $rules = array(
        );
    
        return $this->_verifyInput($args, $rules);
    }
    
    public function process()
    {
        interface_log(INFO, EC_OK,"GetVodSignature args=" . var_export($this->_args, true));        

        $current = time();
        $expired = $current + 86400;

        $error_message = "";       

        $file_sha = $this->_args['file_sha'];
        $procedure = "XIAOZHIBO-DEFAULT";

        $arg_list = array(
            "s" => strval(CLOUD_API_SECRETID),
            "t" => $current,
            "e" => $expired,
            "p" => $procedure,
            "r" => rand());

        $orignal = http_build_query($arg_list);
        $signature = base64_encode(hash_hmac('SHA1', $orignal, strval(CLOUD_API_SECRETKEY), true).$orignal);

        $this->_retValue = EC_OK;
	$this->_data=array("signature" => $signature);
        interface_log(INFO, EC_OK, 'GetVodSignature::process() succeed');
        return true;
    }
}

?>
