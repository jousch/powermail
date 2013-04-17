<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Mischa Heiﬂmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@wunschtacho.de>
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

require_once(t3lib_extMgm::extPath('powermail').'lib/class.tx_powermail_removeXSS.php'); // file for removeXSS function

/**
 * Class with collection of different functions (like string and array functions)
 *
 * @author	Mischa Heiﬂmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@einpraegsam.net>
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
	
	// Function linker() generates link (email and url) from pure text string within an email or url ('test www.test.de test' => 'test <a href="http://www.test.de">www.test.de</a> test')
    function linker($link,$additinalParams = '') {
        $link = str_replace("http://www.","www.",$link);
        $link = str_replace("www.","http://www.",$link);
        $link = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<a href=\"$1\"$additinalParams>$1</a>", $link);
        $link = preg_replace("/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i","<a href=\"mailto:$1\"$additinalParams>$1</a>",$link);
    
        return $link;
    }
	
	// Function nl2br2() changes breakes to html breakes
	function nl2br2($string) {
		return str_replace('\r\n',"<br />",$string);
	}
	
	// Function nl2br2() changes breakes to real breakes
	function nl2nl2($string) {
		return str_replace('\r\n',"\r\n",$string);
	}
	
	// Function sec() is a security function against all bad guys :) 
	function sec($array) {
		if(isset($array) && is_array($array)) { // if array
			$this->removeXSS = t3lib_div::makeInstance('tx_powermail_RemoveXSS'); // New object: function for removing XSS
			
			foreach ($array as $key => $value) { // one loop for every key in first level
				
				if(!is_numeric(str_replace('UID','',$key)) && !is_array($value)) { // all others piVars than UID34
					$array[$key] = intval(trim($value)); // the value should be integer
				}
					
				if(!is_array($value)) {	// if value is not an array
				
					$array[$key] = strip_tags(trim($value)); // strip_tags removes html and php code
					$array[$key] = $this->removeXSS->RemoveXSS($array[$key]); // remove XSS
					$array[$key] = addslashes($array[$key]); // use addslashes
					
				} else { // value is still an array (second level)
					
					if(!is_array($key2)) {	// if value is not an array
						foreach ($value as $key2 => $value2) { // one loop for every key in second level
						
							$array[$key][$key2] = strip_tags(trim($value2)); // strip_tags removes html and php code
							$array[$key][$key2] = $this->removeXSS->RemoveXSS($array[$key][$key2]); // remove XSS
							$array[$key][$key2] = addslashes($array[$key][$key2]); // use addslashes
							
						}
					} else unset($array[$key][$key2]); // if array with 3 or more dimensions - delete this value
					
				}
			}
			
			return $array;
			
		}
	}
	
	
	// Function correctPath() checks if the link is like "fileadmin/test/ and not "/fileadmin/test"
	function correctPath($value) {
		// If there is no Slash at the end of the picture folder, add a slash and if there is a slash at the beginning, remove this slash
		if (substr($value, -1, 1) != '/') $value .= '/'; // add a slash at the end if there is no slash
		if (substr($value, 0, 1) == '/') $value = substr($value, 1); // remove slash from the front
		
		if ($value) return $value;
	}
	
	
	// Function marker2value() replaces ###UID3### with its value from session
	function marker2value($string,$sessiondata) {
		$this->sessiondata = $sessiondata; // make session array available in other functions
		
		$string = preg_replace_callback ( // Automaticly replace ###UID55### with value from session to use markers in query strings
			'#\#\#\#UID(.*)\#\#\##Uis', // regulare expression
			array($this,'uidReplaceIt'), // open function
			$string // current string
		);
	
		return $string;
	}
	
	
	// Function uidReplace is used for the callback function to replace ###UID55## with value
	function uidReplaceIt($uid) {
		if (isset($this->sessiondata['uid'.$uid[1]])) {
			if (!is_array($this->sessiondata['uid'.$uid[1]])) { // value is not an array
				
				return $this->sessiondata['uid'.$uid[1]]; // return 44 (e.g.)
				
			} else { // value is an array
			
				$return = ''; $i=0; // init counter
				foreach ($this->sessiondata['uid'.$uid[1]] as $key => $value) { // one loop for every value
					$return .= ($i!=0?',':'').$value; // add a value (commaseparated)
					$i++; // increase counter
				}
				return $return; // return 44,45,46 (e.g.)
				
			}
		}
	}


	// Function for initialisation.
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
