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

/**
 * Class with collection of different functions (like string and array functions)
 *
 * @author	Mischa Heiﬂmann, Alexander Kellner <typo3@heissmann.org, alexander.kellner@einpraegsam.net>
 * @package	TYPO3
 * @subpackage	tx_powermail
 */
class tx_powermail_functions_div {

	var $extKey = 'powermail';
	
	// Function clearName() to disable not allowed letters (only A-Z and 0-9 allowed) (e.g. Perfect Extension -> perfectextension)
	function clearName($string,$strtolower = 0,$cut = 0) {
		$string = preg_replace("/[^a-zA-Z0-9]/","",$string); // replace not allowed letters with nothing
		if($strtolower) $string = strtolower($string); // string to lower if active
		if($cut) $string = substr($string,0,$cut); // cut after X signs if active
		
		if(isset($string)) return $string;
	}
	
	// Function clearValue() to remove all " or ' from code
	function clearValue($string,$htmlentities = 1,$strip_tags = 0) {
		$notallowed = array('"',"'");
		$string = str_replace($notallowed,"",$string); // replace not allowed letters with nothing
		if($htmlentities) $string = htmlentities($string); // change code to ascii code
		if($strip_tags) $string = strip_tags($string); // disable html/php code
		
		if(isset($string)) return $string;
	}
	
	// Function validateValue() removes all vorbidden signs in piVars
	function validateValue($string) {
		//echo $this->conf['allow.']['signs'];
		//$string = htmlentities($string);
		//$string = preg_replace('/[^'.$this->conf['allow.']['signs'].']/','',$string); // replace not allowed letters with nothing
		echo $string;
		$string = preg_replace('/[^a-zA-Z0-9_ -,.;@!?=()ß$%:+*‰ˆ¸ƒ÷‹ﬂ]/','',$string); // replace not allowed letters with nothing
		echo $string;
		if(isset($string)) return $string;
	}


	//function for initialisation.
	// to call cObj, make $this->pibase->pibase->cObj->function()
	function init(&$conf,&$pibase) {
		$this->conf = $conf;
		$this->pibase = $pibase;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_functions_div.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_functions_div.php']);
}

?>
