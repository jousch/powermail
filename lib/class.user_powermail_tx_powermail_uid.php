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


/**
 * Class/Function which manipulates the item-array for table/field tx_powermail_forms_recip_field.
 *
 * @author	Mischa Heißmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@einpraegsam.net>
 * @package	TYPO3
 * @subpackage	tx_powermail
 */
class user_powermail_tx_powermail_uid {
	function main($PA, $fobj)	{

		//$PA['fieldConf']['config']['field']; // example content: text or textarea
//print_r($PA);
/*		$content = '<script language="JavaScript" type="text/javascript">
						<!--
							var nn = !!document.layers;
							var ie = !!document.all;

							if (nn){
								netscape.security.PrivilegeManager.enablePrivilege("UniversalSystemClipboardAccess");
								var fr=new java.awt.Frame(); // der IE kanns so, aber für den NN muss man dessen Java-API bemühen
								var zwischenablage = fr.getToolkit().getSystemClipboard();
							}

							function copy(textfeld){
								if (nn) {
									textfeld.select();
									zwischenablage.setContents(new java.awt.datatransfer.StringSelection(textfeld.value), null);
								}
								else if (ie) {
									textfeld.select();
									cbBuffer=textfeld.createTextRange();
									cbBuffer.execCommand(\'Copy\');
								}
							}
						//-->
					</script>';*/
					
		$content .= '<input type="text" readonly="readonly" name="uid'.$PA['row']['uid'].'" value="';
		$content .= '###UID'.$PA['row']['uid'].'###';
		$content .= '" />';
	//	$content .= '<input type="button" value="Kopieren" name="cmdCopy" onClick="copy(this.form.uid'.$PA['row']['uid'].')">';
		return $content;

	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.user_powermail_tx_powermail_uid.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.user_powermail_tx_powermail_uid.php']);
}

?>
