<?php
require_once('../lib/class.tx_powermail_functions_div.php'); // include div functions

class tx_powermail_belist {

	var $timeformat = 'Y-m-d H:i'; // timeformat for displaying date

	// Function Main
	function main($pid,$BACK_PATH = '',$mailID = 0) {
		
		// config
		$this->pid = $pid;
		$this->backpath = $BACK_PATH;
		$this->mailID = $mailID;
		$this->content = '';
		$this->divfunctions = t3lib_div::makeInstance('tx_powermail_functions_div'); // make instance with dif functions
		$i = 0;
		
		// DB query
		if($this->mailID > 0) $where_add = ' AND uid = '.$this->mailID; else $where_add = '';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'*',
			'tx_powermail_mails',
			$where_clause = 'pid = '.$this->pid.' AND hidden = 0 AND deleted = 0'.$where_add,
			$groupBy = '',
			$orderBy = 'crdate DESC',
			$limit = ''
		);
		if ($res) { // If on current page is a result
			$this->content .= '<table style="background-color: #7d838c;" border="1" cellpadding="0" cellspacing="0">';
			$this->content .= '
				<tr>
					<td><b style="color: white; padding: 0 5px;">#</b></td>
					<td><b style="color: white; padding: 0 5px;">Date</b></td>
					<td><b style="color: white; padding: 0 5px;">Sender</b></td>
					<td><b style="color: white; padding: 0 5px;">Sender IP</b></td>
					<td><b style="color: white; padding: 0 5px;">Receiver</b></td>
					<td><b style="color: white; padding: 0 5px;">Details</b></td>
				</tr>'
			; // write table head
			
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // one loop for every db entry
				$i++; // increase
				
				$this->content .= '<tr>';
				$this->content .= '<td style="color: white; padding: 0 5px;">'.$i.'</td>'; // #
				$this->content .= '<td style="color: white; padding: 0 5px;">'.date($this->timeformat, $row['crdate']).'</td>'; // date
				$this->content .= '<td style="color: white; padding: 0 5px;">'.$this->divfunctions->linker($row['sender'],' style="color: white; text-decoration: underline;"').'</td>'; // sender email
				$this->content .= '<td style="color: white; padding: 0 5px;">'.$row['senderIP'].'</td>'; // sender IP
				$this->content .= '<td style="color: white; padding: 0 5px;">'.$this->divfunctions->linker($row['recipient'],' style="color: white; text-decoration: underline;"').'</td>'; // receiver email
				$this->content .= '<td style="color: white;" align="center"><a href="tx_powermail_bedetails.php?id='.$pid.'&mailID='.$row['uid'].'" onclick="vHWin=window.open(\'tx_powermail_bedetails.php?id='.$pid.'&mailID='.$row['uid'].'\',\'FEopenLink\',\'width=500,height=600\');vHWin.focus();return false;"><img src="'.$this->backpath.'sysext/t3skin/icons/gfx/zoom.gif" title="Open mail details" /></a></td>';
				
				$this->content .= '</tr>';
			}
			$this->content .= '</table>';
		}
		
		if(!res) { // if on current page is no result
			$this->content = '<strong>No powermails on current page!</strong>';
		}
		
		return $this->content; // return
	}

}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_belist.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_belist.php']);
}
?>