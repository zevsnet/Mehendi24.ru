<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2014 Bitrix
 */

/********************************************************************
*	MySQLi database classes
********************************************************************/
class CDatabase extends CDatabaseMysql
{
	/** @var mysqli */
	var $db_Conn;

	public function ConnectInternal()
	{
		$dbHost = $this->DBHost;
		$dbPort = null;
		if (($pos = strpos($dbHost, ":")) !== false)
		{
			$dbPort = intval(substr($dbHost, $pos + 1));
			$dbHost = substr($dbHost, 0, $pos);
		}

		$persistentPrefix = (DBPersistent && !$this->bNodeConnection? "p:" : "");

		$this->db_Conn = mysqli_connect($persistentPrefix.$dbHost, $this->DBLogin, $this->DBPassword, $this->DBName, $dbPort);

		if(!$this->db_Conn)
		{
			$error = "[".mysqli_connect_errno()."] ".mysqli_connect_error();
			if($this->debug || (isset($_SESSION["SESS_AUTH"]["ADMIN"]) && $_SESSION["SESS_AUTH"]["ADMIN"]))
				echo "<br><font color=#ff0000>Error! mysqli_connect()</font><br>".$error."<br>";

			SendError("Error! mysqli_connect()\n".$error."\n");

			return false;
		}

		return true;
	}

	protected function QueryInternal($strSql)
	{
		return mysqli_query($this->db_Conn, $strSql, MYSQLI_STORE_RESULT);
	}

	protected function GetError()
	{
		return "[".mysqli_errno($this->db_Conn)."] ".mysqli_error($this->db_Conn);
	}

	protected function DisconnectInternal($resource)
	{
		mysqli_close($resource);
	}

	
	/**
	* <p>Метод возвращает ID последней вставленной записи. Динамичный метод.</p> <p> </p>
	*
	*
	* @return int 
	*
	* <h4>Example</h4> 
	* <pre>
	* &lt;?
	* function AddResultAnswer($arFields)
	* {
	* 	$err_mess = (CForm::err_mess())."&lt;br&gt;Function: AddResultAnswer&lt;br&gt;Line: ";
	* 	global $DB;
	* 	$arInsert = $DB-&gt;PrepareInsert("b_form_result_answer", $arFields, "form");
	* 	$strSql = "INSERT INTO b_form_result_answer (".$arInsert[0].") VALUES (".$arInsert[1].")";
	* 	$DB-&gt;Query($strSql, false, $err_mess.__LINE__);
	* 	return intval(<b>$DB-&gt;LastID()</b>);
	* }
	* ?&gt;
	* </pre>
	*
	*
	* <h4>See Also</h4> 
	* <ul><li><a href="http://dev.1c-bitrix.ru/api_help/main/reference/cdatabase/query.php">CDatabase::Query</a></li></ul><a
	* name="examples"></a>
	*
	*
	* @static
	* @link http://dev.1c-bitrix.ru/api_help/main/reference/cdatabase/lastid.php
	* @author Bitrix
	*/
	public function LastID()
	{
		$this->DoConnect();
		return mysqli_insert_id($this->db_Conn);
	}

	
	/**
	* <p>Подготавливает строку (заменяет кавычки и прочее) для вставки в SQL запрос. Если задан параметр <i>max_length</i>, то также обрезает строку до длины <i>max_length</i>. Динамичный метод.</p> <p> </p>
	*
	*
	* @param string $value  Исходная строка.
	*
	* @param int $max_length = 0 Максимальная длина. <br>Необязательный. По умолчанию - "0" (строка не
	* обрезается).
	*
	* @return string 
	*
	* <h4>Example</h4> 
	* <pre>
	* &lt;?
	* $strSql = "
	*     SELECT 
	*         ID 
	*     FROM 
	*         b_stat_phrase_list 
	*     WHERE 
	*         PHRASE='".<b>$DB-&gt;ForSql</b>($search_phrase)."' 
	*     and SESSION_ID='".$_SESSION["SESS_SESSION_ID"]."'
	*     ";
	* $w = $DB-&gt;Query($strSql, false, $err_mess.__LINE__);
	* ?&gt;
	* </pre>
	*
	*
	* <h4>See Also</h4> 
	* <ul> <li> <a href="http://dev.1c-bitrix.ru/api_help/main/reference/cdatabase/query.php">CDatabase::Query</a> </li> <li>
	* <a href="http://dev.1c-bitrix.ru/api_help/main/reference/cdatabase/update.php">CDatabase::Update</a> </li> <li> <a
	* href="http://dev.1c-bitrix.ru/api_help/main/reference/cdatabase/insert.php">CDatabase::Insert</a> </li> </ul><a
	* name="examples"></a>
	*
	*
	* @static
	* @link http://dev.1c-bitrix.ru/api_help/main/reference/cdatabase/forsql.php
	* @author Bitrix
	*/
	public static function ForSql($strValue, $iMaxLength = 0)
	{
		if ($iMaxLength > 0)
			$strValue = substr($strValue, 0, $iMaxLength);

		if (!is_object($this) || !$this->db_Conn)
		{
			global $DB;
			$DB->DoConnect();
			return mysqli_real_escape_string($DB->db_Conn, $strValue);
		}
		else
		{
			$this->DoConnect();
			return mysqli_real_escape_string($this->db_Conn, $strValue);
		}
	}

