<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Mischa Hei?mann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@wunschtacho.de>
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
require_once(str_replace('../','',t3lib_extMgm::extRelPath('powermail')).'lib/class.tx_powermail_functions_div.php'); // file for div functions

class tx_powermail_markers extends tslib_pibase {

    var $extKey = 'powermail';
    var $scriptRelPath = 'pi1/class.tx_powermail_pi1.php';    // Path to pi1 to get locallang.xml from pi1 folder
    var $locallangmarker_prefix = 'locallangmarker_'; // prefix for automatic locallangmarker
    
    // Function GetMarkerArray() to set global Markers for Emails and THX message
    function GetMarkerArray() {
        
        // Configuration
        $this->markerArray = array(); $this->markerArray['###POWERMAIL_ALL###'] = ''; // init
        $this->sessiondata = $GLOBALS['TSFE']->fe_user->getKey('ses',$this->extKey.'_'.$this->pibase->pibase->cObj->data['uid']); // Get piVars from session
       	$this->div_functions = t3lib_div::makeInstance('tx_powermail_functions_div'); // New object: div functions
        $this->notInMarkerAll = t3lib_div::trimExplode(',',$this->conf['markerALL.']['notIn'],1); // choose which fields should not be listed in marker ###ALL### (ERROR is never allowed to be shown)
        $this->tmpl['all']['all'] = $this->pibase->pibase->cObj->getSubpart(tslib_cObj::fileResource($this->conf['template.']['all']),"###POWERMAIL_ALL###"); // Load HTML Template: ALL (works on subpart ###POWERMAIL_ALL###)
		$this->tmpl['all']['item'] = $this->pibase->pibase->cObj->getSubpart($this->tmpl['all']['all'],"###ITEM###"); // Load HTML Template: ALL (works on subpart ###POWERMAIL_ALL###)
		$content_item = ''; $markerArray = array();
        
        if(isset($this->sessiondata)) {
            foreach($this->sessiondata as $k => $v) { // One loop for every piVar
                if(is_numeric(str_replace('uid','',$k))) { // use only piVars like UID555
					if(!is_array($v)) { // standard: value is not an array
						if(is_numeric(str_replace('uid','',$k))) { // check if key is like uid55
							$this->markerArray['###'.strtoupper($k).'###'] = $this->div_functions->nl2br2(stripslashes($v)); // fill ###UID55###
							$this->markerArray['###'.strtolower($k).'###'] = $this->div_functions->nl2br2(stripslashes($v)); // fill ###uid55###
							
							// ###POWERMAIL_ALL###
							if(!in_array(strtoupper($k),$this->notInMarkerAll) && !in_array('###'.strtoupper($k).'###',$this->notInMarkerAll)) {
								$markerArray['###POWERMAIL_LABEL###'] = $this->GetLabelfromBackend($k,$v);
								$markerArray['###POWERMAIL_VALUE###'] = $this->div_functions->nl2br2(stripslashes($v));
								$content_item .= $this->pibase->pibase->cObj->substituteMarkerArrayCached($this->tmpl['all']['item'],$markerArray);
							}
						}
					} else { // value is still an array (needed for e.g. checkboxes tx_powermail_pi1[uid55][0])
						$i=0; // init counter
						foreach($v as $kv => $vv) { // One loop for every piVar
							if(is_numeric(str_replace('uid','',$k))) { // check if key is like uid55
								$this->markerArray['###'.strtoupper($k).'_'.$kv.'###'] = $this->div_functions->nl2br2(stripslashes($vv)); // fill ###UID55_0###
								$this->markerArray['###'.strtolower($k).'_'.$kv.'###'] = $this->div_functions->nl2br2(stripslashes($vv)); // fill ###uid55_0###
								$this->markerArray['###'.strtoupper($k).'###'] .= ($i!=0?', ':'').$this->div_functions->nl2br2(stripslashes($vv)); // fill ###UID55### (comma between every value)
								
								// ###POWERMAIL_ALL###
								if(!in_array(strtoupper($k),$this->notInMarkerAll) && !in_array('###'.strtoupper($k).'###',$this->notInMarkerAll)) {
									$markerArray['###POWERMAIL_LABEL###'] = $this->GetLabelfromBackend($k,$v);
									$markerArray['###POWERMAIL_VALUE###'] = $this->div_functions->nl2br2(stripslashes($vv));
									$content_item .= $this->pibase->pibase->cObj->substituteMarkerArrayCached($this->tmpl['all']['item'],$markerArray);
								}
								$i++; // increase counter
							}
						}
					}
				}
            }
			$subpartArray['###CONTENT###'] = $content_item; // ###POWERMAIL_ALL###
        }
        
        // add standard Markers
		$this->markerArray['###POWERMAIL_UPLOADFOLDER###'] = $this->conf['upload.']['folder']; // Relative upload folder from constants
		$this->markerArray['###POWERMAIL_BASEURL###'] = ($GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'] ? $GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'] : t3lib_div::getIndpEnv('TYPO3_SITE_URL')); // absolute path (baseurl)
		$this->markerArray['###POWERMAIL_ALL###'] = $this->pibase->pibase->cObj->substituteMarkerArrayCached($this->tmpl['all']['all'], array(), $subpartArray); // Fill ###POWERMAIL_ALL###
        $this->markerArray['###POWERMAIL_THX_RTE###'] = $this->pibase->pibase->pi_RTEcssText(tslib_cObj::substituteMarkerArrayCached($this->pibase->pibase->cObj->data['tx_powermail_thanks'],$this->markerArray)); // Thx message with ###fields###
        $this->markerArray['###POWERMAIL_EMAILRECIPIENT_RTE###'] = $this->pibase->pibase->pi_RTEcssText(tslib_cObj::substituteMarkerArrayCached($this->pibase->pibase->cObj->data['tx_powermail_mailreceiver'],$this->markerArray)); // Email to receiver message with ###fields###
        $this->markerArray['###POWERMAIL_EMAILSENDER_RTE###'] = $this->pibase->pibase->pi_RTEcssText(tslib_cObj::substituteMarkerArrayCached($this->pibase->pibase->cObj->data['tx_powermail_mailsender'],$this->markerArray)); // Email to sender message with ###fields###
		
