<?php

namespace kernel\libs;
use \Exception;

/**
 *  Diese Klasse ist ein OpenSource Projekt von Jannick Bruhns - www.brunick.de
 *  Dokumentation ist teilweise Ausstehend
 *
 *	PHP versions 5.2+
 *
 *	LICENSE:
 *	@category   MySQL
 *	@package    Wari3
 *	@copyright  2011 Jannick Bruhns
 * 	@license	GNU GPLv3
 *	@see        project Wari
 *	@date		$Date$
 *	@author     $Author$
 *	@revision 	$Revision$
 *	@id			$Id$
 */

class xSql {

	/**
	*	Beinhaltet die Aktive Verbindung
	*	@var		mixed	$con
	*	@access 	public
	*	@author 	Jannick Bruhns <info@brunick.de>
	*	@version 	1.0-Alpha
	*	@since 		1.0-Alpha
	*/

	protected  $con = false;

	/**
	*	Verbindungs-Daten in einem Array, per SetCon Data setzen oder per auto-load
	*	@var		array	$dat
	*	@access 	public
	*	@author 	Jannick Bruhns <info@brunick.de>
	*	@version 	1.0-Alpha
	*	@since 		1.0-Alpha
	*/

	private $dat = array();

	/**
	*	Lokale-Standard-Datenbank
	*	@var		string 		$datDb
	*	@access 	public
	*	@author 	Jannick Bruhns <info@brunick.de>
	*	@version 	1.0-Alpha
	*	@since 		1.0-Alpha
	*/

	private  $datDb = '';

	public $sql_counter = 0;
    public static $connection_count = 0;

	private $debug = false;

    /**
     *    CMS
     *    Konstruktor, also Start der Klasse, f�gt einige wichtige Werte hinzu
     * @param string $db
     * @throws Exception
     * @access    public
     * @version    1.0-Alpha
     * @since        1.0-Alpha
     * @author    Jannick Bruhns <info@brunick.de>
     */
	function __construct($connect_data, $db) {

        if(!empty($connect_data) && !empty($db)) {
            $this->setConData($connect_data,$db['name']);
            /*
            if ($_SESSION['usr_right_table']['globe']['sql_fehler'] > 0) {
                $this->debug = true;
            }
            */
        } else {
            throw new Exception('Keine MySQL Verbindungsdaten!');
        }
	}

	/**
	*	CMS
	*	Destruktor
	*	@access 	public
	*	@author 	Jannick Bruhns <info@brunick.de>
	*	@version 	1.0-Alpha
	*	@since 		1.0-Alpha
	*/

	function __destructor() {
		$this->_disconnect();
	}


	public function setConData($data,$db) {
		try {
			if(!empty($data) && is_array($data)) {
				$this->dat = $data;
				$this->datDb = $db;
				$this->_connect();
			}
		} catch (Exception $e) {
			echo $e->getMessage() . '<br>';
		}
	}

	public function _setDebugMode($bool) {
		$this->debug = $bool;
	}

	public function qss_c($sql)
	{ 
		$cachekey = "qss_c".md5($sql);
		$return = $this->get_cache($cachekey);
		if ($return === false) {
			$return = $this->qss($sql);
			$this->set_cache($cachekey, $return);
		}
		return $return; 
	}

	/**
	*	CMS
	*	Single String Query
	*	@access 	public
	*	@author 	Jan Engelhardt
	*	@version 	1.0-Alpha
	*	@since 		1.0-Alpha
	*/
  public function qss($query)
  {
		try {
			$resource = $this->_query_sql($query);
			if($resource) {
				$return = @mysqli_fetch_row($resource);
			} else {
				throw new Exception ('QSA Fehlgeschlagen');
			}
			return $return[0];
		} catch (Exception $e) {
			$this->error($e,"#qss\n$query");
			if($this->debug) {
				echo '<pre>';
				debug_print_backtrace();
				echo mysqli_error($this->con);
				echo $e->getMessage() . '<br>';
				echo '</pre>';
				die();
			}
		}
  }


	/**
	*	CMS
	*	Single Access Query
	*	@see qsa() -> alias for query_single_array
	*	@access 	public
	*	@author 	Jannick Bruhns <info@brunick.de>
	*	@version 	1.0-Alpha
	*	@since 		1.0-Alpha
	*/

	public function query_single_array($query) {
		try {
			$resource = $this->_query_sql($query);
			if($resource) {
				$return = mysqli_fetch_assoc($resource);
			} else {
				throw new Exception ('QSA Fehlgeschlagen');
			}
			return $return;
		} catch (Exception $e) {
			$this->error($e,"#query_single_array\n$query");
			/*
			if($this->debug) {
				echo '<pre>';
				debug_print_backtrace();
				echo mysqli_error($this->con);
				echo $e->getMessage() . '<br>';
				echo '</pre>';
				die();
			}
			*/
		}
	}