	public static function ForSqlLike($strValue, $iMaxLength = 0)
	{
		if ($iMaxLength > 0)
			$strValue = substr($strValue, 0, $iMaxLength);

		if(!is_object($this) || !$this->db_Conn)
		{
			global $DB;
			$DB->DoConnect();
			return mysqli_real_escape_string($DB->db_Conn, str_replace("\\", "\\\\", $strValue));
		}
		else
		{
			$this->DoConnect();
			return mysqli_real_escape_string($this->db_Conn, str_replace("\\", "\\\\", $strValue));
		}
	}

	public function GetTableFields($table)
	{
		if(!isset($this->column_cache[$table]))
		{
			$this->column_cache[$table] = array();
			$this->DoConnect();

			$dbResult = $this->query("SELECT * FROM `".$this->ForSql($table)."` LIMIT 0");

			$resultFields = mysqli_fetch_fields($dbResult->result);
			foreach ($resultFields as $field)
			{
				switch($field->type)
				{
					case MYSQLI_TYPE_TINY:
					case MYSQLI_TYPE_SHORT:
					case MYSQLI_TYPE_LONG:
					case MYSQLI_TYPE_INT24:
					case MYSQLI_TYPE_CHAR:
						$type = "int";
						break;

					case MYSQLI_TYPE_DECIMAL:
					case MYSQLI_TYPE_NEWDECIMAL:
					case MYSQLI_TYPE_FLOAT:
					case MYSQLI_TYPE_DOUBLE:
						$type = "real";
						break;

					case MYSQLI_TYPE_DATETIME:
					case MYSQLI_TYPE_TIMESTAMP:
						$type = "datetime";
						break;

					case MYSQLI_TYPE_DATE:
					case MYSQLI_TYPE_NEWDATE:
						$type = "date";
						break;

					default:
						$type = "string";
						break;
				}

				$this->column_cache[$table][$field->name] = array(
					"NAME" => $field->name,
					"TYPE" => $type,
				);
			}
		}
		return $this->column_cache[$table];
	}

	protected function getThreadId()
	{
		return mysqli_thread_id($this->db_Conn);
	}
}

class CDBResult extends CDBResultMysql
{
	public static function CDBResult($res = null)
	{
		parent::CDBResultMysql($res);
	}

