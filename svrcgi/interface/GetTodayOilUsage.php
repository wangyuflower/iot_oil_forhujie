<?php
require_once dirname(__FILE__) . '/../common/Common.php';
require_once dirname(__FILE__) . '/../dao/iot/oil_usage.class.php';

class GetTodayOilUsage extends AbstractInterface
{
    public function initialize()
    {
        return true;
    }
    
    public function verifyInput(&$args)
    {
     //   $req = $args['interface']['para'];
    
        //$rules = array(
        //    'userid' => array('type' => 'string')
        //);
        //$this->_retValue = "GetTodayOilUsage verifyInput error";
    
        //return $this->_verifyInput($args, $rules);
        return true;
    }
    
    public function process()
    {
        interface_log(INFO, EC_OK,"GetTodayOilUsage args=" . var_export($this->_args, true));

        $this->_retValue = "GetTodayOilUsage process 1";

        $config = getConf('ROUTE.DB');
        
        
        $oil_usage = new oil_usage($config['HOST'], $config['PORT'], $config['USER'], $config['PASSWD'], $config['DBNAME']);
        $error_message = "";   

        $userid = $this->_args['userid'];
        if ($userid == "") {
            $userid = "hujie";
        }

        $ret = $oil_usage->getTodayOilUsage($userid, $result);

        if($ret != 0)
        {
            $this->_retValue =$ret;
            $this->_retMsg = 'GetTodayOilUsage::process() fail '.genErrMsg($this->_retValue);
            return false;
        }   
          
        $this->_retValue = EC_OK;
        $this->_data= $result;
        interface_log(INFO, EC_OK, 'GetTodayOilUsage::process() succeed');
        return true;
    }
}

?>
