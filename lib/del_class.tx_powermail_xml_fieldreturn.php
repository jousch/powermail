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

/**
 * Class/Function which returns a xml for field setting
 *
 * @author	Mischa Heißmann, Alexander Kellner <typo3@heissmann.org, alexander.kellner@einpraegsam.net>
 * @package	TYPO3
 * @subpackage	tx_powermail
 */
class tx_powermail_xml_fieldreturn {

	var $extKey = 'powermail'; // Extension key
	var $path_xml = '../typo3conf/ext/powermail/lib/field_definitions.xml'; // relative path to xml file
	var $path_xml_temp = 'lib/temp_field_definitions.xml'; // relative path to xml file
	var $writefile = 1; // disable for testing only
	
	// Function main() is for backendconfig and creates a xml file
	function main(&$params,&$pObj)	{
		
		// Create items for select-field
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.text'), 'text');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.textarea'), 'textarea');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.select'), 'select');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.check'), 'check');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.radio'), 'radio');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.submit'), 'submit');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.reset'), 'reset');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.label'), 'label');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.content'), 'content');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.html'), 'html');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.password'), 'password');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.file'), 'file');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.hidden'), 'hidden');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.datetime'), 'datetime');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.date'), 'date');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.time'), 'time');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.button'), 'button');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.submitgraphic'), 'submitgraphic');
		$params['items'][] = array($pObj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.countryselect'), 'countryselect');

		/*
		if (file_exists($this->path_xml)) { // only if xml file exists
			if(phpversion() >= '5.0') { // if php5 is enabled
				$xml_object = simplexml_load_file($this->path_xml); // Generate object from xml string
				if(isset($xml_object->$params['row']['type'])) { // If there is a fitting xml string (like text or textarea etc..)
					if($this->writefile) { // if allowed to write (see above)
						t3lib_div::writeFile ( // generate temporary xml file for TYPO3 backend config
							t3lib_extMgm::extPath($this->extKey).$this->path_xml_temp, // path
							$xml_object->$params['row']['type']->children()->asXML() // xml
						);
					}
				} else { // no fitting xml tag
					if($this->writefile) { // if allowed to write (see above)
						t3lib_div::writeFile ( // generate empty xml file
							t3lib_extMgm::extPath($this->extKey).$this->path_xml_temp, // path
							$xml_object->emptyxml->children()->asXML() // xml
						);
					}
				}
			} else { // don't use php5 functions
				$xmlstring = ''; $cur = 0;
				$xmlfile = fopen($this->path_xml, "r"); // open file
				while (!feof($xmlfile)) { // read every line
					$xmlstring .= fgets($xmlfile, 1024); // get line
				}
				$xmlarray = t3lib_div::xml2array($xmlstring); // change xml to an array
				if(is_array($xmlarray)) { // only if array
					foreach ($xmlarray as $key => $value) { // every key in the xmlarray
						if($key == $params['row']['type']) {
							if($this->writefile) { // if allowed to write (see above)
								t3lib_div::writeFile ( // generate xml file
									t3lib_extMgm::extPath($this->extKey).$this->path_xml_temp, // path
									trim(str_replace(array('<>','</>'),'',t3lib_div::array2xml($xmlarray[$key],'',0,''))) // array2xml and replace empty tags and trim
								);
							}
							$cur = 1; // There is a fitting xml tag
						}
					}
					if(!$cur) { // no fitting xml tag
						if($this->writefile) { // if allowed to write (see above)
							t3lib_div::writeFile ( // generate xml file
								t3lib_extMgm::extPath($this->extKey).$this->path_xml_temp, // path
								trim(str_replace(array('<>','</>'),'',t3lib_div::array2xml($xmlarray['emptyxml'],'',0,''))) // array2xml and replace empty tags and trim
							);
						}
					}
				}
			}
			
		} else echo 'error - no XML-file found on this server.'; // If xml file don't exist
		*/
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_xml_fieldreturn.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_xml_fieldreturn.php']);
}

?>
