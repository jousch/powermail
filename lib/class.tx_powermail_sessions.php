<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Mischa Heiﬂmann, Alexander Kellner <typo3@heissmann.org, alexander.kellner@wunschtacho.de>
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

require_once(str_replace('../','',t3lib_extMgm::extRelPath('powermail')).'lib/class.tx_powermail_functions_div.php'); // file for div functions

/**
 * Class with collection of different functions (like string and array functions)
 *
 * @author	Mischa Heiﬂmann, Alexander Kellner <typo3@heissmann.org, alexander.kellner@einpraegsam.net>
 * @package	TYPO3
 * @subpackage	tx_powermail
 */
class tx_powermail_sessions {

	var $extKey = 'powermail';
	
	// Function setSession() to save all piVars to a session
	function setSession($piVars) {
		if(isset($piVars)) { // Only if piVars are existing
			// Security Options
			//$piVars = array_map(array($this->div_functions, 'validateValue'), $piVars); // disable forbidden values (He'l_lo y$<>ou => Hel_lo you)
			//$piVars = array_map('strip_tags', $piVars); // Removes HTML and PHP code - TODO Funktioniert nur in erster Dimension
			t3lib_div::addSlashesOnArray($piVars); // addslashes for every piVar (He'l"lo => He\'l\"lo)
			
			// Set Session
			$GLOBALS['TSFE']->fe_user->setKey("ses", $this->extKey.'_'.$this->pibase->cObj->data['uid'], $piVars); // Generate Session with piVars array
			$GLOBALS['TSFE']->storeSessionData(); // Save session
		}
	}
	
	// Function getSession() to get all saved session data in an array
	function getSession($all = 1) {
		$piVars = $GLOBALS['TSFE']->fe_user->getKey("ses", $this->extKey.'_'.$this->pibase->cObj->data['uid']); // Get piVars from Session
		//$piVars = array_map('html_entity_decode',$piVars);
		
		if($all == 0) { // delete not allowed values from piVars
			$this->notInMarkerAll = t3lib_div::trimExplode(',',$this->conf['markerALL.']['notIn'],1); // choose which fields should not be listed
			
			for ($i=0;$i<count($this->notInMarkerAll);$i++) { // One loop for every not allowed value
				if(isset($piVars[$this->notInMarkerAll[$i]])) unset($piVars[$this->notInMarkerAll[$i]]); // delte current value
			}
		}
		
		if(isset($piVars)) return $piVars;
	}


	//function for initialisation.
	// to call cObj, make $this->pibase->cObj->function()
	function init(&$conf,&$pibase) {
		$this->conf = $conf;
		$this->pibase = $pibase;
		$this->div_functions = t3lib_div::makeInstance('tx_powermail_functions_div'); // New object: div functions
		$this->div_functions->init($this->conf,$this); // Initialise the new instance to make cObj available in all other functions.
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_sessions.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_sessions.php']);
}

?>