	protected function FetchRow()
	{
		return mysqli_fetch_assoc($this->result);
	}

	
	/**
	* <p>Метод возвращает количество выбранных записей (выборка записей осуществляется с помощью SQL-команды "SELECT ..."). Динамичный метод.</p> <p class="note"><b>Примечание</b>. Для Oracle версии данный метод будет корректно работать только после вызова <a href="http://dev.1c-bitrix.ru/api_help/main/reference/cdbresult/navstart.php">CDBResult::NavStart</a>, либо если достигнут конец (последняя запись) выборки.</p>
	*
	*
	* @return int 
	*
	* <h4>Example</h4> 
	* <pre>
	* &lt;?
	* $rsBanners = CAdvBanner::GetList($by, $order, $arFilter, $is_filtered);
	* $rsBanners-&gt;NavStart(20);
	* if (intval(<b>$rsBanners-&gt;SelectedRowsCount()</b>)&gt;0):
	*     echo $rsBanners-&gt;NavPrint("Баннеры");
	*     while($rsBanners-&gt;NavNext(true, "f_")):
	*          echo "[".$f_ID."] ".$f_NAME."&lt;br&gt;";
	*     endwhile;
	*     echo $rsBanners-&gt;NavPrint("Баннеры");
	* endif;
	* ?&gt;
	* </pre>
	*
	*
	* <h4>See Also</h4> 
	* <ul><li> <a
	* href="http://dev.1c-bitrix.ru/api_help/main/reference/cdbresult/affectedrowscount.php">CDBResult::AffectedRowsCount</a>
	* </li></ul><a name="examples"></a>
	*
	*
	* @static
	* @link http://dev.1c-bitrix.ru/api_help/main/reference/cdbresult/selectedrowscount.php
	* @author Bitrix
	*/
	public function SelectedRowsCount()
	{
		if($this->nSelectedCount !== false)
			return $this->nSelectedCount;

		if(is_object($this->result))
			return mysqli_num_rows($this->result);
		else
			return 0;
	}

	
	/**
	* <p>Метод возвращает количество записей, измененных SQL-командами <b>INSERT</b>, <b>UPDATE</b> или <b>DELETE</b>. Динамичный метод.</p> <br>
	*
	*
	* @return int 
	*
	* <h4>Example</h4> 
	* <pre>
	* &lt;?
	* $strSql = "
	* 	INSERT INTO b_stat_day(
	* 		ID,
	* 		DATE_STAT,
	* 		TOTAL_HOSTS)
	* 	SELECT
	* 		SQ_B_STAT_DAY.NEXTVAL,
	* 		trunc(SYSDATE),
	* 		nvl(PREV.MAX_TOTAL_HOSTS,0)
	* 	FROM
	* 		(SELECT	max(TOTAL_HOSTS) AS MAX_TOTAL_HOSTS	FROM b_stat_day) PREV						
	* 	WHERE			
	* 		not exists(SELECT 'x' FROM b_stat_day D WHERE TRUNC(D.DATE_STAT) = TRUNC(SYSDATE))
	* 	";
	* $q = $DB-&gt;Query($strSql, true, $err_mess.__LINE__);
	* if ($q &amp;&amp; intval(<b>$q-&gt;AffectedRowsCount</b>())&gt;0)
	* {
	* 	$arFields = Array("LAST"=&gt;"'N'");
	* 	$DB-&gt;Update("b_stat_adv_day",$arFields,"WHERE LAST='Y'", $err_mess.__LINE__);
	* 	$DB-&gt;Update("b_stat_adv_event_day",$arFields,"WHERE LAST='Y'", $err_mess.__LINE__);
	* 	$DB-&gt;Update("b_stat_searcher_day",$arFields,"WHERE LAST='Y'", $err_mess.__LINE__);
	* 	$DB-&gt;Update("b_stat_event_day",$arFields,"WHERE LAST='Y'", $err_mess.__LINE__);
	* 	$DB-&gt;Update("b_stat_country_day",$arFields,"WHERE LAST='Y'", $err_mess.__LINE__);
	* 	$DB-&gt;Update("b_stat_guest",$arFields,"WHERE LAST='Y'",$err_mess.__LINE__);
	* 	$DB-&gt;Update("b_stat_session",$arFields,"WHERE LAST='Y'",$err_mess.__LINE__);
	* }
	* ?&gt;
	* </pre>
	*
	*
	* <h4>See Also</h4> 
	* <ul><li> <a
	* href="http://dev.1c-bitrix.ru/api_help/main/reference/cdbresult/selectedrowscount.php">CDBResult::SelectedRowsCount</a>
	* </li></ul><a name="examples"></a>
	*
	*
	* @static
	* @link http://dev.1c-bitrix.ru/api_help/main/reference/cdbresult/affectedrowscount.php
	* @author Bitrix
	*/
	public static function AffectedRowsCount()
	{
		if(is_object($this) && is_object($this->DB))
		{
			/** @noinspection PhpUndefinedMethodInspection */
			$this->DB->DoConnect();
			return mysqli_affected_rows($this->DB->db_Conn);
		}
		else
		{
			global $DB;
			$DB->DoConnect();
			return mysqli_affected_rows($DB->db_Conn);
		}
	}

	
	/**
	* <p>Метод возвращает количество полей результата выборки. Динамичный метод.</p>
	*
	*
	* @return int 
	*
	* <h4>Example</h4> 
	* <pre>
	* &lt;?
	* $rs = $DB-&gt;Query($query,true);
	* $intNumFields = <b>$rs-&gt;FieldsCount</b>();
	* $i = 0;
	* while ($i &lt; $intNumFields) 
	* {
	* 	$arFieldName[] = $rs-&gt;FieldName($i);
	* 	$i++;
	* }
	* ?&gt;
	* </pre>
	*
	*
	* <h4>See Also</h4> 
	* <ul><li> <a href="http://dev.1c-bitrix.ru/api_help/main/reference/cdbresult/fieldname.php">CDBResult::FieldName</a>
	* </li></ul><a name="examples"></a>
	*
	*
	* @static
	* @link http://dev.1c-bitrix.ru/api_help/main/reference/cdbresult/fieldscount.php
	* @author Bitrix
	*/
	public function FieldsCount()
	{
		if(is_object($this->result))
			return mysqli_num_fields($this->result);
		else
			return 0;
	}

	
	/**
	* <p>Метод возвращает название поля по его номеру. Динамичный метод.</p>
	*
	*
	* @param int $column  
	*
	* @return mixed 
	*
	* <h4>Example</h4> 
	* <pre>
	* &lt;?
	* $rs = $DB-&gt;Query($query,true);
	* $intNumFields = $rs-&gt;FieldsCount();
	* $i = 0;
	* while ($i &lt; $intNumFields) 
	* {
	* 	$arFieldName[] = <b>$rs-&gt;FieldName</b>($i);
	* 	$i++;
	* }
	* ?&gt;
	* </pre>
	*
	*
	* <h4>See Also</h4> 
	* <ul><li> <a href="http://dev.1c-bitrix.ru/api_help/main/reference/cdbresult/fieldscount.php">CDBResult::FieldsCount</a>
	* </li></ul><a name="examples"></a>
	*
	*
	* @static
	* @link http://dev.1c-bitrix.ru/api_help/main/reference/cdbresult/fieldname.php
	* @author Bitrix
	*/
	public function FieldName($iCol)
	{
		$fieldInfo = mysqli_fetch_field_direct($this->result, $iCol);
		return $fieldInfo->name;
	}

