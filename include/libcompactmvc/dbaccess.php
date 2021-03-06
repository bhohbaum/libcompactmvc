<?php
if (file_exists('../libcompactmvc.php'))
	include_once ('../libcompactmvc.php');
LIBCOMPACTMVC_ENTRY;

/**
 * this class handles our DB connection and requests
 *
 * @author 		Botho Hohbaum <bhohbaum@googlemail.com>
 * @package		LibCompactMVC
 * @copyright   Copyright (c) Botho Hohbaum
 * @license 	BSD License (see LICENSE file in root directory)
 * @link		https://github.com/bhohbaum/LibCompactMVC
 */
abstract class DbAccess {
	// keeps instance of the class
	private static $instance;
	protected static $mysqli;

	protected function __construct() {
		$this->open_db();
	}

	public function __destruct() {
		// Do not close the DB, as other objects might still need a connection.
		// $this->close_db();
	}

	// prevent cloning
	private function __clone() {
		DLOG();
	}

	/**
	 *
	 * @return DbAccess the instance of this class. this is a singleton. there can only be one instance per derived class.
	 */
	public static function get_instance($name) {
		DLOG();
		if ((!isset(self::$instance)) || (!array_key_exists($name, self::$instance))) {
			if (($name == null) || ($name == "")) {
				$name = get_class($this);
			}
			if (!is_subclass_of($name, "DbAccess"))
				throw new DBException("Class must be a subclass of DbAccess.", 500);
			self::$instance[$name] = new $name();
		}

		return self::$instance[$name];
	}

	/**
	 *
	 * @throws Exception
	 */
	protected function open_db() {
		if (isset(self::$mysqli)) {
			return;
		}
		self::$mysqli = MySQLAdapter::get_instance($GLOBALS['MYSQL_HOSTS']);
	}

	/**
	 */
	protected function close_db() {
		DLOG();
		if (self::$mysqli != null) {
			self::$mysqli->close();
			self::$mysqli = null;
		}
	}

	/**
	 * Execute a DB query.
	 *
	 * @param String $query
	 *        	The query to execute.
	 * @param Boolean $has_multi_result
	 *        	Is one object expected as result, or a list?
	 * @param Boolean $object
	 *        	Return as array or as object
	 * @param String $field
	 *        	Columnname if a single value shall be returned.
	 * @param String $table
	 *        	Name of the table that is operated on.
	 * @param Boolean $is_write_access
	 *        	Set to true when issuing a write query.
	 * @throws Exception
	 * @return Ambigous <multitype:, NULL>
	 */
	protected function run_query($query, $has_multi_result = false, $object = false, $field = null, $table = null, $is_write_access = true) {
		DLOG($query);
		$ret = null;
		$typed_object = $table != null && class_exists($table) && is_subclass_of(new $table(), "DbObject");
		$key = REDIS_KEY_TBLCACHE_PFX . $table . "_" . $is_write_access . "_" . $field . "_" . $object . "_" . $has_multi_result . "_" . md5($query);
		$object = ($field == null) ? $object : false;
		if (array_search($table, $GLOBALS['MYSQL_NO_CACHING']) === false) {
			if ($is_write_access) {
				$delkey = REDIS_KEY_TBLCACHE_PFX;
				$delkey .= ($table == null) ? "*" : $table . "*";
				$keys = RedisAdapter::get_instance()->keys($delkey);
				foreach ($keys as $k) {
					RedisAdapter::get_instance()->delete($k);
				}
			} else {
				$res = RedisAdapter::get_instance()->get($key);
				if ($res !== false) {
					RedisAdapter::get_instance()->expire($key, REDIS_KEY_TBLCACHE_TTL);
					DLOG("Query was cached!!!");
					return unserialize($res);
				}
			}
		}
		$result = self::$mysqli->query($query, $is_write_access, $table);
		if ($result === false) {
			throw new DBException("Query \"$query\" caused an error: " . self::$mysqli->get_error(), self::$mysqli->get_errno());
		} else {
			if (is_object($result)) {
				if ($has_multi_result) {
					if ($object) {
						while ($row = $result->fetch_assoc()) {
							if ($typed_object) {
								$tmp = new $table($row, false);
							} else {
								$tmp = new DbObject($row, false);
								if ($table != null) {
									$tmp->table($table);
								}
							}
							$ret[] = $tmp;
						}
					} else {
						while ($row = $result->fetch_assoc()) {
							if ($field != null) {
								$ret[] = $row[$field];
							} else {
								$ret[] = $row;
							}
						}
					}
				} else {
					$num_loaded = 0;
					if ($object) {
						while ($row = $result->fetch_assoc()) {
							if ($num_loaded >= 1) throw new MultipleResultsException("Query has multiple results, but only one result was requested.", 500);
							if ($typed_object) {
								$tmp = new $table($row, false);
							} else {
								$tmp = new DbObject($row, false);
								if ($table != null) {
									$tmp->table($table);
								}
							}
							$num_loaded++;
							$ret = $tmp;
						}
					} else {
						while ($row = $result->fetch_assoc()) {
							if ($num_loaded >= 1) throw new MultipleResultsException("Query has multiple results, but only one result was requested.", 500);
							if ($field != null) {
								$ret = $row[$field];
							} else {
								$ret = $row;
							}
							$num_loaded++;
						}
					}
				}
				$result->close();
			} else {
				$ret = self::$mysqli->get_insert_id();
			}
		}
		if (($ret == null) && ($has_multi_result == true)) {
			$ret = array();
		}
		if (array_search($table, $GLOBALS['MYSQL_NO_CACHING']) === false) {
			if (!$is_write_access) {
				RedisAdapter::get_instance()->set($key, serialize($ret));
				RedisAdapter::get_instance()->expire($key, REDIS_KEY_TBLCACHE_TTL);
			}
		}
		DLOG("Query was NOT cached!!!");
		return $ret;
	}