	public function qsa($q) {
		return $this->query_single_array($q);
	}

	/**
	*	CMS
	*	Query and return an Array
	*	@see qa() -> alias for query_array
	*	@access 	public
	*	@author 	Jannick Bruhns <info@brunick.de>
	*	@version 	1.0-Alpha
	*	@since 		1.0-Alpha
	*/

	public function query_array($query) {
		try {
			$resource = $this->_query_sql($query);
			if($resource) {
				$return = $this->_resourceToArray($resource);
			} else {
				throw new Exception ('QA Fehlgeschlagen');
			}

			return $return;
		} catch (Exception $e) {
			$this->error($e,"#query_array\n$query");
			/*
			if($this->debug) {
				echo '<pre>';
				debug_print_backtrace();
				echo mysqli_error($this->con);
				echo $e->getMessage() . '<br>';
				echo '</pre>';
				die();
			}
			*/
		}
	}

	public function qa($q) {
		return $this->query_array($q);
	}

	/**
	*	CMS
	*	Query and return the resource
	*	@see q() -> alias for query
	*	@access 	public
	*	@author 	Jannick Bruhns <info@brunick.de>
	*	@version 	1.0-Alpha
	*	@since 		1.0-Alpha
	*/

	public function query($query) {
		try {
			$resource = $this->_query_sql($query);
			if($resource) {
				return $resource;
			} else {
				throw new Exception ('Query Fehlgeschlagen');
			}
		} catch (Exception $e) {
			$this->error($e,"#query\n$query");
			/*
			if($this->debug) {
				echo '<pre>';
				debug_print_backtrace();
				echo mysqli_error($this->con);
				echo $e->getMessage() . '<br>';
				echo '</pre>';
				die();
			}
			*/
		}
	}

	public function q($q) {
		return $this->query($q);
	}

	/**
	*	CMS
	*	Returns the Last ID
	*	@access 	private
	*	@author 	Jannick Bruhns <info@brunick.de>
	*	@version 	1.0-Alpha
	*	@since 		1.0-Alpha
	*/

	public function lastId() {
		$id = mysqli_insert_id($this->con);
		return $id;
	}

	public function qis($q) {
		return $this->mysqli_insert_single($q);
	}

	public function mysqli_insert_single($sql)
	{
		$t = time();
		$ret = $this->query($sql);
		$insert_id = mysqli_insert_id($this->con);
		return $insert_id;
	}

	public function mysqli_update($array,$cond,$table,$replace = true) {
		try {
			$query = 'UPDATE ' . $table . ' SET ';
			foreach ($array as $key=>$val) {
				if($replace == true) {
					$query .=' ' . mysqli_real_escape_string($this->con,$key) . ' = "'.@mysqli_real_escape_string($this->con,$val) . '",';
				} else {
					$query .=' ' . ($key) . ' = "'.($val) . '",';
				}
			}
			$query = substr($query,0,-1);
			$query .=' WHERE ';
			foreach($cond as  $key=>$val) {
				if($replace == true) {
					$query .= $key .' = "'.mysqli_real_escape_string($this->con,$val).'" AND ';
				} else {
					$query .= $key .' = "'.($val).'" AND ';
				}
			}
			$query = substr($query,0,-4);
			return $this->q($query);

		} catch (Exception $e) {
			$this->error($e,"#mysqli_update\n$query");
			/*
			if($this->debug) {
				echo '<pre>';
				debug_print_backtrace();
				echo mysqli_error($this->con);
				echo $e->getMessage() . '<br>';
				echo '</pre>';
				die();
			}
			*/
		}
	}

	public function mu($array,$cond,$table,$replace = true) {
		return $this->mysqli_update($array,$cond,$table,$replace);
	}

	/**
	*	Einf�gen neuer Eintr�ge
	*	@param 	array 	$array 		Array mit den Daten die eingef�gt werden m�ssen
	*	@param  string  $table      Die Tabelle in die Eingef�gt werden soll
	*   @param  bool    $ignore     Boolean f�r "INSERT IGNORE"
	*   @param  bool    $escape     Boolean f�r escapen der Input-Daten
	*   @return int     Die ID des letzten Eintrags
	*/

