<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Mischa Heiﬂmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@wunschtacho.de>
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


	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH.'init.php');
require_once($BACK_PATH.'template.php');
require_once('class.tx_powermail_belist.php'); // include Backend list function
require_once('../lib/class.tx_powermail_functions_div.php'); // include div functions

$LANG->includeLLFile('EXT:powermail/mod1/locallang.xml');
require_once(PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]



/**
 * Module 'Powermail' for the 'powermail' extension.
 *
 * @author	Mischa Heiﬂmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@wunschtacho.de>
 * @package	TYPO3
 * @subpackage	tx_powermail
 */
class tx_powermail_bedetails extends t3lib_SCbase {
	var $pageinfo;
	
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
	
		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;
	
		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{
	
				// Draw the header.
			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="POST">';
	
				// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = 0;
				</script>
			';
	
			$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);
	
			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
			$this->content.=$this->doc->divider(5);
	
	
			// Render content:
			$this->moduleContent();
	
	
			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
			}
	
			$this->content.=$this->doc->spacer(10);
		} else {
			// If no access or if ID == zero
	
			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;
	
			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}
	
	// Final output
	function printContent()	{
		$this->content.=$this->doc->endPage();
		echo $this->content;
	}
	
	// What to show
	function moduleContent()	{
		global $BACK_PATH;
		
		switch((string)$this->MOD_SETTINGS['function'])	{
			case 1:
			default:
				$this->belist = t3lib_div::makeInstance('tx_powermail_belist');
				$this->content .= $this->belist->main($this->id,$BACK_PATH,$_GET['mailID']);
				$this->getDetails();
			break;
		}
	}
	
	// Get details of an email
	function getDetails() {
		$this->divfunctions = t3lib_div::makeInstance('tx_powermail_functions_div'); // make instance with dif functions
		$this->content .= '<br /><br />';
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'piVars',
			'tx_powermail_mails',
			$where_clause = 'hidden = 0 AND deleted = 0 AND uid = '.$_GET['mailID'],
			$groupBy = '',
			$orderBy = '',
			$limit = ''
		);
		if ($res) $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if (isset($row)) { // if is set
			$values = t3lib_div::xml2array($row['piVars'],'pivars'); // xml2array
			
			$this->content .= '<table>';
			foreach ($values as $key => $value) { // one loop for every piVar
				$this->content .= '<tr>';
				$this->content .= '<td style="padding: 0 5px;"><strong>'.$key.':</strong></td>'; // write table cell
				$this->content .= '<td style="padding: 0 5px;">'.$this->divfunctions->linker($value,' style="text-decoration: underline;"').'</td>'; // write table cell
				$this->content .= '</tr>';
			}
			$this->content .= '</table>';
		}
	}
	
	
	// make variables global available
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		parent::init();
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/tx_powermail_bedetails.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/tx_powermail_bedetails.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_powermail_bedetails');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE) include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>