	/**
	 *
	 * @param String $tablename
	 * @param array $constraint
	 */
	public function by_table($tablename, $constraint = null, $wildcard = false) {
		$qb = new QueryBuilder();
		$constraint = ($constraint == null) ? array() : $constraint;
		if ($wildcard)
			$q = $qb->like($tablename, $constraint);
		else
			$q = $qb->select($tablename, $constraint);
		if (is_object($constraint) && get_class($constraint) == "DbConstraint" && $constraint->count)
			$res = $this->run_query($q, false, true, "count", $tablename, false);
		else
			$res = $this->run_query($q, true, true, null, $tablename, false);
		return $res;
	}

	/**
	 *
	 * @param unknown_type $mode
	 * @throws Exception
	 */
	public function autocommit($mode) {
		DLOG();
		self::$mysqli->autocommit($mode);
	}

	/**
	 *
	 * @throws Exception
	 */
	public function begin_transaction() {
		DLOG();
		self::$mysqli->begin_transaction();
	}

	/**
	 *
	 * @throws Exception
	 */
	public function commit() {
		DLOG();
		self::$mysqli->commit();
	}

	/**
	 *
	 * @throws Exception
	 */
	public function rollback() {
		DLOG();
		self::$mysqli->rollback();
	}

	/**
	 *
	 * @param unknown $str
	 * @throws Exception
	 */
	protected function escape($str) {
		// we don't DLOG here, it's spaming...
		// DLOG();
		if (self::$mysqli) {
			return self::$mysqli->real_escape_string($str);
		}
		throw new Exception("DbAccess::mysqli is not initialized, unable to escape string.");
	}

	/**
	 * Use this method for values that can be null, when building the SQL query.
	 * Refrain from surrounding this return value with "'", as they are automatically added to string values!
	 *
	 * @param
	 *        	String_or_Number input value that has to be transformed
	 * @return String value to concatenate with the rest of the sql query
	 */
	protected function sqlnull($var, $wildcard = false) {
		// we don't DLOG here, it's spaming...
		// DLOG();
		$leadingzero = substr($var, 0, 1) == "0";
		$leadingplus = substr($var, 0, 1) == "+";
		$iszero = ($var === "0");
		if ($iszero || (is_numeric($var) && !$leadingzero && !$leadingplus)) {
			$var = (empty($var) && !is_numeric($var)) ? "NULL" : ($wildcard ? "'%" : "") . $var . ($wildcard ? "%'" : "");
		} else {
			$var = (empty($var) && !is_numeric($var)) ? "NULL" : "'" . ($wildcard ? "%" : "") . $var . ($wildcard ? "%" : "") . "'";
		}
		return $var;
	}

	/**
	 *
	 * @param unknown $var value that needs to be compared
	 * @return string comparison operator
	 */
	protected function cmpissqlnull($var) {
		if ($this->sqlnull($var) == "NULL") {
			return "IS";
		} else {
			return "=";
		}
	}
	
	/**
	 *
	 * @param unknown $var value that needs to be compared
	 * @return string comparison operator
	 */
	protected function cmpisnotsqlnull($var) {
		if ($this->sqlnull($var) == "NULL") {
			return "IS NOT";
		} else {
			return "!=";
		}
	}
	
}
