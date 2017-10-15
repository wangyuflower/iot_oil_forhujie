<?php

include_once dirname(__FILE__).'/GlobalFunctions.php';

abstract class AbstractInterface {
	
	// common input parameters
	protected $_interfaceName;
	
	// common return parameters
	protected  $_retValue = 0;
	protected  $_retMsg = "";
	protected  $_data = array();
	
	// 请求参数列表para
	protected  $_args = array();


	/**
	 * @return the $_interfaceName
	 */
	public function getInterfaceName() {
		return $this->_interfaceName;
	}

	/**
	 * @return the $_retValue
	 */
	public function getRetValue() {
		return $this->_retValue;
	}


	/**
	 * @return the $_retMsg
	 */
	public function getRetMsg() {
		return $this->_retMsg;
	}

	/**
	 * @return the $_data
	 */
	public function getData() {
		return $this->_data;
	}

	/**
	 * @param field_type $_interfaceName
	 */
	public function setInterfaceName($_interfaceName) {
		$this->_interfaceName = $_interfaceName;
	}

	/**
	 * @param field_type $_retValue
	 */
	public function setRetValue($_retValue) {
		$this->_retValue = $_retValue;
	}


	/**
	 * @param field_type $_retMsg
	 */
	public function setRetMsg($_retMsg) {
		$this->_retMsg = $_retMsg;
	}

	/**
	 * @param field_type $_data
	 */
	public function setData($_data) {
		$this->_data = $_data;
	}
	
	/**
	 * 
	 * 加载需要用到的对象
	 */
	abstract public function initialize();
	/**
	 * 
	 * 输入校验
	 * @param array $args 输入参数
	 */
	abstract public function verifyInput(&$args);
	/**
	 * 
	 * 请求处理
	 */
	abstract public function process();
	
	public function renderOutput() {
		return json_encode (
			array(				
				"returnValue" => $this->_retValue, 
				"returnMsg" => genErrMsg($this->_retValue , $this->_retMsg),
				"returnData" => $this->_data
			)
		);
	}
	
	public function setLogAppIdAndChannelId($app_id, $channel_id = "") {
		MiniLog::instance(ROOT_PATH . "/log/")->setAppIdAndChannelId($app_id, $channel_id);
	}
	
// 	private function strFilter($str){
// 		$str = str_replace('<', '', $str);
// 		$str = str_replace('>', '', $str);		
// 		$str = str_replace('"', '', $str);
// 		$str = str_replace('&', '', $str);
// 		return trim($str);
// 	}
	protected function _verifyInput(&$args, $rules){
		$req = $args;
        $result = ParamChecker::getInstance()->checkParam($rules, $req);
        if (!$result['result']) {
            $this->_retValue = EC_INVALID_INPUT;
            $this->_retMsg = ParaStrFilter($result['msg']);      
            return false;
        }
        $keys = array_keys($rules);
        extract($req);
        $this->_args = compact($keys);
        
        if(isset($channel_id)) {
        	$this->setLogAppIdAndChannelId($app_id, $channel_id);	
        } else {
        	$this->setLogAppIdAndChannelId($app_id);
        }
        
        return true;
	}
	
// 	public function createDaoInstance($dao_class_name)
// 	{
// 		if (! class_exists ( $dao_class_name )) {
// 			return null;
// 		} else {
// 			$config = getConf('ROUTE.DB');
// 			return new $dao_class_name ($config['HOST'], $config['PORT'], $config['USER'], $config['PASSWD'], $config['DBNAME']);
// 		}
// 	}
}

?>
