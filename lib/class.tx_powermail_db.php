<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Mischa Heißmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@einpraegsam.net>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');

// This class saves powermail values in OTHER db tables if wanted (this class is not the main database class for storing)
class tx_powermail_db extends tslib_pibase {

	var $extKey = 'powermail';
    var $scriptRelPath = 'pi1/class.tx_powermail_pi1.php'; // Path to pi1 to get locallang.xml from pi1 folder
	var $dbInsert = 1; // Disable db insert for testing only
	
	
	// Main Function for inserting datas to other tables
	function main($conf, $sessiondata, $ok) {
		if($ok) { // if it's allowed to save db values
			
			// config
			global $TSFE;
			$this->cObj = $TSFE->cObj; // cObject
			$this->conf = $conf;
			$this->sessiondata = $sessiondata;
			$db_allowed = ( $this->cObj->cObjGetSingle($this->conf['dbEntry.']['tt_address.']['enable'], $this->conf['dbEntry.']['tt_address.']['enable.']) != '' ? $this->cObj->cObjGetSingle($this->conf['dbEntry.']['tt_address.']['enable'], $this->conf['dbEntry.']['tt_address.']['enable.']) : $this->conf['dbEntry.']['tt_address'] );
			$db_values = array(); // init dbArray
			
			// Let's go
			if (isset($this->conf['dbEntry.']) && is_array($this->conf['dbEntry.'])) { // Only if any dbEntry is set per typoscript
				foreach ($this->conf['dbEntry.'] as $key => $value) { // One loop for every table to insert
					
					// 1. Insert dynamic values to array
					if (isset($this->conf['dbEntry.'][$key]) && is_array($this->conf['dbEntry.'][$key])) { // Only if its an array
						foreach ($this->conf['dbEntry.'][$key] as $kk => $vv) { // One loop for every field to insert in current table
							$vvv = str_replace(';',',',t3lib_div::trimExplode(',',$vv,1)); // if value should be saved in more fields
							for ($i=0; $i<count($vvv); $i++) { // one loop for every field (if a value should be saved to more fields
								if (strpos(strtolower($kk), "_") === false) { // if there is no underscore (uid34)
									if ($this->sessiondata[strtolower($kk)] && $this->fieldExists($vvv[$i], str_replace('.','',$key))) { // If value exists and db field exists
										$db_values[$vvv[$i]] = $this->sessiondata[strtolower($kk)]; // generate array for saving to db (if there is a value in the session AND table/field exist)
									}
								} else { // if there is an underscore (uid34_1) maybe for checkboxes
									$keyparts = t3lib_div::trimExplode('_', strtolower($kk), 1); // array for 34 and 1
									if ($this->sessiondata[$keyparts[0]][$keyparts[1]] && $this->fieldExists($vvv[$i], str_replace('.','',$key))) { // If value exists and db field exists
										$db_values[$vvv[$i]] = $this->sessiondata[$keyparts[0]][$keyparts[1]]; // generate array for saving to db (if there is a value in the session AND table/field exist)
									}
								}
							}
						}
					}
					
					
					// 2. Insert static values to same array
					if (isset($this->conf['dbEntryDefault.'][$key]) && is_array($this->conf['dbEntryDefault.'][$key])) { // Only if any dbEntryDefault is set per typoscript
						foreach ($this->conf['dbEntryDefault.'][$key] as $sk => $sv) { // One loop for every field to insert in current table
							if( $this->fieldExists($sk, str_replace('.','',$key)) ) { // If database table exists
								
								if ($sv == '[pid]') $db_values[$sk] = $GLOBALS['TSFE']->id; // add current pid
								elseif ($sv == '[tstamp]') $db_values[$sk] = time(); // add current timestamp
								else $db_values[$sk] = ( $this->cObj->cObjGetSingle($this->conf['dbEntryDefault.'][$key][$sk], $this->conf['dbEntryDefault.'][$key][$sk.'.']) ? $this->cObj->cObjGetSingle($this->conf['dbEntryDefault.'][$key][$sk], $this->conf['dbEntryDefault.'][$key][$sk.'.']) : $this->conf['dbEntryDefault.'][$key][$sk] ); // add static value from ts
								
							}
						}
					}
					
					// 3. DB insert
					if ($this->dbInsert && $db_allowed != '0' && isset($db_values) && is_array($db_values)) { // if its allowed and db array is not empty
						$GLOBALS['TYPO3_DB']->exec_INSERTquery(str_replace('.','',$key), $db_values); // DB entry for every table
					}
					
				}
			}
				
			
		}
	}
	
	
	// Function fieldExists() checks if a table and field exist
	function fieldExists($field = '', $table = '') {
		if (!empty($field) && !empty($table) && strpos($field, ".") === false) {
			$row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( mysql_query('SHOW TABLES LIKE "'.$table.'"') ); // check if table exist
			if($row1) $row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( mysql_query('DESCRIBE '.$table.' '.$field) ); // check if field exist (if table is wront - errormessage)
			
			if($row1 && $row2) return 1; // table and field exist
			else return 0; // table or field don't exist
		}
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_db.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_db.php']);
}

?>
