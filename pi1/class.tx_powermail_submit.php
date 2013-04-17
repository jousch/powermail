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
require_once(PATH_t3lib.'class.t3lib_htmlmail.php');
require_once(str_replace('../','',t3lib_extMgm::extRelPath('powermail')).'lib/class.tx_powermail_functions_div.php'); // file for div functions
require_once(str_replace('../','',t3lib_extMgm::extRelPath('powermail')).'lib/class.tx_powermail_markers.php'); // file for marker functions


class tx_powermail_submit extends tslib_pibase {
	var $prefixId      = 'tx_powermail_submit';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_powermail_pi1.submit.inc.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'powermail';	// The extension key.
	var $pi_checkCHash = true;
	var $locallangmarker_prefix = 'locallangmarker_'; // prefix for automatic locallangmarker
	var $noreply_prefix = 'noreply'; // prefix for email sender if not defined (default noreply@currentdomain.com)
	var $email_send = 1; // Enable email send function (disable for testing only)
	var $dbInsert = 1; // Enable db insert of every sent item (disable for testing only)

	function main($content,$conf){
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexform(); // Init and get the flexform data of the plugin

		// Instances
		$this->htmlMail = t3lib_div::makeInstance('t3lib_htmlmail'); // New object: TYPO3 mail functions
		$this->div_functions = t3lib_div::makeInstance('tx_powermail_functions_div'); // New object: div functions
		$this->markers = t3lib_div::makeInstance('tx_powermail_markers'); // New object: TYPO3 mail functions
		$this->markers->init($this->conf,$this); // Initialise the new instance to make cObj available in all other functions.
		
		// Configuration
		$this->sessiondata = $GLOBALS['TSFE']->fe_user->getKey('ses',$this->extKey.'_'.$this->pibase->cObj->data['uid']); // Get piVars from session
		$this->sender = ($this->pibase->cObj->data['tx_powermail_sender'] ? $this->sessiondata[$this->pibase->cObj->data['tx_powermail_sender']] : $this->noreply_prefix.'@'.$_SERVER['SERVER_NAME']); // email sender
		$this->receiver = $this->emailReceiver(); // Receiver mail
		$this->subject_r = $this->pibase->cObj->data['tx_powermail_subject_r']; // Subject of mails (receiver)
		$this->subject_s = $this->pibase->cObj->data['tx_powermail_subject_s']; // Subject of mails (sender)
		
		// Templates
		$this->tmpl = array(); $this->mailcontent = array();
		$this->tmpl['thx'] = $this->pibase->cObj->getSubpart(tslib_cObj::fileResource($this->conf['template.']['thxMessage']),"###POWERMAIL_THX###"); // Load HTML Template: THX (works on subpart ###POWERMAIL_THX###)
		$this->tmpl['all'] = $this->pibase->cObj->getSubpart(tslib_cObj::fileResource($this->conf['template.']['all']),"###POWERMAIL_ALL###"); // Load HTML Template: ALL (works on subpart ###POWERMAIL_ALL###)
		$this->tmpl['emails']['all'] = tslib_cObj::fileResource($this->conf['template.']['emails']); // Load HTML Template: Emails
		
		
		// 1. Set $this->markerArray
		$this->markerArray = $this->markers->GetMarkerArray(); // Fill markerArray
 		
		// 2. add hook for manipulation of data after E-Mails where sent
		if(!$this->hook_submit_beforeEmails()) { // All is ok (no spam maybe)
			
			if($this->conf['allow.']['email2receiver']) $this->sendMail('recipient_mail'); // 2a. Email: Generate the Mail for the recipient (if allowed via TS)
			if($this->conf['allow.']['email2sender']) $this->sendMail('sender_mail'); // 2b. Email: Generate the Mail for the sender (if allowed via TS)
			if($this->conf['allow.']['dblog']) $this->saveMail(); // 2c. Safe values to DB (if allowed via TS)
			
		} else { // Spam hook is true (maybe spam recognized)
			$this->markerArray = array(); // clear markerArray
			$this->markerArray['###POWERMAIL_THX_ERROR###'] = $this->hook_submit_beforeEmails(); // Fill ###POWERMAIL_THX_MESSAGE### with error message from Hook
		}
		
		// 3. Return Message to FE
		$this->hook_submit_afterEmails(); // add hook for manipulation of data after E-Mails where sent
		$this->content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['thx'],$this->markerArray); // substitute Marker in Template
		$this->content = preg_replace_callback ( // Automaticly fill locallangmarkers with fitting value of locallang.xml
			'#\#\#\#POWERMAIL_LOCALLANG_(.*)\#\#\##Uis', // regulare expression
			array($this->markers,'DynamicLocalLangMarker'), // open function
			$this->content // current content
		);
		$this->content = preg_replace("|###.*###|i","",$this->content); // Finally clear not filled markers
		
