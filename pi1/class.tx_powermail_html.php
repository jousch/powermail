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
require_once('class.tx_powermail_renderWizard.php');
require_once(str_replace('../','',t3lib_extMgm::extRelPath('powermail')).'lib/class.tx_powermail_functions_div.php'); // file for div functions
require_once(str_replace('../','',t3lib_extMgm::extRelPath('powermail')).'lib/class.tx_powermail_sessions.php'); // load session class

/**
 * Plugin 'Powermail' for the 'powermail' extension.
 *
 * @author	Mischa Heißmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@einpraegsam.net>
 * @package	TYPO3
 * @subpackage	tx_powermail
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   66: class tx_powermail_pi1_html extends tslib_pibase
 *   81:     function init(&$conf,&$pibase)
 *   94:     function main($content, $conf, $row)
 *  173:     function html_text()
 *  186:     function html_textarea()
 *  201:     function html_select()
 *  226:     function html_check()
 *  259:     function html_radio()
 *  291:     function html_submit()
 *  306:     function html_reset()
 *  321:     function html_label()
 *  339:     function html_content()
 *  361:     function html_password()
 *  374:     function html_file()
 *  387:     function html_hidden()
 *  400:     function html_datetime()
 *  439:     function html_date()
 *  477:     function html_time()
 *  579:     function GetSessionValue($name)
 *
 * TOTAL FUNCTIONS: 18
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_powermail_html extends tslib_pibase {
	var $prefixId      = 'tx_powermail_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_powermail_form.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'powermail';	// The extension key.
	var $pi_checkCHash = true;


	/**
	 * Main function selects what kind of html tag is needed
	 *
	 * @param	[type]		$content: ...
	 * @param	[type]		$conf: ...
	 * @param	[type]		$row: ...
	 * @return	[type]		...
	 */
	function main($conf, $row) {
		$this->pibase->pi_initPIflexForm();
		$this->xml = $row['f_field'];
		$this->title = $row['f_title'];
		$this->type = $row['f_type'];
		$this->formtitle = $row['c_title'];
		$this->uid = $row['f_uid'];
		$this->fe_field = $row['f_fefield'];
		$this->tmpl = array('all' => tslib_cObj::fileResource($this->conf['template.']['fieldWrap'])); // Load HTML Template

		// Main functions
		$this->GetSessionValue($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name')); // get value from session (if any)
		$this->setGlobalMarkers(); // set global markers

		// selection
		if($row['f_type']) { // If type exists
			switch($row['f_type']) {
				case 'text':
					$content = $this->html_text(); // generate text field <input type="text"...
				break;
				case 'textarea':
					$content = $this->html_textarea(); // generate textarea <textarea...
				break;
				case 'check':
					$content = $this->html_check(); // generate textarea <input type="checkbox"
				break;
				case 'select':
					$content = $this->html_select(); // generate selectorbox <select><option>...
				break;
				case 'radio':
					$content = $this->html_radio(); // generate radio buttons <input type="radio"...
				break;
				case 'submit':
					$content = $this->html_submit(); // generate submitbutton <input type="submit"...
				break;
				case 'reset':
					$content = $this->html_reset(); // generate resetbutton <input type="reset"...
				break;
				case 'label':
					$content = $this->html_label(); // generate textlabel
				break;
				case 'html':
					$content = $this->html_html(); // generate pure html
				break;
				case 'content':
					$content = $this->html_content(); // returns page content
				break;
				case 'file':
					$content = $this->html_file(); // generate file field
				break;
				case 'password':
					$content = $this->html_password(); // generate password field
				break;
				case 'hidden':
					$content = $this->html_hidden(); // generate hidden field
				break;
				case 'datetime':
					$content = $this->html_datetime(); // generate datetime field
				break;
				case 'date':
					$content = $this->html_date(); // generate date field
				break;
				case 'time':
					$content = $this->html_time(); // generate time field
				break;
				case 'button':
					$content = $this->html_button(); // generate button field
				break;
				case 'submitgraphic':
					$content = $this->html_submitgraphic(); // generate submitgraphic button
				break;
				case 'countryselect':
					$content = $this->html_countryselect(); // generate select fields with countries from static_info_tables
				break;
				default: // errormessage if undefined tag needed
					$content = 'POWERMAIL: wrong input field required: <strong>'.$row['f_type'].'</strong> in tx_powermail_pi1_html (field uid '.$row['f_uid'].')<br />'; // errormessage
				break;
			}
		} else { // no type selected
			$content = 'POWERMAIL: <strong>no field type</strong> in backend selected (field uid '.$row['f_uid'].')<br />'; // errormessage
		}
		
		$this->html_hook($content); // adds hook
		
		if(isset($content)) return $content;
	}


	/**
	 * Function html_text() returns HTML tag for textfields
	 *
	 * @return	[type]		...
	 */
	function html_text() {
		$this->tmpl['html_text'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_TEXT###'); // work on subpart
		
		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_text'],$this->markerArray); // substitute Marker in Template
		$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_textarea() returns HTML tag for textareas
	 *
	 * @return	[type]		...
	 */
	function html_textarea() {
		$this->tmpl['html_textarea'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_TEXTAREA###'); // work on subpart

		$this->markerArray['###VALUE###'] = substr(trim($this->markerArray['###VALUE###']), 7, -1); // remove the first 7 letters (value=") and the last letter (")

		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_textarea'],$this->markerArray); // substitute Marker in Template
		$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_select() returns HTML tag for selectorbox
	 *
	 * @return	[type]		...
	 */
	function html_select() {
		$this->tmpl['html_select']['all'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_SELECT###'); // work on subpart 1
		$this->tmpl['html_select']['item'] = tslib_cObj::getSubpart($this->tmpl['html_select']['all'],'###ITEM###'); // work on subpart 2

		if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'options')) { // Only if options are set
			$optionlines = t3lib_div::trimExplode("\n",$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'options'),1); // Every row is a new option
			$content_item = '';

			for($i=0;$i<count($optionlines);$i++) { // One tag for every option
				$markerArray['###VALUE###'] = $optionlines[$i];
				$content_item .= $this->pibase->pibase->cObj->substituteMarkerArrayCached($this->tmpl['html_select']['item'], $markerArray);
			}
		}
		$subpartArray['###CONTENT###'] = $content_item; // subpart 3

		$content = $this->pibase->pibase->cObj->substituteMarkerArrayCached($this->tmpl['html_select']['all'],$this->markerArray,$subpartArray); // substitute Marker in Template
		$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_check() returns HTML tag for checkboxes
	 *
	 * @return	[type]		...
	 */
	function html_check() {
		$this->tmpl['html_check']['all'] = $this->pibase->pibase->cObj->getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_CHECK###'); // work on subpart 1
		$this->tmpl['html_check']['item'] = $this->pibase->pibase->cObj->getSubpart($this->tmpl['html_check']['all'],'###ITEM###'); // work on subpart 2

		if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'options')) { // Only if options are set
			$optionlines = t3lib_div::trimExplode("\n",$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'options'),1); // Every row is a new option
			$content_item = ''; // init

			for($i=0;$i<count($optionlines);$i++) { // One tag for every option
				$markerArray['###NAME###'] = 'name="'.$this->prefixId.'[uid'.$this->uid.']['.$i.']" '; // add name to markerArray
				$markerArray['###LABEL###'] = $optionlines[$i]; // add label
				$markerArray['###LABEL_NAME###'] = $this->div_functions->clearName($optionlines[$i]); // add labelname
				$markerArray['###ID###'] = 'id="'.$this->div_functions->clearName($optionlines[$i]).'" '; // add labelname
				$markerArray['###VALUE###'] = 'value="'.$optionlines[$i].'" '; // add labelname
				$markerArray['###CLASS###'] = 'class="powermail_'.$this->formtitle.' powermail_'.$this->type.' powermail_'.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name').'_'.$i.'" '; // add class name to markerArray
				if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'mandatory') == 1) $markerArray['###MANDATORY_SYMBOL###'] = $this->pibase->pibase->cObj->wrap($this->conf['mandatory.']['symbol'],$this->conf['mandatory.']['wrap'],'|'); // add mandatory symbol if current field is a mandatory field
				
				// ###CHECKED###
				if(isset($this->piVarsFromSession['uid'.$this->uid][$i])) $markerArray['###CHECKED###'] = 'checked="checked" '; // mark as checked
				else $markerArray['###CHECKED###'] = ''; // clear
				
				$content_item .= $this->pibase->pibase->cObj->substituteMarkerArrayCached($this->tmpl['html_check']['item'], $markerArray); // substitute Marker in Template (subpart 2)
 			}

		}
		$subpartArray = array(); // init
		$subpartArray['###CONTENT###'] = $content_item; // subpart 3
		
		// Outer Marker array
		$this->markerArray['###LABEL_MAIN###'] = $this->title; 
		$this->markerArray['###POWERMAIL_FIELD_UID###'] = $this->uid;

		$content = $this->pibase->pibase->cObj->substituteMarkerArrayCached($this->tmpl['html_check']['all'], $this->markerArray, $subpartArray); // substitute Marker in Template
		$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_radio() returns HTML tag for radio buttons
	 *
	 * @return	[type]		...
	 */
	function html_radio() {
		$this->tmpl['html_radio']['all'] = $this->pibase->pibase->cObj->getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_RADIO###'); // work on subpart 1
		$this->tmpl['html_radio']['item'] = $this->pibase->pibase->cObj->getSubpart($this->tmpl['html_radio']['all'],'###ITEM###'); // work on subpart 2

		if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'options')) { // Only if options are set
			$optionlines = t3lib_div::trimExplode("\n",$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'options'),1); // Every row is a new option
			$content_item = ''; // init

			for($i=0;$i<count($optionlines);$i++) { // One tag for every option
				$markerArray['###NAME###'] = 'name="'.$this->prefixId.'[uid'.$this->uid.']" '; // add name to markerArray
				$markerArray['###LABEL###'] = $optionlines[$i]; // add label
				$markerArray['###LABEL_NAME###'] = $this->div_functions->clearName($optionlines[$i]); // add labelname
				$markerArray['###ID###'] = 'id="'.$this->div_functions->clearName($optionlines[$i]).'" '; // add labelname
				$markerArray['###VALUE###'] = 'value="'.$optionlines[$i].'" '; // add labelname
				$markerArray['###CLASS###'] = 'class="powermail_'.$this->formtitle.' powermail_'.$this->type.' powermail_'.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name').'_'.$i.'" '; // add class name to markerArray
				
				// ###CHECKED###
				if(isset($this->piVarsFromSession['uid'.$this->uid])) { // if there is a preselection in the session
					if($this->piVarsFromSession['uid'.$this->uid] == $optionlines[$i]) {
						$markerArray['###CHECKED###'] = 'checked="checked" '; // precheck radiobutton
					} else $markerArray['###CHECKED###'] = ''; // clear
				}
				
				$content_item .= $this->pibase->pibase->cObj->substituteMarkerArrayCached($this->tmpl['html_radio']['item'], $markerArray); // substitute Marker in Template (subpart 2)
 			}

		}
		$subpartArray = array(); // init
		$subpartArray['###CONTENT###'] = $content_item; // subpart 3
		$this->markerArray['###LABEL_MAIN###'] = $this->title;
		$this->markerArray['###POWERMAIL_FIELD_UID###'] = $this->uid;
		
		if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'mandatory') == 1) $this->markerArray['###MANDATORY_SYMBOL###'] = $this->pibase->pibase->cObj->wrap($this->conf['mandatory.']['symbol'],$this->conf['mandatory.']['wrap'],'|'); // add mandatory symbol if current field is a mandatory field

		$content = $this->pibase->pibase->cObj->substituteMarkerArrayCached($this->tmpl['html_radio']['all'], $this->markerArray, $subpartArray); // substitute Marker in Template
		$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_submit() returns HTML tag for submit button
	 *
	 * @return	[type]		...
	 */
	function html_submit() {
		$this->tmpl['html_submit'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_SUBMIT###'); // work on subpart

		$this->markerArray['###CLASS###'] = 'class="powermail_'.$this->formtitle.' powermail_'.$this->type.' powermail_submit" '; // add class name to markerArray

		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_submit'],$this->markerArray); // substitute Marker in Template
		$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_reset() returns HTML tag for reset button
	 *
	 * @return	[type]		...
	 */
	function html_reset() {
		$this->tmpl['html_reset'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_RESET###'); // work on subpart

		$this->markerArray['###CLASS###'] = 'class="powermail_'.$this->formtitle.' powermail_'.$this->type.' powermail_reset" '; // add class name to markerArray

		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_reset'],$this->markerArray); // substitute Marker in Template
		$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_label() returns HTML tag for some text
	 *
	 * @return	[type]		...
	 */
	function html_label() {
		$this->tmpl['html_label'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_LABEL###'); // work on subpart

		if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'send')) { // label should be send with email
			$this->markerArray['###HIDDEN###'] = '<input type="hidden" name="'.$this->prefixId.'['.$this->div_functions->clearName($this->title,1).']" value="'.$this->div_functions->clearValue($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value')).'" />'; // create hidden field
		}
		$this->markerArray['###CONTENT###'] = strip_tags($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value'),$this->conf['label.']['allowTags']); // fill label marker

		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_label'],$this->markerArray); // substitute Marker in Template
		$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_html() returns pure HTML
	 *
	 * @return	[type]		...
	 */
	function html_html() {
		$this->tmpl['html_html'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_HTML###'); // work on subpart

		if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'send')) { // label should be send with email
			$this->markerArray['###HIDDEN###'] = '<input type="hidden" name="'.$this->prefixId.'['.$this->div_functions->clearName($this->title,1).']" value="'.$this->div_functions->clearValue($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value')).'" />'; // create hidden field
		}
		$this->markerArray['###CONTENT###'] = $this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value'); // fill label marker

		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_html'],$this->markerArray); // substitute Marker in Template
		$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_content() returns page content
	 *
	 * @return	[type]		...
	 */
	function html_content() {
		$this->tmpl['html_content'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_CONTENT###'); // work on subpart

		$uid = str_replace('tt_content_','',$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value')); // get uid from flexform
		$conf = array('tables' => 'tt_content','source' => $uid,'dontCheckPid' => 1); // config

		$this->markerArray = array('###CONTENT###' => $this->pibase->pibase->cObj->RECORDS($conf)); // CONTENT Marker with content
		$this->markerArray['###POWERMAIL_FIELD_UID###'] = $this->uid; // UID to marker

		if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'send')) { // content should be send with email
			$this->markerArray['###HIDDEN###'] = '<input type="hidden" name="'.$this->prefixId.'[tt_content]['.$this->div_functions->clearName($this->title,1).']" value="'.$uid.'" />'; // create hidden field
		}

		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_content'],$this->markerArray); // substitute Marker in Template
		$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_password() returns password field like
	 *
	 * @return	[type]		...
	 */
	function html_password() {
		$this->tmpl['html_password'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_PASSWORD###'); // work on subpart

		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_password'],$this->markerArray); // substitute Marker in Template
		$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_file() returns file field
	 *
	 * @return	[type]		...
	 */
	function html_file() {
		$this->tmpl['html_file'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_FILE###'); // work on subpart

		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_file'],$this->markerArray); // substitute Marker in Template
		$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		return $content; // return HTML
	}
	

	/**
	 * Function html_hidden() returns hidden field
	 *
	 * @return	[type]		...
	 */
	function html_hidden() {
		$this->tmpl['html_hidden'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_HIDDEN###'); // work on subpart

		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_hidden'],$this->markerArray); // substitute Marker in Template
		$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_datetime() returns text field for date and time with calender help
	 *
	 * @return	[type]		...
	 */
	function html_datetime() {
		
		if(t3lib_extMgm::isLoaded('date2cal',0)) { // only if date2cal is loaded
			$this->tmpl['html_datetime'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_DATETIME###'); // work on subpart
	
			$tag = '<input type="text" ';
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name')) $tag .= 'name="'.$this->prefixId.'['.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name').']" '; // add name to field
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name')) $tag .= 'id="'.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name').'" '; // add name to field
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value')) $tag .= 'value="'.strftime($this->conf['format.']['datetime'], $this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value')).'" '; // add value to field
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'size')) $tag .= 'size="'.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'size').'" '; // add size to field
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'readonly')) $tag .= 'readonly="readonly" '; // add readonly to field
			$tag .= 'class="powermail_'.$this->formtitle.' powermail_datetime powermail_'.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name').'" '; // add class to field
			$tag .= '/>';
	
			$itemConfig = array (
				'itemName' => $this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name'),
				'item' => $tag,
				'wConf' => array (
					'evalValue' => 'datetime',
				),
				'table' => 'tt_content',
				'uid' => $this->uid,
				'field' => $this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name'),
			);
			$jscal = t3lib_div::makeInstance('tx_powermail_renderWizard'); // New object: jscalendar
	
			$this->markerArray['###FIELD###'] = $jscal->renderWizard($itemConfig,$this->pibase->pibase->cObj); // start tag generating
			$this->markerArray['###LABEL###'] = $this->title; // add label
			$this->markerArray['###LABEL_NAME###'] = $this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name'); // add name for label
			$this->markerArray['###POWERMAIL_FIELD_UID###'] = $this->uid; // UID to marker
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'mandatory') == 1) $this->markerArray['###MANDATORY_SYMBOL###'] = $this->pibase->pibase->cObj->wrap($this->conf['mandatory.']['symbol'],$this->conf['mandatory.']['wrap'],'|'); // add mandatory symbol if current field is a mandatory field
	
			$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_datetime'],$this->markerArray); // substitute Marker in Template
			$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		
		} else { // Extension date2cal is missing
			$content = 'Please install extension <strong>date2cal</strong> to use datetime feature';
		}
		
		return $content; // return HTML
	}
	

	/**
	 * Function html_date() returns text field for date with calender help
	 *
	 * @return	[type]		...
	 */
	function html_date() {
		
		if(t3lib_extMgm::isLoaded('date2cal',0)) { // only if date2cal is loaded
			$this->tmpl['html_date'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_DATE###'); // work on subpart
	
			$tag = '<input type="text" ';
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name')) $tag .= 'name="'.$this->prefixId.'['.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name').']" '; // add name to field
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name')) $tag .= 'id="'.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name').'" '; // add name to field
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value')) $tag .= 'value="'.strftime($this->conf['format.']['date'], $this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value')).'" '; // add value to field
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'size')) $tag .= 'size="'.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'size').'" '; // add size to field
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'readonly')) $tag .= 'readonly="readonly" '; // add readonly to field
			$tag .= 'class="powermail_'.$this->formtitle.' powermail_datetime powermail_'.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name').'" '; // add class to field
			$tag .= '/>';
	
			$itemConfig = array (
				'itemName' => $this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name'),
				'item' => $tag,
				'wConf' => array (
					'evalValue' => 'date',
				),
				'table' => 'tt_content',
				'uid' => $this->uid,
				'field' => $this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name'),
			);
			$jscal = t3lib_div::makeInstance('tx_powermail_renderWizard');
			$this->markerArray['###FIELD###'] = $jscal->renderWizard($itemConfig,$this->pibase->pibase->cObj); // start tag generating
			$this->markerArray['###LABEL###'] = $this->title; // add label
			$this->markerArray['###LABEL_NAME###'] = $this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name'); // add name for label
			$this->markerArray['###POWERMAIL_FIELD_UID###'] = $this->uid; // UID to marker
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'mandatory') == 1) $this->markerArray['###MANDATORY_SYMBOL###'] = $this->pibase->pibase->cObj->wrap($this->conf['mandatory.']['symbol'],$this->conf['mandatory.']['wrap'],'|'); // add mandatory symbol if current field is a mandatory field
	
			$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_date'],$this->markerArray); // substitute Marker in Template
			$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		
		} else { // Extension date2cal is missing
			$content = 'Please install extension <strong>date2cal</strong> to use date feature';
		}
		
		return $content; // return HTML
	}
	

	/**
	 * Function html_time() returns text field for time with calender help
	 *
	 * @return	[type]		...
	 */
	function html_time() {
		
		if(t3lib_extMgm::isLoaded('date2cal',0)) { // only if date2cal is loaded
			$this->tmpl['html_time'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_TIME###'); // work on subpart
	
			$tag = '<input type="text" ';
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name')) $tag .= 'name="'.$this->prefixId.'['.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name').']" '; // add name to field
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name')) $tag .= 'id="'.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name').'" '; // add name to field
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value')) $tag .= 'value="'.strftime($this->conf['format.']['time'], $this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value')).'" '; // add value to field
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'size')) $tag .= 'size="'.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'size').'" '; // add size to field
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'readonly')) $tag .= 'readonly="readonly" '; // add readonly to field
			$tag .= 'class="powermail_'.$this->formtitle.' powermail_datetime powermail_'.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name').'" '; // add class to field
			$tag .= '/>';
	
			$itemConfig = array (
				'itemName' => $this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name'),
				'item' => $tag,
				'wConf' => array (
					'evalValue' => 'time',
				),
				'table' => 'tt_content',
				'uid' => $this->uid,
				'field' => $this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name'),
			);
			$jscal = t3lib_div::makeInstance('tx_powermail_renderWizard');
			$this->markerArray['###FIELD###'] = $jscal->renderWizard($itemConfig,$this->pibase->pibase->cObj); // start tag generating
			$this->markerArray['###LABEL###'] = $this->title; // add label
			$this->markerArray['###LABEL_NAME###'] = $this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name'); // add name for label
			$this->markerArray['###POWERMAIL_FIELD_UID###'] = $this->uid; // UID to marker
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'mandatory') == 1) $this->markerArray['###MANDATORY_SYMBOL###'] = $this->pibase->pibase->cObj->wrap($this->conf['mandatory.']['symbol'],$this->conf['mandatory.']['wrap'],'|'); // add mandatory symbol if current field is a mandatory field
	
			$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_time'],$this->markerArray); // substitute Marker in Template
		
		} else { // Extension date2cal is missing
			$content = 'Please install extension <strong>date2cal</strong> to use time feature';
		}
		
		return $content; // return HTML
	}
	

	/**
	 * Function html_button() returns button field
	 *
	 * @return	[type]		...
	 */
	function html_button() {
		$this->tmpl['html_button'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_BUTTON###'); // work on subpart

		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_button'],$this->markerArray); // substitute Marker in Template
		$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		return $content; // return HTML
	}
	

	/**
	 * Function html_countryselect() returns select field with countries from static_info_tables
	 *
	 * @return	[type]		...
	 */
	function html_countryselect() {
		
		if(t3lib_extMgm::isLoaded('static_info_tables',0)) { // only if static_info_tables is loaded
			$this->tmpl['html_countryselect']['all'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_COUNTRYSELECT###'); // work on subpart 1
			$this->tmpl['html_countryselect']['item'] = tslib_cObj::getSubpart($this->tmpl['html_countryselect']['all'],'###ITEM###'); // work on subpart 2
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'without')) $withoutarray = t3lib_div::trimExplode(',',strtoupper($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'without')),1); // without values
			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'within')) $withinarray = t3lib_div::trimExplode(',',strtoupper($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'within')),1); // within values
			$content_item = ''; $valuearray = array(); $longvaluearray = array(); // init
			
			// Give me all needed fieldsets
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
				'cn_iso_2,cn_short_local,cn_short_en',
				'static_countries',
				$where_clause = '1=1',
				$groupBy = '',
				$orderBy = 'cn_short_en',
				$limit = ''
			);
			if ($res) { // If there is a result
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // One loop for every country
					$valuearray[] = $row['cn_iso_2']; // fill array with country shortage
					$longvaluearray[] = $row['cn_short_en']; // fill array with country names
				}
			}
			
			// Fill inner subpart
			for($i=0;$i<count($valuearray);$i++) { // one loop for every country
				$markerArray['###SELECTED###'] = ''; // clear selected marker
				if(isset($withinarray)) { // if within values are set
					if(in_array($valuearray[$i],$withinarray)) {	
						$markerArray['###VALUE###'] = $valuearray[$i];
						$markerArray['###LONGVALUE###'] = $longvaluearray[$i];
						if(strtoupper($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'preselect')) == $valuearray[$i]) $markerArray['###SELECTED###'] = 'selected="selected" '; // if current country should be preselected
						$content_item .= $this->pibase->pibase->cObj->substituteMarkerArrayCached($this->tmpl['html_countryselect']['item'], $markerArray);
					}
				} elseif(isset($withoutarray)) { // if without values are set
					if(!in_array($valuearray[$i],$withoutarray)) {	
						$markerArray['###VALUE###'] = $valuearray[$i];
						$markerArray['###LONGVALUE###'] = $longvaluearray[$i];
						if(strtoupper($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'preselect')) == $valuearray[$i]) $markerArray['###SELECTED###'] = 'selected="selected" '; // if current country should be preselected
						$content_item .= $this->pibase->pibase->cObj->substituteMarkerArrayCached($this->tmpl['html_countryselect']['item'], $markerArray);
					}
				} else { // show all
					$markerArray['###VALUE###'] = $valuearray[$i];
					$markerArray['###LONGVALUE###'] = $longvaluearray[$i];
					if(strtoupper($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'preselect')) == $valuearray[$i]) $markerArray['###SELECTED###'] = 'selected="selected" '; // if current country should be preselected
					$content_item .= $this->pibase->pibase->cObj->substituteMarkerArrayCached($this->tmpl['html_countryselect']['item'], $markerArray);
				}
			}
			
			$subpartArray['###CONTENT###'] = $content_item; // subpart 3
	
			$content = $this->pibase->pibase->cObj->substituteMarkerArrayCached($this->tmpl['html_countryselect']['all'],$this->markerArray,$subpartArray); // substitute Marker in Template
			$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		
		} else { // Extension static_info_tables is missing
			$content = 'Please install extension <strong>static_info_tables</strong> to use countryselect feature';
		}
		
		return $content; // return HTML
	}
	

	/**
	 * Function html_graphicsubmit() returns graphic as submitbutton
	 *
	 * @return	[type]		...
	 */
	function html_submitgraphic() {
		$this->tmpl['html_submitgraphic'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_SUBMITGRAPHIC###'); // work on subpart
		
		$this->markerArray['###SRC###'] = 'src="'.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'src').'" '; // source path for image
		if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'alt')) $this->markerArray['###ALT###'] = 'alt="'.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'alt').'" '; // if alt text exist, write alt text

		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_submitgraphic'],$this->markerArray); // substitute Marker in Template
		$content = preg_replace("|###.*###|i","",$content); // Finally clear not filled markers
		return $content; // return HTML
	}


	// Function setGlobalMarkers() to fill global markers with values
	function setGlobalMarkers() {

		// Configuration
		$this->div_functions = t3lib_div::makeInstance('tx_powermail_functions_div'); // New object: div functions

		// set global markers
		$this->markerArray = array(); // init
		
		if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name')) {
			// ###NAME###
			$this->markerArray['###NAME###'] = 'name="'.$this->prefixId.'[uid'.$this->uid.']" '; // add name to markerArray like tx_powermail_pi1[55]
			
			// ###LABEL_NAME###
			$this->markerArray['###LABEL_NAME###'] = $this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name'); // add label name to markerArray
			
			// ###ID###
			$this->markerArray['###ID###'] = 'id="'.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name').'"'; // add label name to markerArray
			
			// ###CLASS###
			$this->markerArray['###CLASS###'] = 'class="powermail_'.$this->formtitle.' powermail_'.$this->type.' powermail_'.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name').'" '; // add class name to markerArray
		}
		
		// ###SIZE###
		if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'size')) {
			$this->markerArray['###SIZE###'] = 'size="'.intval($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'size')).'" '; // add size to markerArray
		}
		
		// ###MAXLENGTH###
		if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'maxlength')) {
			$this->markerArray['###MAXLENGTH###'] = 'maxlength="'.intval($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'maxlength')).'" '; // add size to markerArray
		}
		
		// ###READONLY###
		if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'readonly')) {
			$this->markerArray['###READONLY###'] = 'readonly="readonly" '; // add readonly to markerArray
		}
		
		// ###VALUE###
		$this->markerArray['###VALUE###'] = 'value="'.strip_tags($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value')).'" '; // add value to markerArray (don't allow html/php tags)
		if($this->fe_field && $GLOBALS['TSFE']->fe_user->user[$this->fe_field]) $this->markerArray['###VALUE###'] = 'value="'.strip_tags($GLOBALS['TSFE']->fe_user->user[$this->fe_field]).'" '; // add value to markerArray if should filled from feuser data
		if(isset($this->piVarsFromSession['uid'.$this->uid])) $this->markerArray['###VALUE###'] = 'value="'.$this->piVarsFromSession['uid'.$this->uid].'" '; // Overwrite value from session value
		
		// ###LABEL###
		$this->markerArray['###LABEL###'] = $this->title; // add label to markerArray
		
		// ###MANDATORY_SYMBOL###
		if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'mandatory') == 1) $this->markerArray['###MANDATORY_SYMBOL###'] = $this->pibase->pibase->cObj->wrap($this->conf['mandatory.']['symbol'],$this->conf['mandatory.']['wrap'],'|');
		
		// ###POWERMAIL_FIELD_UID###
		$this->markerArray['###POWERMAIL_FIELD_UID###'] = $this->uid; // add uid to markerArray
		
		// ###COLS###
		if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'cols')) {
			$this->markerArray['###COLS###'] = 'cols="'.intval($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'cols')).'" '; // add number of columns to markerArray
		}
		
		// ###ROWS###
		if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'rows')) {
			$this->markerArray['###ROWS###'] = 'rows="'.intval($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'rows')).'" '; // add number of rows to markerArray
		}
		
		// ###WRAP###
		if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'wrap')) {
			$this->markerArray['###WRAP###'] = 'wrap="hard" '; // add wrap to markerArray
		}
		
		// ###ONCHANGE###
		if($this->conf['js.']['onchange']) {
			$this->markerArray['###ONCHANGE###'] = 'onchange="this.form.submit()"'; // onchange js for select fields
		}

		// ###ONFOCUS### Marker
		if($this->conf['js.']['init'] || $this->conf['js.']['onfocus']) { // only allowed if jsinit or onfocus set
			if($this->conf['js.']['init']) { // if jsinit allowed
				$init =  'init(\''.$this->prefixId.'['.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name').']'.'\',\''.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name').'\',\''.
					$this->pibase->pibase->pi_linkTP_keepPIvars_url (
						$overrulePIvars = array('basket' => '1'),
						$cache=0,
						$clearAnyway=0,
						$altPageId=0
					).'\',\'\');'; // add js init to string like: onblur="init(tx_powermail_pi1[textfeld],textfeld,index.php?id=index,'');"

				//$init =  'init(\''.$this->prefixId.'['.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name').']'.'\','.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'name').',\''.str_replace('../','',t3lib_extMgm::extRelPath($this->extKey)).'pi1/class.tx_powermail_pi1.session.inc.php\',\'\');'; // add js init to string like: onblur="init(tx_powermail_pi1[textfeld],textfeld,index.php?id=index,'');"
			} else $init = ''; // clean $init

			if($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value') && $this->conf['js.']['onfocus']) { // if value exists
				$js = 'onfocus="if(this.value==\''.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value').'\') this.value=\'\';" onblur="if(this.value==\'\') this.value=\''.$this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value').'\'; '.$init.'" '; // add onfocus js to markerArray
			} else { // if no value or focus not allowed
				if($init) $js = 'onblur="'.$init.'" ';
				else $js = '';
			}
			$this->markerArray['###ONFOCUS###'] = $js; // Fill markerArray with JS
		}
	}


	// Function GetSessionValue() to get any field value which is already in the session
	function GetSessionValue() {
		$sessions = t3lib_div::makeInstance('tx_powermail_sessions'); // New object: session functions
		$this->piVarsFromSession = $GLOBALS['TSFE']->fe_user->getKey("ses", $this->extKey.'_'.$this->pibase->pibase->cObj->data['uid']); // Load 
	}
	
	
	// function html_hook() to add a hook at the end of this file to manipulate markers and content
	function html_hook($content) {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FieldWrapMarkerHook'])) { // Adds hook for processing of extra global markers
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FieldWrapMarkerHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$this->markerArray = $_procObj->PM_FieldWrapMarkerHook($this->markerArray,$content,$this); // Get new marker Array from other extensions
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



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_html.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_html.php']);
}

?>
