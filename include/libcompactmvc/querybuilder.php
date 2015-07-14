<?php
@include_once ('../libcompactmvc.php');
LIBCOMPACTMVC_ENTRY;

/**
 * Simple PHP implementation of an SMTP client.
 *
 * @author Botho Hohbaum (bhohbaum@googlemail.com)
 * @package LibCompactMVC
 * @copyright Copyright (c) Botho Hohbaum 24.01.2012
 * @license LGPL version 3
 * @link https://github.com/bhohbaum/libcompactmvc
 */
class QueryBuilder extends DbAccess {
	public $log;
	private $td;

	/**
	 *
	 */
	public function __construct() {
		parent::__construct();
		$this->td = new TableDescription();
	}

	/**
	 *
	 * @param unknown_type $tablename
	 * @param unknown_type $constraint
	 */
	public function select($tablename, $constraint = array()) {
		$q = "SELECT * FROM " . $tablename;
		if (count($constraint) > 0) {
			$q .= " WHERE";
			$desc = $this->td->columninfo($tablename);
			foreach ($desc as $key => $val) {
				if (array_key_exists($val->Field, $constraint)) {
					$q .= " " . $val->Field . " = " . $this->sqlnull($this->escape($constraint[$val->Field])) . " AND ";
				}
			}
			$q = substr($q, 0, strlen($q) - 5);
		}
		DLOG($q);
		return $q;
	}

	/**
	 *
	 * @param unknown_type $tablename
	 * @param unknown_type $fields
	 */
	public function insert($tablename, $fields) {
		$nofields = true;
		$desc = $this->td->columninfo($tablename);
		$q = "INSERT INTO " . $tablename . " (";
		foreach ($desc as $key => $val) {
			if (array_key_exists($val->Field, $fields)) {
				$q .= $val->Field . ", ";
				$nofields = false;
			}
		}
		$q = substr($q, 0, strlen($q) - 2);
		if ($nofields) {
			return $q . " () VALUES ()";
		}
		$q .= ") VALUES (";
		foreach ($desc as $key => $val) {
			if (array_key_exists($val->Field, $fields)) {
				$q .= $this->sqlnull($this->escape($fields[$val->Field])) . ", ";
			}
		}
		$q = substr($q, 0, strlen($q) - 2);
		$q .= ")";
		DLOG($q);
		return $q;
	}

	/**
	 *
	 * @param unknown_type $tablename
	 * @param unknown_type $fields
	 * @param unknown_type $constraint
	 */
	public function update($tablename, $fields, $constraint) {
		$desc = $this->td->columninfo($tablename);
		$q = "UPDATE " . $tablename . " SET ";
		foreach ($desc as $key => $val) {
			if (array_key_exists($val->Field, $fields)) {
				$q .= $val->Field . " = " . $this->sqlnull($this->escape($fields[$val->Field])) . ", ";
			}
		}
		$q = substr($q, 0, strlen($q) - 2);
		$q .= " WHERE ";
		foreach ($desc as $key => $val) {
			if (array_key_exists($val->Field, $constraint)) {
				$q .= $val->Field . " = " . $this->sqlnull($this->escape($constraint[$val->Field])) . " AND ";
			}
		}
		$q = substr($q, 0, strlen($q) - 5);
		DLOG($q);
		return $q;
	}

	/**
	 *
	 * @param unknown_type $tablename
	 * @param unknown_type $constraint
	 */
	public function delete($tablename, $constraint) {
		$desc = $this->td->columninfo($tablename);
		$q = "DELETE FROM " . $tablename . " WHERE ";
		foreach ($desc as $key => $val) {
			if (array_key_exists($val->Field, $constraint)) {
				$q .= $val->Field . " = " . $this->sqlnull($this->escape($constraint[$val->Field])) . " AND ";
			}
		}
		$q = substr($q, 0, strlen($q) - 5);
		DLOG($q);
		return $q;
	}

}
