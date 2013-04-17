<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Mischa Heißmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@wunschtacho.de>
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
require_once('class.tx_powermail_form.php');
require_once('class.tx_powermail_submit.php');
require_once('class.tx_powermail_confirmation.php');
require_once('class.tx_powermail_mandatory.php');
require_once(str_replace('../','',t3lib_extMgm::extRelPath('powermail')).'lib/class.tx_powermail_sessions.php'); // load session class
require_once(str_replace('../','',t3lib_extMgm::extRelPath('powermail')).'lib/class.tx_powermail_functions_div.php'); // load functions class


class tx_powermail_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_powermail_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_powermail_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'powermail';	// The extension key.
	var $pi_checkCHash = true;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The		content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->conf = $conf;
		$this->content = $content;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$GLOBALS['TYPO3_DB']->debugOutput = true; // SQL Debug mode
		
		// Instances
		$this->div = t3lib_div::makeInstance('tx_powermail_functions_div'); // Create new instance for submit class
		$this->sessions = t3lib_div::makeInstance('tx_powermail_sessions'); // New object: session functions
		$this->form = t3lib_div::makeInstance('tx_powermail_form'); // Initialise the new instance to make cObj availabla in all other functions.
		$this->submit = t3lib_div::makeInstance('tx_powermail_submit'); // Create new instance for submit class
		$this->confirmation = t3lib_div::makeInstance('tx_powermail_confirmation'); // Create new instance for submit class
		$this->mandatory = t3lib_div::makeInstance('tx_powermail_mandatory'); // Create new instance for submit class
		$this->mandatory->init($this->conf,$this); // mandatory init
		
		// Security for piVars
		$this->piVars = $this->div->sec($this->piVars); // first of all clean piVars
		
		// Sessionwork
		$this->sessions->init($this->conf,$this); // Initialise the new instance to make cObj available in all other functions.
		$this->piVars = $this->sessions->changeData($this->piVars); // manipulate data (upload fields, check email, etc..)
		$this->sessions->setSession($this->piVars,0); // Set piVars to session (but don't overwrite old values)
		$this->sessionfields = $this->sessions->getSession(0); // give me all piVars from session (without not needed values)
		
		// Start main choose
		if(t3lib_div::GPvar('type') != 3131) { // typenum is not 3131
			if(isset($this->piVars['multiple']) || isset($this->piVars['mailID']) || isset($this->piVars['sendNow'])) {
				// What kind of function should be showed in frontend
				if(!$this->piVars['multiple']) { // if multiple is not set
					if($this->piVars['mailID']) { // submitted
						if($this->cObj->data['tx_powermail_confirm']) { // Confirm page activated
						
							if(!$this->piVars['sendNow']) { // If sendNow is not set
							
								$this->confirmation->init($this->conf,$this); // Initialise the new instance to make cObj available in all other functions.
								if(!$this->check()) { // if all needed fields in backend where filled
									if(!$this->mandatory->main($this->conf,$this->sessionfields)) { // Mandatory check negative
										$this->content = $this->confirmation->main($this->content,$this->conf); // Call the confirmation function.
									} else { // Mandatory check positive
										$this->content = $this->mandatory->main($this->conf,$this->sessionfields); // Call the mandatory function
									}
								}
								else $this->content = $this->check(); // Error message
								
							} else { // sendNow is set - so call submit function
							
								$this->submit->init($this->conf,$this); // Initialise the new instance to make cObj available in all other functions.
								if(!$this->check()) { // if all needed fields in backend where filled
									if(!$this->mandatory->main($this->conf,$this->sessionfields)) { // Mandatory check negative
										$this->content = $this->submit->main($this->content,$this->conf); // Call the submit function.
									} else { // Mandatory check positive
										$this->content = $this->mandatory->main($this->conf,$this->sessionfields); // Call the mandatory function
									}
								}
								else $this->content = $this->check(); // Error message
								
							}
							
						} else { // No confirm page active, so start submit
							
							$this->submit->init($this->conf,$this); // Initialise the new instance to make cObj available in all other functions.
							if(!$this->check()) {
								if(!$this->mandatory->main($this->conf,$this->sessionfields)) { // Mandatory check negative
									$this->content = $this->submit->main($this->content,$this->conf); // Call the submit function.
								} else { // Mandatory check positive
									$this->content = $this->mandatory->main($this->conf,$this->sessionfields); // Call the mandatory function
								}			
							}
							else $this->content = $this->check(); // Error message
							
						}
					}
				} else { // multiple link is set, so show form again
					$this->form->init($this->conf,$this); // init
					if(!$this->check()) $this->content = $this->form->main($this->content,$this->conf); // Show form
					else $this->content = $this->check(); // Error message
				}
				/*
				if ($this->piVars['basket']) {  // this is used for onBlur in Formfields. Called by AJAX to set the values into a session.
					
					$postarray = $GLOBALS['TSFE']->fe_user->getKey("ses", "tx_powermail_pi1"); // first get the stored data
					$values = $this->piVars; // get the submitted value
					foreach($values as $k => $v) {
						if($k != 'basket') {
							$postarray[$k] = $v; // add new value or overwrite the old one
							$GLOBALS['TSFE']->fe_user->setKey("ses", "tx_powermail_pi1", $postarray); // Session aufbauen
							$GLOBALS['TSFE']->storeSessionData(); // Session speichern
						}
					}
					$session = $GLOBALS['TSFE']->fe_user->getKey("ses", "tx_powermail_pi1");
					print_r($session);
				}
				*/
			} else { // No piVars so show form
				$this->form->init($this->conf,$this); // init
				if(!$this->check()) $this->content = $this->form->main($this->content,$this->conf); // Show form
				else $this->content = $this->check(); // Error message
			}
		
		
		} else { // typenum 3131 - show js for mandatory
			include 'JSvalidation.php'; // load JSvalidation.php from pi1 folder (load dynamic js)
			
			return $validationJS;
		}
		

		return $this->pi_wrapInBaseClass($this->content);
	}
	
	
	// Function check() checks if all needed fields are filled in backend
	function check() {
		$error = ''; // init
		$prefix = $this->pi_getLL('error_check_prefix','<strong>PowerMail error</strong> - Please fill in this backend field: ');
		if (!$this->cObj->data['tx_powermail_subject_r']) { // If subject of receiver is not set
			$error .= $prefix.'<b>'.$this->pi_getLL('error_check_subject_r','<strong>Email receiver subject</strong>').'</b><br />'; // Error MSG
		}
		if (!$this->cObj->data['tx_powermail_recipient'] && !$this->cObj->data['tx_powermail_recip_id'] && !$this->cObj->data['tx_powermail_recip_field'] && !$this->cObj->data['tx_powermail_query']) { // If email of receiver is not set
			$error .= $prefix.'<b>'.$this->pi_getLL('error_check_recipient','<strong>Email address of receiver</strong>').'</b><br />'; // Error MSG
		}
		return $error;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_pi1.php']);
}

?>
