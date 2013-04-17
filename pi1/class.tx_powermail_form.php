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

require_once(PATH_tslib.'class.tslib_pibase.php'); // get pibase
require_once('class.tx_powermail_html.php'); // get html and field functions
require_once(str_replace('../','',t3lib_extMgm::extRelPath('powermail')).'lib/class.tx_powermail_functions_div.php'); // file for div functions
require_once(str_replace('../','',t3lib_extMgm::extRelPath('powermail')).'lib/class.tx_powermail_markers.php'); // file for marker functions
require_once(str_replace('../','',t3lib_extMgm::extRelPath('powermail')).'lib/class.tx_powermail_sessions.php'); // load session class


class tx_powermail_form extends tslib_pibase {
	var $prefixId      = 'tx_powermail_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_powermail_form.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'powermail';	// The extension key.
	var $pi_checkCHash = true;

	// Function main chooses what to show
	function main($content, $conf) {
		if ($this->pibase->cObj->data['tx_powermail_multiple'] == 2) { // If multiple (PHP) active (load tmpl_multiple.html)
			
			// Set limit
			$limitArray = array(0,1); // If multiple (PHP) set limit
			if(isset($this->piVars['multiple'])) $limitArray[0] = ($this->piVars['multiple'] - 1); // Set current fieldset
			$limit = $limitArray[0].','.$limitArray[1]; // e.g. 0,1
		
		} elseif ($this->pibase->cObj->data['tx_powermail_multiple'] == 0 || $this->pibase->cObj->data['tx_powermail_multiple'] == 1) { // Standardmode
			
			$limit = ''; // no limit for SQL select
			
		} else return 'Wrong multiple setting ('.$this->pibase->cObj->data['tx_powermail_multiple'].') in backend'; // Errormessage if wrong multiple choose
		
		return $this->form($limit); // Load only
	}
	
	
	// Function form() generates form tags and loads field
	function form($limit = '') {
		// Configuration
		$div_functions = t3lib_div::makeInstance('tx_powermail_functions_div'); // New object: div functions
		$html_input_field = t3lib_div::makeInstance('tx_powermail_html'); // New object: html generation of input fields

		$this->tmpl['all'] = tslib_cObj::fileResource($this->conf['template.']['formWrap']); // Load HTML Template
		$this->tmpl['formwrap']['all'] = $this->pibase->cObj->getSubpart($this->tmpl['all'],'###POWERMAIL_FORMWRAP###'); // work on subpart 1
		$this->tmpl['formwrap']['item'] = $this->pibase->cObj->getSubpart($this->tmpl['formwrap']['all'],'###POWERMAIL_ITEM###'); // work on subpart 2

		// Form tag generation
		$this->InnerMarkerArray = array(); $this->OuterMarkerArray = array(); $this->content_item = ''; // init
		$this->OuterMarkerArray['###POWERMAIL_TARGET###'] = $this->pibase->cObj->typolink('x',array("returnLast"=>"url","parameter"=>$GLOBALS['TSFE']->id,"additionalParams"=>'&tx_powermail_pi1[mailID]='.$this->pibase->cObj->data['uid'],"useCacheHash"=>1)); // Fill Marker with action parameter
		$this->OuterMarkerArray['###POWERMAIL_NAME###'] = $this->pibase->cObj->data['tx_powermail_title']; // Fill Marker with formname
		$this->OuterMarkerArray['###POWERMAIL_METHOD###'] = $this->conf['form.']['method']; // Form method
		$this->OuterMarkerArray['###POWERMAIL_FORM_UID###'] = $this->pibase->cObj->data['uid']; // Form method
		if($limit) { // If multiple is set
			$this->OuterMarkerArray['###POWERMAIL_MULTIPLE_BACKLINK###'] = $this->multipleLink(-1); // Backward Link (-1)
			$this->OuterMarkerArray['###POWERMAIL_MULTIPLE_FORWARDLINK###'] = $this->multipleLink(1); // Forward Link (+1)
			$this->OuterMarkerArray['###POWERMAIL_MULTIPLE_PAGEBROWSER###'] = $this->multipleLink(0); // Pagebrowser
			if($this->multiple['numberoffieldsets'] != $this->multiple['currentpage']) { // On last fieldset, don't overwrite Target
				$this->OuterMarkerArray['###POWERMAIL_TARGET###'] = $this->pibase->cObj->typolink('x',array("returnLast"=>"url","parameter"=>$GLOBALS['TSFE']->id,"additionalParams"=>'&tx_powermail_pi1[mailID]='.$this->pibase->cObj->data['uid'].'&tx_powermail_pi1[multiple]='.($this->multiple['currentpage'] + 1),"useCacheHash"=>1)); // Overwrite Target
			}
		}

		// Give me all needed fieldsets
		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'uid,title',
			'tx_powermail_fieldsets',
			$where_clause = 'tt_content = '.$this->pibase->cObj->data['uid'].tslib_cObj::enableFields('tx_powermail_fieldsets'),
			$groupBy = '',
			$orderBy = 'sorting',
			$limit
		);
		if ($res1) { // If there is a result
			while($row_fs = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) { // One loop for every fieldset
				$this->InnerMarkerArray['###POWERMAIL_FIELDS###'] = ''; // init

				// Give me all fields in current fieldset, which are related to current content
				$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
					'fs.uid fs_uid,f.uid f_uid,fs.felder fs_fields,fs.title fs_title,f.title f_title,f.formtype f_type,f.flexform f_field,c.tx_powermail_title c_title',
					'tx_powermail_fieldsets fs LEFT JOIN tx_powermail_fields f ON (fs.uid = f.fieldset) LEFT JOIN tt_content c ON (fs.tt_content = c.uid)',
					$where_clause = 'fs.deleted = 0 AND fs.hidden = 0 AND fs.tt_content = '.$this->pibase->cObj->data['uid'].' AND f.hidden = 0 AND f.deleted = 0 AND f.fieldset = '.$row_fs['uid'],
					$groupBy = '',
					$orderBy = 'fs.sorting, f.sorting',
					$limit1 = ''
				);
				if ($res2) { // If there is a result
					$html_input_field->init($this->conf,$this);
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2)) { // One loop for every field
						$this->InnerMarkerArray['###POWERMAIL_FIELDS###'] .= $html_input_field->main($conf,$row); // Get HTML code for each field
					}
				}

				$this->InnerMarkerArray['###POWERMAIL_FIELDSETNAME###'] = $row_fs['title']; // Name of fieldset
				$this->InnerMarkerArray['###POWERMAIL_FIELDSETNAME_small###'] = $div_functions->clearName($row_fs['title'],1,32); // Fieldsetname clear (strtolower = 1 / cut after 32 letters)
				$this->InnerMarkerArray['###POWERMAIL_FIELDSET_UID###'] = $row_fs['uid']; // uid of fieldset
				$this->content_item .= $this->pibase->cObj->substituteMarkerArrayCached($this->tmpl['formwrap']['item'],$this->InnerMarkerArray);
			}
		}

		$this->subpartArray = array('###POWERMAIL_CONTENT###' => $this->content_item); // work on subpart 3
		
		$this->hook(); // adds hook
		$this->contentForm = $this->pibase->cObj->substituteMarkerArrayCached($this->tmpl['formwrap']['all'],$this->OuterMarkerArray,$this->subpartArray); // substitute Marker in Template
		$this->contentForm = preg_replace("|###.*###|i","",$this->contentForm); // Finally clear not filled markers
		return $this->contentForm; // return HTML
	}
	
	
	// Function multipleLink() generates links to switch between fieldset-pages
	function multipleLink($add = 0) {
		// Get number of pages of current form
		$this->multiple = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'count(*) no',
			'tx_powermail_fieldsets',
			$where_clause = 'tt_content = '.$this->pibase->cObj->data['uid'].tslib_cObj::enableFields('tx_powermail_fieldsets'),
			$groupBy = '',
			$orderBy = '',
			$limit
		);
		if ($res) $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		
		// Configuration
		$this->multiple['numberoffieldsets'] = $row['no']; // Numbers of all fieldsets
		if(isset($this->piVars['multiple'])) $this->multiple['currentpage'] = $this->piVars['multiple']; // Currentpage
		else $this->multiple['currentpage'] = 1; // Currentpage = 1 if not set
		
		if ($add > 0) { // Forward link
		
			if($this->multiple['numberoffieldsets'] != $this->multiple['currentpage']) { // If current fieldset is not the latest
				$content = '<input type="submit" value="'.$this->pi_getLL('multiple_forward').'" class="tx_powermail_pi1_submitmultiple_forward" />';
			} else $content = ''; // clear it if it's not needed
			
		} elseif ($add < 0) { // Backward link
		
			if($this->multiple['currentpage'] > 1) { // If current fieldset is not the first
				$link = $this->pibase->cObj->typolink('x',array('parameter'=>$GLOBALS['TSFE']->id,'returnLast'=>'url', 'additionalParams'=>'&tx_powermail_pi1[multiple]='.($this->multiple['currentpage'] + $add).'&tx_powermail_pi1[mailID]='.$this->pibase->cObj->data['uid'],'useCacheHash' => 1)); // Create target url
				$content = '<input type="button" value="'.$this->pi_getLL('multiple_back').'" onclick="location=\''.$link.'\'" class="tx_powermail_pi1_submitmultiple_forward" />';
			}
			else $content = ''; // clear it if it's not needed
		
		} elseif ($add == 0) {
		
			$content = 'Pagebrowser';
		
		} else { // Error
		
			$content = 'ERROR in function multipleLink';
		
		}
		
		return $content;
	}
	
	
	// Function hook() to enable manipulation datas with another extension(s)
	function hook() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FormWrapMarkerHook'])) { // Adds hook for processing of extra global markers
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FormWrapMarkerHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_FormWrapMarkerHook($this); // Open function to manipulate datas
			}
		}
	}

	function init(&$conf,&$pibase) {
		$this->conf = $conf;
		$this->pibase = $pibase;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_form.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_form.php']);
}

?>