		// 4. Now clear the session if option is set in TS
		$this->clearSession();
		
		// 5. Clear sessions of captcha
		$this->clearCaptchaSession();
		
		// 6. Redirect if wanted
		$this->redirect();
		
		return $this->content; // return HTML for THX Message
	}

	
	// Function redirect() forward the user to a new location after submit
	function redirect() {
		if($this->pibase->cObj->data['tx_powermail_redirect']) { // only if redirect target was set in backend
				
			$typolink_conf = array (
			  "returnLast" => "url", // Give me only the string
			  "parameter" => $this->pibase->cObj->data['tx_powermail_redirect'], // target pid
			  "useCacheHash" => 0 // Don't use cache
			);
			$link = $this->pibase->cObj->typolink('x', $typolink_conf); // Create target url
			
			if (intval($this->pibase->cObj->data['tx_powermail_redirect']) > 0 || strpos($this->pibase->cObj->data['tx_powermail_redirect'],'fileadmin/') !== false) { // PID (intern link) OR file
				$link = $GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'].$link; // Add baseurl to link
			} 
			elseif (t3lib_div::validEmail($this->pibase->cObj->data['tx_powermail_redirect'])) { // if email recognized
				$link = 'mailto:'.$link; // add mailto: 
			}
			
			// Header for redirect
			header("Location: $link"); 
			header("Connection: close");
	
		}
	}
	
	
	// Function sendMail() generates mail for sender and receiver
	function sendMail($subpart) {

		// Configuration
		$this->tmpl['emails'][$subpart] = $this->pibase->cObj->getSubpart($this->tmpl['emails']['all'],'###POWERMAIL_'.strtoupper($subpart).'###'); // Content for HTML Template
		$this->mailcontent[$subpart] = $this->pibase->cObj->substituteMarkerArrayCached($this->tmpl['emails'][$subpart],$this->markerArray); // substitute markerArray for HTML content
		$this->mailcontent[$subpart] = preg_replace_callback ( // Automaticly fill locallangmarkers with fitting value of locallang.xml
			'#\#\#\#POWERMAIL_LOCALLANG_(.*)\#\#\##Uis', // regulare expression
			array($this->markers,'DynamicLocalLangMarker'), // open function
			$this->mailcontent[$subpart] // current content
		);
		$this->mailcontent[$subpart] = preg_replace("|###.*###|i","",$this->mailcontent[$subpart]); // Finally clear not filled markers
		if ($subpart == 'recipient_mail') { // default settings: mail to receiver
			$receiver = $this->receiver; // set receiver
			$sender = $this->sender; // set sender
			$subject = $this->subject_r; // set subject
		} elseif ($subpart == 'sender_mail') { // extended settings: mail to sender
			$receiver = $this->sender; // set receiver
			$sender = $this->receiver; // set sender
			$subject = $this->subject_s; // set subject
		}
		
		$this->htmlMail->start();
		$this->htmlMail->recipient = $receiver;
		$this->htmlMail->subject = $subject;
		$this->htmlMail->from_email = $sender;
		$this->htmlMail->from_name = $sender;
		$this->htmlMail->returnPath = $sender;
		$this->htmlMail->replyto_email = ''; // clear replyto email
		$this->htmlMail->replyto_name = ''; // clear replyto name
		$this->htmlMail->charset = $GLOBALS['TSFE']->metaCharset; // set current charset
		$this->htmlMail->defaultCharset = $GLOBALS['TSFE']->metaCharset; // set current charset
		$this->htmlMail->addPlain($this->mailcontent[$subpart]);
		$this->htmlMail->setHTML($this->htmlMail->encodeMsg($this->mailcontent[$subpart]));
		$this->htmlMail->send($receiver);
	}
	
	
	// Function saveMail() to save piVars and some more infos to DB (tx_powermail_mails)
	function saveMail() {
		
		// Configuration
		$this->save_PID = $GLOBALS['TSFE']->id; // PID where to save: Take current page
		if($this->conf['PID.']['dblog']) $this->save_PID = $this->conf['PID.']['dblog']; // PID where to save: Get it from TS if set
		
		// DB entry for table Tabelle: tx_powermail_mails
		$db_values = array (
			'pid' => $this->save_PID, // PID
			'tstamp' => time(), // save current time
			'crdate' => time(), // save current time
			'formid' => $this->pibase->cObj->data['uid'],
			'recipient' => $this->receiver,
			'subject_r' => $this->subject_r,
			'sender' => $this->sender,
			'content' => trim($this->mailcontent['recipient_mail']),
			'piVars' => t3lib_div::array2xml($this->sessiondata,'',0,'piVars'),
			'senderIP' => $_SERVER['REMOTE_ADDR'],
			'UserAgent' => $_SERVER['HTTP_USER_AGENT'],
			'Referer' => $_SERVER['HTTP_REFERER'],
			'SP_TZ' => $_SERVER['SP_TZ']
		);
		if($this->dbInsert) $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_powermail_mails',$db_values); // DB entry
	}
	
	
	// Function emailReceiver() returns comma-separated list of email receivers
	function emailReceiver() {
		
		if ($this->pibase->cObj->data['tx_powermail_recipient']) { // If receivers are listed in field receiver
			$emails = str_replace(array("\r\n","\n\r","\n","\r"),',',$this->pibase->cObj->data['tx_powermail_recipient']); // commaseparated list of emails
		}
		
		elseif ($this->pibase->cObj->data['tx_powermail_recip_id']) { // If emails from table was chosen
			$emails = $this->pibase->cObj->data['tx_powermail_recip_id']; // commaseparated list of emails
		}
		
		elseif ($this->pibase->cObj->data['tx_powermail_recip_field']) { // If own select query is chosen
			//$emails = $this->pibase->cObj->data['tx_powermail_recip_field']; // commaseparated list of emails
			echo 'TODO'; // TODO
		}
		
		if (isset($emails)) return $emails; // return email receiver list
		else return false;
	}
	
	
	// Function hook_submit_beforeEmails() to add a hook at the end of this file to manipulate markers and content before emails where sent
	function hook_submit_beforeEmails() {
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitBeforeMarkerHook'])) { // Adds hook for processing of extra global markers
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitBeforeMarkerHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				return $_procObj->PM_SubmitBeforeMarkerHook($this,$this->markerArray,$this->sessiondata); // Get new marker Array from other extensions - if TRUE, don't send mails (maybe spam)
			}
		} else { // if hook is not set
			return FALSE; // Return False is default (no spam, so emails could be sent)
		}
	}
	

	// Function hook_submit_afterEmails() to add a hook at the end of this file to manipulate markers and content after emails where sent
	function hook_submit_afterEmails() {
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitAfterMarkerHook'])) { // Adds hook for processing of extra global markers
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitAfterMarkerHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_SubmitAfterMarkerHook($this,$this->markerArray,$this->sessiondata); // Get new marker Array from other extensions
			}
		}
	}
	
	// Function to clear the Session after submitting the form. Will only be cleared when option is selected in Constant-Editor oder set by TS
	function clearSession() {
		if($this->conf['clear.']['session'] == 1) {
			$GLOBALS['TSFE']->fe_user->setKey("ses", $this->extKey.'_'.$this->pibase->cObj->data['uid'], array()); // Generate Session without ERRORS
			$GLOBALS['TSFE']->storeSessionData(); // Save session*/
		}
	}
	
	// Function clearCaptchaSession() clears already filled captcha sessions from captcha or sr_freecap
	function clearCaptchaSession() {
		session_start(); // start session
		if(isset($_SESSION['tx_captcha_string'])) $_SESSION['tx_captcha_string'] = ''; // clear session of captcha
		if(isset($_SESSION['sr_freecap_attempts'])) $_SESSION['sr_freecap_attempts'] = 0; // clear session of sr_freecap
		if(isset($_SESSION['sr_freecap_word_hash'])) $_SESSION['sr_freecap_word_hash'] = false; // clear session of sr_freecap
	}

	//function for initialisation.
	// to call cObj, make $this->pibase->cObj->function()
	function init(&$conf,&$pibase) {
		$this->conf = $conf;
		$this->pibase = $pibase;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_submit.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_submit.php']);
}

?>