	public function mysqli_insert($array,$table,$ignore = true,$escape = true) {
		try {
			if($ignore === true) {
				$query = 'INSERT IGNORE INTO ' . $table  . ' (';
			} else {
				$query = 'INSERT INTO ' . $table  . ' (';
			}

			foreach ($array as $key=>$val) {
				$query .= '`'.$key . '`,';
			}
			$query = substr($query,0,-1) . ') VALUES (';

			foreach ($array as $key=>$val) {
				if($escape === true) {
					$query .= '"' . @mysqli_real_escape_string($this->con,$val) . '",';
				} else{
					$query .= '"' . $val . '",';
				}

			}
			$query = substr($query,0,-1) . ')';
			$this->q($query);
			$id = $this->lastId();
			if(!empty($id)) {
				return $this->lastId();
			} else {
				return false;
			}

		} catch (Exception $e) {
			$this->error($e,"#mysqli_insert\n$query");
			/*
			if($this->debug) {
				echo '<pre>';
				debug_print_backtrace();
				echo mysqli_error($this->con);
				echo $e->getMessage() . '<br>';
				echo '</pre>';
				die();
			}
			*/
		}
	}

	public function mysqli_multiple_insert($array,$table,$dpl=false) {
		try {
			$query = 'INSERT IGNORE INTO ' . $table  . ' (';
			if(!is_array($array)) {
				return false;
			}
			foreach (current($array) as $key=>$val) {
				$query .= $key . ',';
			}
			$query = substr($query,0,-1) . ') VALUES ';
			$count = 0;
			foreach($array as $key=>$val) {
				if($count < 2000) {
					$query .='(';
					foreach ($val as $ke=>$va) {
						$query .= '"' . mysqli_real_escape_string($this->con,$va) . '",';
					}
					$query = substr($query,0,-1) . ') , ';
					unset($array[$key]);
					$count++;
				} else {
					break;
				}
			}
			$query = substr($query,0,-2);
			if($dpl == true) {
				$query .= 'ON DUPLICATE KEY UPDATE';
			}
			$this->q($query);
			if(!empty($array)) {
				$this->mmi($array,$table);
			}

			return $this->lastId();

		} catch (Exception $e) {
			$this->error($e,"#mysqli_multiple_insert\n$query");
			/*
			if($this->debug) {
				echo '<pre>';
				debug_print_backtrace();
				echo mysqli_error($this->con);
				echo $e->getMessage() . '<br>';
				echo '</pre>';
				die();
			}
			*/
		}
	}

	public function mmi($array,$table,$dpl=false) {
		return $this->mysqli_multiple_insert($array,$table,$dpl);
	}

	public function mi($array,$table,$ignore = true,$escape = true) {
		return $this->mysqli_insert($array,$table,$ignore,$escape);
	}

	public function merrno() {
		return mysqli_errno($this->con);
	}

	public function merr() {
		return $this->error;
	}

	public function mGetCount() {
		return mysqli_affected_rows($this->con);
	}

	/**
	*	CMS
	*	_resourceToArray fetches all items as an associative array
	*	@access 	private
	*	@author 	Jannick Bruhns <info@brunick.de>
	*	@version 	1.0-Alpha
	*	@since 		1.0-Alpha
	*/

	private function _resourceToArray($resource) {
		$return = null;
		while ($row = mysqli_fetch_assoc($resource)) {
			$return[] = $row;
		}
		return $return;
	}

	function sql_add_backtrace($sql)
	{

		$bt = debug_backtrace();
		$caller = array_shift($bt);
		if (strstr($caller["file"], "db.class.php"))
		{
			$caller = array_shift($bt);
		}
		if (strstr($caller["file"], "db.class.php"))
		{
			$caller = array_shift($bt);
		}
		if (strstr($caller["file"], "db.class.php"))
		{
			$caller = array_shift($bt);
		}
		return "# File:$caller[file] Line:$caller[line] \n".$sql;
	}


	/**
	*	CMS
	*	_query_sql takes the query down to the server
	*	@access 	private
	*	@author 	Jannick Bruhns <info@brunick.de>
	*	@version 	1.0-Alpha
	*	@since 		1.0-Alpha
	*/

	private function _query_sql($query)
	{
		try {
			if($this->con) {

				//
				// &$trace = (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
				//$arr = array('trace'=>$trace,'query'=>$query);
				//$_SESSION['querys'][] = $arr;
				//
				$query = $this->sql_add_backtrace($query);
				$result = @mysqli_query($this->con,$query);
				while (!$result && (strstr(strtolower(mysqli_error($this->con)), "deadlock") || strstr(strtolower(mysqli_error($this->con)), "lock wait timeout"))){
					$i++;
			    	//echo "\nDEADLOCK $i: $sql\n";
			    	sleep(5);
			    	$result = @mysqli_query($this->con,$query, $this->con);
			   		if ($i == 10) {
			   			break;
					}
			  	}
				$this->sql_counter++;
				return $result;
			} else {
				throw new Exception ('Keine Verbindung, Query fehlgeschlagen');
			}
		} catch (Exception $e) {
			$this->error($e,"#_query_sql\n$query");
			/*
			if($this->debug) {
				echo '<pre>';
				debug_print_backtrace();
				echo mysqli_error($this->con);
				echo $e->getMessage() . '<br>';
				echo '</pre>';
				die();
			}
			*/
		}
	}

