<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

/**
 * Contains static methods for database operations.
 * A static Database object ($main) is created to handle the requests.
 * A PDO object ($pdo) is used to execute database queries.
 */
class Database {

	//Return types:
	/**
	 * Returns id value of the inserted set of data.
	 * Used by the function @see insert.
	 */
	const RETURN_LASTINSERTID = 1;
	/**
	 * Returns result of the SELECT query in form of an associative array.
	 * Used by the function @see select.
	 */
	const RETURN_ASSOC = 2;
	/**
	 * Returns the number of rows affected by the last query.
	 * Used by the function @see delete.
	 */
	const RETURN_ROWCOUNT = 3;

	/** @var Database */
	private static $main = null;

	/** @var PDO */
	private $pdo;
	private $error_msg;
	private $error_code;

	public function __construct($host, $dbname, $user, $password, $exit_on_error = true) {
		$this->reset_error();
		try {
			$this->pdo = new PDO("mysql:host=" . $host . ";dbname=" . $dbname, $user, $password);
			$this->pdo->query('SET NAMES utf8');
		} catch (Exception $e) {
			$this->fehler_beim_pdo_erstellen($e, $exit_on_error);
		}
	}

	/**
	 * Sets the error values in case of a failed PDO instantiation.
	 * If no main database is set, it is assumed that we are in installation process and the installer is called.
	 * @param Exception $e
	 * @param bool      $exit_on_error
	 */
	private function fehler_beim_pdo_erstellen(Exception $e, $exit_on_error) {
		require_once ROOT_HDD_CORE . "/core/Errors.php";
		$this->set_error(Errors::format_exception($e), $e->getCode());
		if ($exit_on_error) {

			//Is no main database set?
			if (self::$main == null) {
				if ($this->error_code == 1049/*Unknown database*/) {
					include_once ROOT_HDD_CORE . '/inst/Install.php';
					$page = Install::initialize_install("installer_dbinit");
					$dbinitform = Install::dbinit_1();
					$page->addMessageInfo("Datenbank nicht vorhanden!");
					$page->addHtml($dbinitform);
					$page->send_and_quit();
				}
				if ($this->error_code == 1045/*Access denied*/) {
					include_once ROOT_HDD_CORE . '/inst/Install.php';
					$page = Install::initialize_install("installer_dbinit");
					$page->exit_with_error("Zugriff auf die Datenbank fehlgeschlagen! Bitte Benutzername und Passwort überprüfen: "
						. "<code>" . TCFGFILE . "</code>");
				}
			}

			echo("Datenbank-Fehler! " . $this->error_msg);
			exit;
		}
	}

	/**
	 * The two values error_msg and error_code are coupled together. This function shoud be used to set them.
	 * $error=true .... If no error message or error code is available the value is set to TRUE.
	 * $error=false ... In case of no error both values shoud be FALSE.
	 * @param string     $msg
	 * @param string|int $code
	 * @param bool       $error
	 */
	private function set_error($msg, $code, $error = true) {
		$this->error_msg = $msg ?: $error;
		$this->error_code = $code ?: $error;
	}

	public static function get_error_msg(){
		return self::$main->error_msg;
	}

	public static function get_error_code(){
		return self::$main->error_code;
	}

	private function reset_error() {
		$this->set_error(false, false, false);
	}

	/**
	 * Should be called only once by the configuration file.
	 * Sets the main database connection.
	 * @param Database $database
	 */
	public static function set_main_connection(Database $database) {
		self::$main = $database;
	}

	/**
	 * Handles different types of queries, specified by $return
	 * @param string $comment
	 * @param string $query
	 * @param int    $return_type Database::RETURN_...
	 * @return array|false|null|string
	 */
	private function iquery($comment, $query, $return_type) {
		$this->reset_error();
		/** @var PDOStatement */
		$statement = $this->pdo->query($query);
		if ($statement === false) {
			$errorInfo = $this->pdo->errorInfo();
			$this->set_error($errorInfo[2], $errorInfo[0]);
			return false;
		}
		switch ($return_type) {
			case self::RETURN_LASTINSERTID:
				return $this->pdo->lastInsertId();
				break;
			case self::RETURN_ASSOC:
				return $statement->fetchAll(PDO::FETCH_ASSOC);
				break;
			case self::RETURN_ROWCOUNT:
				return $statement->rowCount();
				break;
			default:
				return null;/*No return type specified*/
				break;
		}
	}

