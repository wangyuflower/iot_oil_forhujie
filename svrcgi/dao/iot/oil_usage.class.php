<?php
require_once dirname(__FILE__).'/../dao_base/dao.class.php';

class oil_usage extends Dao
{
    public function getTodayOilUsage($userid,&$result)
    {
        //$query_sql = "select stream_id from oil_usage where '" . $userid . "'";
        $query_sql = "SELECT usrid, sum(oiluse) dayuse FROM `oil_usage` WHERE usrid='" . $userid . "' and time>curdate() and time<DATE_SUB(CURDATE(),INTERVAL -1 DAY)";
        try
        {
            $result = $this->session_->ExecuteSelectSql($query_sql);

        }
        catch (Exception $e)
        {
            $error_message = $e->getMessage();
            mysql_log(ERROR, EC_OK, "get today oil_usage  error :" . $error_message );
            return self::ERROR_CODE_DB_ERROR;
        }
        //$result = $query_sql;
        return self::ERROR_CODE_SUCCESSFUL;
    }
}
?>