	/**
	*	CMS
	*	_connect 	connects to the server with the local data
	*	@access 	private
	*	@author 	Jannick Bruhns <info@brunick.de>
	*	@version 	1.0-Alpha
	*	@since 		1.0-Alpha
	*/

	private function _connect() {
		try {
			$this->con = mysqli_connect($this->dat['mysql_server'],$this->dat['mysql_user'],$this->dat['mysql_pass'],$this->datDb);
            self::$connection_count++;
			if(mysqli_connect_errno()) {
				throw new Exception (mysqli_connect_error());
			} else {
//				mysqli_select_db();
//				mysqli_query('SET NAMES "utf8"',$this->con);
				mysqli_set_charset($this->con, "utf8");
				return true;
			}
		} catch (Exception $e) {
			$this->error($e,"#_connect");

			/*if($this->debug) {
				echo '<pre>';
				debug_print_backtrace();
				echo mysqli_error($this->con);
				echo $e->getMessage() . '<br>';
				echo '</pre>';
				die();
			}*/
		}
	}

	public function _server_info() {
		return mysqli_get_server_info($this->con);
	}

	/**
	*	CMS
	*	_disconnect closes the connection
	*	@access 	private
	*	@author 	Jannick Bruhns <info@brunick.de>
	*	@version 	1.0-Alpha
	*	@since 		1.0-Alpha
	*/

	private function _disconnect() {
		try{
			if(mysqli_close($this->con)) {
				return true;
			} else {
				throw new Exception ('Keine Verbindung aktiv');
			}
		} catch (Exception $e) {
			$this->error($e,"#_disconnect");
			/*if($this->debug) {
				echo '<pre>';
				debug_print_backtrace();
				echo mysqli_error($this->con);
				echo $e->getMessage() . '<br>';
				echo '</pre>';
				die();
			}*/
		}
	}


	/**
	*	Gibt den Fehler aus. Bzw. speichert den Fehler in eine Tabelle
	*	@access 	private
	*	@author 	Dennis Rehbehn <dennis.rehbehn@df-automotive.de>
	*	@version 	1.0-Alpha
	*	@since 		1.0-Alpha
	*/
	private function error($error,$query) {
		if($this->debug) {
			echo '<span style="font-size:24px;">SQL FEHLER</span>';
			echo '<pre>';
			debug_print_backtrace();
			echo mysqli_error($this->con);
			if(is_object($error)) echo $error->getMessage() . '<br>';
			echo '</pre>';
			//die();
		}
		else
		{
			if(!stristr($query, "#IGNOREERRORREPORTING"))
			{
					$error = mysqli_error($this->con);
					if(is_object($error))
						$message = $error->getMessage();
					else
						$message = "";
					$backtrace = debug_backtrace();
                    $backtraceMsg = "";
					// print_r($backtrace);
					foreach ($backtrace as $step){
						$backtraceMsg .= $step['file'] . ':' . $step['line']."\n";
						//$backtraceMsg .= $step['class'] . '->' . $step['function']."\n\n";
					}
					$backtraceMsg .= $_SERVER['HTTP_HOST'].'?'.$_SERVER['QUERY_STRING']."\n\n\$_POST\n".print_r($_POST,true);
					// die();

					$q = "#IGNOREERRORREPORTING
								INSERT INTO sql_errors.wari3
								set
									query = '".addslashes($query)."',
									error = '".addslashes($error)."',
									message = '".addslashes($message)."',
									backtrace = '".mysqli_real_escape_string($this->con,$backtraceMsg)."',
									created = now()

									";
					$id = $this->qis($q);
				/*
					echo "<div style='clear:both'><pre>Es wurde ein SQL Fehler festgestellt. Fehlernummer #$id</pre></div>";
				*/
			}
		}
	}
	public function mysqli_real_escape_string($text) {
		return mysqli_real_escape_string($this->con,$text);
	}
	public function real_escape_string ($string) {
		return mysqli_real_escape_string($this->con, $string);
	}

	public function escape_string ($string) {
		return $this->real_escape_string($string);
	}
	public function esc ($string) {
		return $this->real_escape_string($string);
	}

	public function get_cache($cachekey)
	{
		if (isset($GLOBALS["cache_a"]["$cachekey"]))
		{
		  //echo " hit ";
		  return $GLOBALS["cache_a"]["$cachekey"];
		}
		return false;
	}

	public function set_cache($cachekey, $return)
	{
		$GLOBALS["cache_a"]["$cachekey"] = $return;
	}

	public function clear_cache()
	{
		$GLOBALS["cache_a"] = array();
	}

}
