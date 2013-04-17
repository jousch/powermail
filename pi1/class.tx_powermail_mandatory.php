<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Mischa Heißmann, Alexander Kellner <typo3@heissmann.org, alexander.kellner@wunschtacho.de>
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

	function main($conf,$sessionfields){
		$this->conf = $conf;
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
		$content_item = ''; $this->innerMarkerArray = array(); $fieldarray = array(); $error = 0;
		$this->markerArray = $this->markers->GetMarkerArray(); // Fill markerArray
		$this->markerArray['###POWERMAIL_TARGET###'] = $this->pibase->cObj->typolink('x',array("returnLast"=>"url","parameter"=>$GLOBALS['TSFE']->id,"useCacheHash"=>1)); // Fill Marker with action parameter
		$this->markerArray['###POWERMAIL_NAME###'] = $this->pibase->cObj->data['tx_powermail_title'].'_mandatory'; // Fill Marker with formname
		$this->markerArray['###POWERMAIL_METHOD###'] = $this->conf['form.']['method']; // Form method
		
		
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
					if(!trim($sessionfields['uid'.$row['uid']]) || !isset($sessionfields['uid'.$row['uid']])) { // only if current value is not set in session (piVars)
						$error = 1; // min. 1 field was not filled
						$this->innerMarkerArray['###POWERMAIL_MANDATORY_LABEL###'] = $row['title']; // current field title (label)
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
		if($error == 1) return $this->content; // return HTML
	}
	
	
	// Function hook() to enable manipulation datas with another extension(s)
	function hook() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_MandatoryHook'])) { // Adds hook for processing of extra global markers
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_MandatoryHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_ConfirmationHook($this); // Open function to manipulate data
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
