<?php
require_once dirname(__FILE__) . '/../common/Common.php';
require_once dirname(__FILE__) . '/../dao/dao_live/dao_live.class.php';
require_once dirname(__FILE__) . '/../conf/OutDefine.php';
class ReportUser extends AbstractInterface
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
        	'hostuserid'=>array('type' => 'string')
        );
    
        return $this->_verifyInput($args, $rules);
    }
    
    public function process()
    {
        interface_log(INFO, EC_OK,"ReportUser args=" . var_export($this->_args, true));
        

        $config = getConf('ROUTE.DB'); 
        $dao_live = new dao_live($config['HOST'], $config['PORT'], $config['USER'], $config['PASSWD'], $config['DBNAME']);
        $dao_live->deteteReportUserRecord($this->_args['userid']);
        
        $this->_retValue = EC_OK;
        $this->_data=array();
        interface_log(INFO, EC_OK, 'ReportUser::process() succeed');
        return true;
    }
}

?>