	/**
	 * Handles INSERT-queries given by a query string.
	 * @param string $comment
	 * @param string $query
	 * @return string|false ID of the inserted data, false in case of any failure
	 */
	public static function insert($comment, $query) {
		return self::$main->iquery($comment, $query, self::RETURN_LASTINSERTID);
	}

	/**
	 * Handles DELETE-queries given by a query string.
	 * @param string $comment
	 * @param string $query
	 * @return int|false Number of deleted rows or false in case of any failure
	 */
	public static function delete($comment, $query) {
		return self::$main->iquery($comment, $query, self::RETURN_ROWCOUNT);
	}

	/**
	 * Handles SELECT-queries given by a query string.
	 * @param string $comment
	 * @param string $query
	 * @return array|false Array of associative array containing requested data or false in case of any failure
	 */
	public static function select($comment, $query) {
		return self::$main->iquery($comment, $query, self::RETURN_ASSOC);
	}

	/**
	 * Handles SELECT-queries of a single data row given by a query string.
	 * @param string $comment
	 * @param string $query
	 * @return array|false Associative array containing first row of the requested data or false in case of any failure
	 */
	public static function select_single($comment, $query) {
		$response = self::select($comment, $query);
		if(!empty($response)){
			return $response[0];
		}
		return false;
	}

	/**
	 * Handles SELECT-queries of a single data cell.
	 * @param string $comment
	 * @param string $query
	 * @param string $column_title
	 * @param string $default_value
	 * @return string|false Value of the given column of the first row of the requested data
	 *                      or false in case of any failure
	 */
	public static function select_single_col($comment, $query, $column_title, $default_value) {
		$response = self::select_single($comment, $query);
		if(!empty($response)){
			return $response[$column_title];
		}
		return $default_value;
	}

	/**
	 * Adds data ($data_set and $data_where) to table $tabelle if it doesn't yet exist ($data_where).
	 * If $data_where exists the data from $data_set in $tabelle will be updated.
	 */
	public static function update_or_insert($tabelle, $data_where, $data_set) {
		if (empty($data_where) && empty($data_set)) return;

		//Build the WHERE statement:
		$where_sql = array();
		foreach ($data_where as $key => $value) {
			$where_sql[] = "`$key` = '" . escape_sql($value) . "'";
		}
		$where = implode(" AND ", $where_sql);

		//Check, if data already exists:
		$query1 = "SELECT count(*) as c FROM $tabelle WHERE $where;";
		$data = self::select("Check, if data already exists", $query1);
		$anzahl_treffer = $data[0]["c"];

		if($anzahl_treffer){
			//Data already exists: UPDATE
			$set_sql = array();
			foreach ($data_set as $key => $value) {
				$set_sql[] = "`$key` = '" . escape_sql($value) . "'";
			}
			$set = implode(", ", $set_sql);
			$query2 = "UPDATE $tabelle SET $set WHERE $where;";
			self::delete("Data already exists: UPDATE",$query2);
		}else{
			//Data didn't exist: INSERT
			$keys_sql = array();
			$values_sql = array();
			$data_alltogehter = $data_where + $data_set;
			foreach ($data_alltogehter as $key => $value) {
				$keys_sql[] = "`$key`";
				$values_sql[] = "'" . escape_sql($value) . "'";
			}
			$keys = implode(", ", $keys_sql);
			$values = implode(", ", $values_sql);
			$query2 = "INSERT INTO $tabelle ($keys) VALUES ($values);";
			self::insert("Data didn't exist: INSERT",$query2);
		}
	}

}