	public function DBNavStart()
	{
		global $DB;

		//total rows count
		if(is_object($this->result))
			$this->NavRecordCount = mysqli_num_rows($this->result);
		else
			return;

		if($this->NavRecordCount < 1)
			return;

		if($this->NavShowAll)
			$this->NavPageSize = $this->NavRecordCount;

		//calculate total pages depend on rows count. start with 1
		$this->NavPageCount = floor($this->NavRecordCount/$this->NavPageSize);
		if($this->NavRecordCount % $this->NavPageSize > 0)
			$this->NavPageCount++;

		//page number to display. start with 1
		$this->NavPageNomer = ($this->PAGEN < 1 || $this->PAGEN > $this->NavPageCount? ($_SESSION[$this->SESS_PAGEN] < 1 || $_SESSION[$this->SESS_PAGEN] > $this->NavPageCount? 1:$_SESSION[$this->SESS_PAGEN]):$this->PAGEN);

		//rows to skip
		$NavFirstRecordShow = $this->NavPageSize * ($this->NavPageNomer-1);
		$NavLastRecordShow = $this->NavPageSize * $this->NavPageNomer;

		if($this->SqlTraceIndex)
			$start_time = microtime(true);

		mysqli_data_seek($this->result, $NavFirstRecordShow);

		$temp_arrray = array();
		for($i=$NavFirstRecordShow; $i<$NavLastRecordShow; $i++)
		{
			if(($res = $this->FetchInternal()))
			{
				$temp_arrray[] = $res;
			}
			else
			{
				break;
			}
		}

		if($this->SqlTraceIndex)
		{
			/** @noinspection PhpUndefinedVariableInspection */
			$exec_time = round(microtime(true) - $start_time, 10);
			$DB->addDebugTime($this->SqlTraceIndex, $exec_time);
			$DB->timeQuery += $exec_time;
		}

		$this->arResult = $temp_arrray;
	}
}
