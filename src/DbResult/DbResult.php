<?php
namespace Netsilik\DbHandler\DbResult;

/**
 * @package DbHandler
 * @copyright (c) 2011-2016 Netsilik (http://netsilik.nl)
 * @license EUPL-1.1 (European Union Public Licence, v1.1)
 */



/**
 * Result object, returned by the DbHandler whenever a valid query is executed
 */
abstract class DbResult implements iDbResult {
		
	/**
	 * @var int $_queryTime The query time in mili seconds
	 */
	protected $_queryTime = 0;
	
	/**
	 * @inheritDoc
	 */
	public function fetchColumn($column = null) {
		$records = $this->fetch();
		$values = array();
		foreach ($records as $record) {
			foreach ($record as $key => $val) {
				if ( ! is_null($column) && $column <> $key) {
					continue;
				}
				$values[] = $val;
				break;
			}
		}
		return $values;
	}
	
	/**
	 * @inheritDoc
	 */
	public function fetchField($field = null, $recordNum = 0) {
		$records = $this->fetch();
		if (null === $field) {
			return reset($records[$recordNum]);
		}
		return isset($records[$recordNum][$field]) ? $records[$recordNum][$field] : null;	
	}
	
	/**
	 * @inheritDoc
	 */
	public function fetchRecord($recordNum = 0) {
		$records = $this->fetch();
		return isset($records[$recordNum]) ? $records[$recordNum] : array();	
	}
	
	/**
	 * Get the query execution 
	 * @return int time in seconds for this query
	 */
	public function getQueryTime() {
		return number_format($this->_queryTime, 4);
	}
	
	/**
	 * Dump query result to std-out as an html table
	 * @return void
	 */
	public function dump() {
		echo "<div class=\"debug\">\n";
		if (is_null($this->_result)) {
			echo "<strong>Query OK, ".$this->getAffectedRecords()." rows affected (".$this->getQueryTime()." sec.)</strong>\n";
		} elseif ( 0 == ($recordCount = $this->getRecordCount()) ) {
			echo "<strong>empty set (".$this->getQueryTime()." sec.)</strong>\n";
		} else {
			$records = $this->fetch();
			
			echo "<table cellspacing=\"0\" cellpadding=\"3\" border=\"1\">\n";
			for ($i = 0; $i < $recordCount; $i++) {
			
				if ($i == 0) {
					$fieldnames = array_keys($records[0]);
					echo "\t<tr>\n";
					foreach ($fieldnames as $fieldname) {
						echo "\t\t<th>".$fieldname."</th>\n";
					}
					echo "\t</tr>\n";
				}
				
				echo "\t<tr>\n";
				foreach ($records[$i] as $field) {
					echo "\t\t<td>";
					echo (is_null($field)) ? '<em>NULL</em>' : htmlspecialchars($field, ENT_NOQUOTES);
					echo "</td>\n";
				}
				echo "\t</tr>\n";
				
			}
			echo "</table>\n";
			echo "<strong>$recordCount row in set (".$this->getQueryTime()." sec.)</strong>\n";
		}
		echo "</div>\n";
	}
}