		if(isset($this->markerArray)) return $this->markerArray;
    }
    
    
    // Function GetLabelfromBackend() to get label to current field for emails and thx message
    function GetLabelfromBackend($name,$value) {
		if(strpos($name,'uid') !== FALSE) { // $name like uid55
			$uid = str_replace('uid','',$name);

			// additional where clause for tt_content table
			$where_clause = 'c.deleted=0 AND c.t3ver_state!=1 AND c.hidden=0 AND (c.starttime<='.time().') AND (c.endtime=0 OR c.endtime>'.time().') AND (c.fe_group="" OR c.fe_group IS NULL OR c.fe_group="0" OR (c.fe_group LIKE "%,0,%" OR c.fe_group LIKE "0,%" OR c.fe_group LIKE "%,0" OR c.fe_group="0") OR (c.fe_group LIKE "%,-1,%" OR c.fe_group LIKE "-1,%" OR c.fe_group LIKE "%,-1" OR c.fe_group="-1"))'; // enable fields for tt_content
			if($GLOBALS['TYPO_VERSION'] < '4.0') $where_clause = 'c.deleted=0 AND c.hidden=0 AND (c.starttime<='.time().') AND (c.endtime=0 OR c.endtime>'.time().') AND (c.fe_group="" OR c.fe_group IS NULL OR c.fe_group="0" OR (c.fe_group LIKE "%,0,%" OR c.fe_group LIKE "0,%" OR c.fe_group LIKE "%,0" OR c.fe_group="0") OR (c.fe_group LIKE "%,-1,%" OR c.fe_group LIKE "-1,%" OR c.fe_group LIKE "%,-1" OR c.fe_group="-1"))'; // enable fields for tt_content

			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery ( // GET title where fields.flexform LIKE <value index="vDEF">vorname</value>
				'f.title',
				'tx_powermail_fields f LEFT JOIN tx_powermail_fieldsets fs ON (f.fieldset = fs.uid) LEFT JOIN tt_content c ON (c.uid = fs.tt_content)',
				$where_clause .= ' AND c.uid = '.$this->pibase->pibase->cObj->data['uid'].' AND f.uid = '.$uid.' AND f.hidden = 0 AND f.deleted = 0',
				$groupBy = '',
				$orderBy = '',
				$limit = ''
			);
			if ($res) $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			
			if(isset($row['title'])) return $row['title']; // if title was found return ist
			else return 'POWERMAIL ERROR: No title to current field found in DB ('.$name.')'; // if no title was found return  
		} else { // no uid55 so return $name
			return $name;
		}
    }
    
    
    // Function DynamicLocalLangMarker() to get automaticly a marker from locallang.xml (###LOCALLANG_BLABLA### from locallang.xml: locallangmarker_blabla
    function DynamicLocalLangMarker($array) {
        $string = $this->pi_getLL(strtolower($this->locallangmarker_prefix.$array[1]));
        if(isset($string)) return $string;
    }


    //function for initialisation.
    // to call cObj, make $this->pibase->pibase->cObj->function()
    function init(&$conf,&$pibase) {
        $this->conf = $conf;
        $this->pibase = $pibase;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_markers.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_markers.php']);
}

?>