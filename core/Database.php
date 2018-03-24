<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

class Database {

	public static $RETURN_LASTINSERTID =1 ;
	public static $RETURN_FETCHALLCOLUMN =2 ;

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

	private function fehler_beim_pdo_erstellen(Exception $e, $exit_on_error){
		require_once ROOT_HDD_CORE . "/core/Errors.php";
		$this->set_error(Errors::format_exception($e), $e->getCode());
		if($exit_on_error){

			//Handelt es sich um die Initialisierung der main-database?
			if(self::$main==null&&$this->error_code==1049/*Unknown database*/){
				include_once ROOT_HDD_CORE . '/inst/Install.php';
				$page=Install::initialize_install("installer_dbinit");
				$dbinitform=Install::dbinit();
				$page->addMessageInfo("Datenbank nicht vorhanden!");
				$page->addHtml($dbinitform);
				$page->send_and_quit();
			}
			if(self::$main==null&&$this->error_code==1045/*Access denied*/){
				include_once ROOT_HDD_CORE . '/inst/Install.php';
				$page=Install::initialize_install("installer_dbinit");
				$page->exit_with_error("Zugriff auf die Datenbank fehlgeschlagen! Bitte Benutzername und Passwort überprüfen: "
					."<code>".TCFGFILE."</code>");
			}

			echo ("Datenbank-Fehler! ".$this->error_msg);exit;
		}
	}

	private function set_error($msg, $code, $error=true){
		$this->error_msg=$msg?:$error;
		$this->error_code=$code?:$error;
	}
	private function reset_error(){
		$this->set_error(false, false, false);
	}

	public static function set_main_connection(Database $database){
		self::$main=$database;
	}

	public function iquery($comment, $query, $return){
		$this->reset_error();
		/** @var PDOStatement */
		$statement = $this->pdo->query($query);
		if($statement===false){
			$errorInfo=$this->pdo->errorInfo();
			$this->set_error($errorInfo[2], $errorInfo[0]);
			return false;
		}
		switch ($return){
			case self::$RETURN_LASTINSERTID:
					return $this->pdo->lastInsertId();
				break;
			case self::$RETURN_FETCHALLCOLUMN:
					return $statement->fetchAll(PDO::FETCH_COLUMN);
				break;
			default:
					return null;/*No return type specified*/
				break;
		}
	}

	public static function insert($comment, $query){
		return self::$main->iquery($comment, $query, self::$RETURN_LASTINSERTID);
	}

}