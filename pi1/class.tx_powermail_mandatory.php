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
require_once(str_replace('../','',t3lib_extMgm::extRelPath('powermail')).'lib/class.tx_powermail_markers.php'); // file for marker functions

class tx_powermail_mandatory extends tslib_pibase {
	var $extKey        = 'powermail';	// The extension key.
	var $pi_checkCHash = true;
    var $scriptRelPath = 'pi1/class.tx_powermail_mandatory.php';    // Path to pi1 to get locallang.xml from pi1 folder

	function main($conf,$sessionfields) {
		$this->conf = $conf;
		$this->sessionfields = $sessionfields;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexform(); // Init and get the flexform data of the plugin
		
		// Instances
		$this->markers = t3lib_div::makeInstance('tx_powermail_markers'); // New object: TYPO3 mail functions
		$this->markers->init($this->conf,$this); // Initialise the new instance to make cObj available in all other functions.
		
		// Template
		$this->tmpl = array();
		$this->tmpl['mandatory']['all'] = $this->pibase->cObj->getSubpart(tslib_cObj::fileResource($this->conf['template.']['mandatory']),'###POWERMAIL_MANDATORY_ALL###'); // Load HTML Template outer (work on subpart)
		$this->tmpl['mandatory']['item'] = $this->pibase->cObj->getSubpart($this->tmpl['mandatory']['all'],'###ITEM###'); // Load HTML Template inner (work on subpart)
		
		// Fill Markers
		$content_item = ''; $this->innerMarkerArray = array(); $fieldarray = array(); $this->error = 0;
		$this->markerArray = $this->markers->GetMarkerArray(); // Fill markerArray
		$this->markerArray['###POWERMAIL_TARGET###'] = $this->pibase->cObj->typolink('x',array("returnLast"=>"url","parameter"=>$GLOBALS['TSFE']->id,"useCacheHash"=>1)); // Fill Marker with action parameter
		$this->markerArray['###POWERMAIL_NAME###'] = $this->pibase->cObj->data['tx_powermail_title'].'_mandatory'; // Fill Marker with formname
		$this->markerArray['###POWERMAIL_METHOD###'] = $this->conf['form.']['method']; // Form method
		
		// Different check functions
		$this->captchaCheck(); // Captcha Check
		$this->emailCheck(); // Email Check
		$this->regulareExpressions(); // Regulare Expression Check
		
		// Give me all fields of current content uid
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'f.uid,f.title,f.flexform',
			'tx_powermail_fields f LEFT JOIN tx_powermail_fieldsets fs ON (f.fieldset = fs.uid) LEFT JOIN tt_content c ON (fs.tt_content = c.uid)',
			$where_clause = 'fs.tt_content = '.$this->pibase->cObj->data['uid'].' AND fs.hidden = 0 AND fs.deleted = 0 AND f.hidden = 0 AND f.deleted = 0 AND c.hidden = 0 AND c.deleted = 0',
			$groupBy = '',
			$orderBy = 'fs.sorting ASC, f.sorting ASC',
			$limit
		);
		if ($res) { // If there is a result
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // One loop for every field
				if($this->pi_getFFvalue(t3lib_div::xml2array($row['flexform']),'mandatory') == 1) { // if in current xml mandatory == 1
					if(!trim($this->sessionfields['uid'.$row['uid']]) || !isset($this->sessionfields['uid'.$row['uid']])) { // only if current value is not set in session (piVars)
						$this->sessionfields['ERROR'][$row['uid']][] = $this->pi_getLL('locallangmarker_mandatory_emptyfield').' <b>'.$row['title'].'</b>'; // set current error to sessionlist
					}
				}
			}
		}
		
		// Check for errors
		if(isset($this->sessionfields['ERROR'])) {
			foreach($this->sessionfields['ERROR'] as $key1 => $value1) { // one loop for every field with an error
				if(isset($this->sessionfields['ERROR'][$key1])) {
					foreach($this->sessionfields['ERROR'][$key1] as $key2 => $value2) { // one loop for every error on current field
						$this->error = 1; // mark as error
						$this->innerMarkerArray['###POWERMAIL_MANDATORY_LABEL###'] = $value2; // current field title (label)
						$content_item .= $this->pibase->cObj->substituteMarkerArrayCached($this->tmpl['mandatory']['item'], $this->innerMarkerArray); // add to content_item
					}
				}
			}
		}
		$subpartArray['###CONTENT###'] = $content_item;
		
		// Return
		$this->hook(); // adds hook
		$this->content = $this->pibase->cObj->substituteMarkerArrayCached($this->tmpl['mandatory']['all'],$this->markerArray,$subpartArray); // substitute Marker in Template
		$this->content = preg_replace_callback ( // Automaticly fill locallangmarkers with fitting value of locallang.xml
			'#\#\#\#POWERMAIL_LOCALLANG_(.*)\#\#\##Uis', // regulare expression
			array($this->markers,'DynamicLocalLangMarker'), // open function
			$this->content // current content
		);
		$this->content = preg_replace("|###.*###|i","",$this->content); // Finally clear not filled markers
		if($this->error == 1) { // if there is an error
			$this->clearErrorsInSession();
			return $this->content; // return HTML
		}
	}
	
	
	// Functions regulareExpressions() checks values
	function regulareExpressions() {
		// Config - set regulare expressions for autocheck
		$autoarray = array (
			'email' => "^[_a-z0-9]+(\.[_a-z0-9-]+)*@([a-z0-9-]+\.)+([a-z0-9]{2,4})$^",
			'url' => "^(http://)?([a-z0-9-]+\.)+([a-z0-9-]{2,3})$^",
			'numbers' => "/[0-9]+$/",
			'phone' => "/[0-9\/+-]+$/",
			'alphanum' => "/[a-zA-Z0-9]/"
		);
		
		// Let's go and check
		if (isset($this->conf['validate.']) && is_array($this->conf['validate.'])) { // Only if any validation is set per typoscript
			foreach ($this->conf['validate.'] as $key => $value) { // One loop for every validation
				// autocheck
				if ($this->conf['validate.'][$key]['auto']) { // If autocheck of current value is active
					if (isset($autoarray[$this->conf['validate.'][$key]['auto']])) { // if regulare expression in $autoarray
						if ($this->sessionfields[str_replace('.','',$key)]) { // if there is a value in the field, which to check
							
							// Check
							if (!preg_match($autoarray[$this->conf['validate.'][$key]['auto']], $this->sessionfields[str_replace('.','',$key)])) { // If check failed
								$this->sessionfields['ERROR'][str_replace('.','',$key)][] = ($this->conf['validate.'][$key]['errormsg']?$this->conf['validate.'][$key]['errormsg']:$this->pi_getLL('error_expression_validation')); // write errormessage
							}
							
						}
					}
				} elseif ($this->conf['validate.'][$key]['expression']) { // regulare expression
					if ($this->sessionfields[str_replace('.','',$key)]) { // if there is a value in the field, which to check
						
						// Check
						if (!preg_match($this->conf['validate.'][$key]['expression'], $this->sessionfields[str_replace('.','',$key)])) { // If check failed
							$this->sessionfields['ERROR'][str_replace('.','',$key)][] = ($this->conf['validate.'][$key]['errormsg']?$this->conf['validate.'][$key]['errormsg']:$this->pi_getLL('error_expression_validation')); // write errormessage
						}
						
					}
				}
			}
		}
	}
	
	
	// Function emailCheck() checks if sender email address is a real email address, if not write error to session
	function emailCheck() {
		if($this->pibase->cObj->data['tx_powermail_sender']) { // If email address from sender is set in backend
			if($this->sessionfields[$this->pibase->cObj->data['tx_powermail_sender']]) { // if there is content in the email sender field
				if(!t3lib_div::validEmail($this->sessionfields[$this->pibase->cObj->data['tx_powermail_sender']])) { // Value is not an email address
					$this->sessionfields['ERROR'][str_replace('uid','',$this->pibase->cObj->data['tx_powermail_sender'])][] = $this->pi_getLL('error_validemail'); // write error message to session
				}
			}
		}
	}
	
	
	// Function captchaCheck check if captcha fields are within current content and set errof if value is wrong
	function captchaCheck() {
		if(t3lib_extMgm::isLoaded('captcha',0) || t3lib_extMgm::isLoaded('sr_freecap',0)) { // only if a captcha extension is loaded
		
			// Give me all captcha fields of current tt_content
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
				'f.uid',
				'tx_powermail_fields f LEFT JOIN tx_powermail_fieldsets fs ON (f.fieldset = fs.uid) LEFT JOIN tt_content c ON (fs.tt_content = c.uid)',
				$where_clause = 'f.formtype = "captcha" AND fs.tt_content = '.$this->pibase->cObj->data['uid'].' AND fs.hidden = 0 AND fs.deleted = 0 AND f.hidden = 0 AND f.deleted = 0 AND c.hidden = 0 AND c.deleted = 0',
				$groupBy = '',
				$orderBy = 'fs.sorting ASC, f.sorting ASC',
				$limit = 1
			);
			if ($res) { // If there is a result
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // One loop for every captcha field
					
					if(t3lib_extMgm::isLoaded('sr_freecap',0)) { // use sr_freecap if available
					
						session_start(); // start session
						if($this->sessionfields['uid'.$row['uid']] == '') { // if captcha value is empty
							$this->sessionfields['ERROR'][$row['uid']][] = $this->pi_getLL('error_captcha_empty'); // write error message to session
						}
						
						elseif (
							($_SESSION['sr_freecap_word_hash'] != md5($this->sessionfields['uid'.$row['uid']])) && 
							($_SESSION['sr_freecap_word_hash'] != md5($this->sessionfields['uid'.$row['uid']]."\n"))) {
							
							$this->sessionfields['ERROR'][$row['uid']][] = $this->pi_getLL('error_captcha_wrong'); // write error message to session
							
						}
						// clear session values in submit.php
						//$_SESSION['sr_freecap_attempts'] = 0; // clear values
						//$_SESSION['sr_freecap_word_hash'] = false; // clear values
					}
					
					elseif (t3lib_extMgm::isLoaded('captcha',0)) { // use captcha if available
					
						session_start(); // start session
						$captchaStr = $_SESSION['tx_captcha_string']; // get captcha value from session
						// clear session values in submit.php
						//$_SESSION['tx_captcha_string'] = ''; // clear value in session
						
						if ($this->sessionfields['uid'.$row['uid']] == '') { // if captcha value is empty
							$this->sessionfields['ERROR'][$row['uid']][] = $this->pi_getLL('error_captcha_empty'); // write error message to session
						}
						
						elseif ($this->sessionfields['uid'.$row['uid']] != $captchaStr) { // if captcha value is wrong
							$this->sessionfields['ERROR'][$row['uid']][] = $this->pi_getLL('error_captcha_wrong'); // write error message to session
						}
						
					}
				}
			}
			
		}
	}
	
	
	// Function clearErrorsInSession() removes all global errors, which are marked as an error in the session
	function clearErrorsInSession() {
		// Set Session (overwrite all values)
		unset($this->sessionfields['ERROR']); // remove all error messages
		$GLOBALS['TSFE']->fe_user->setKey("ses", $this->extKey.'_'.$this->pibase->cObj->data['uid'], $this->sessionfields); // Generate Session without ERRORS
		$GLOBALS['TSFE']->storeSessionData(); // Save session
	}
	
	
	// Function hook() to enable manipulation datas with another extension(s)
	function hook() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_MandatoryHook'])) { // Adds hook for processing of extra global markers
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_MandatoryHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_ConfirmationHook($this->error,$this->markerArray,$this->innerMarkerArray,$this->sessionfields,$this); // Open function to manipulate data
			}
		}
	}


	//function for initialisation.
	// to call cObj, make $this->pibase->cObj->function()
	function init(&$conf,&$pibase) {
		$this->conf = $conf;
		$this->pibase = $pibase;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_mandatory.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_mandatory.php']);
}

?>
