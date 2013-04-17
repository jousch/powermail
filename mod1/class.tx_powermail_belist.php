<?php
require_once('../lib/class.tx_powermail_functions_div.php'); // include div functions

class tx_powermail_belist {

	var $timeformat = 'Y-m-d H:i'; // timeformat for displaying date
	var $timeformat_start = '2008-01-01 00:00'; // timeformat for startdate
	
	// Function Main
	function main($pid,$BACK_PATH = '',$mailID = 0) {
		
		// config
		$this->pid = $pid;
		$this->backpath = $BACK_PATH;
		$this->mailID = $mailID;
		$this->content = '';
		$this->divfunctions = t3lib_div::makeInstance('tx_powermail_functions_div'); // make instance with dif functions
		if(isset($_POST['startdate'])) $this->startdate = $_POST['startdate'];
		else $this->startdate = $this->timeformat_start;
		if(isset($_POST['enddate'])) $this->enddate = $_POST['enddate'];
		else $this->enddate = date($this->timeformat,time());
		$i = 0;
		
		// DB query
		if($this->mailID > 0) $where_add = ' AND uid = '.$this->mailID; else $where_add = '';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'*',
			'tx_powermail_mails',
			$where_clause = 'pid = '.$this->pid.' AND crdate > '.strtotime($this->startdate).' AND crdate < '.strtotime($this->enddate).' AND hidden = 0 AND deleted = 0'.$where_add,
			$groupBy = '',
			$orderBy = 'crdate DESC',
			$limit = ''
		);
		if ($res) { // If on current page is a result
			if(!isset($_GET['mailID'])) { // If mailID was set per GET params
				$this->content .= $this->inputFields(); // Show input fields for date filter
				$this->content .= $this->exportIcons(); // Show export images
			}
			$this->content .= '<div style="width: 470px; overflow: auto;">';
			$this->content .= '<table style="background-color: #7d838c;" border="1" cellpadding="0" cellspacing="0">';
			$this->content .= '
				<tr>
					<td><b style="color: white; padding: 0 5px;">'.$this->LANG->getLL('title_no').'</b></td>
					<td><b style="color: white; padding: 0 5px;">'.$this->LANG->getLL('title_date').'</b></td>
					<td><b style="color: white; padding: 0 5px;">'.$this->LANG->getLL('title_sender').'</b></td>
					<td><b style="color: white; padding: 0 5px;">'.$this->LANG->getLL('title_IP').'</b></td>
					<td><b style="color: white; padding: 0 5px;">'.$this->LANG->getLL('title_receiver').'</b></td>
					<td><b style="color: white; padding: 0 5px;">'.$this->LANG->getLL('title_details').'</b></td>
				</tr>'."\n"
			; // write table head
			
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // one loop for every db entry
				$i++; // increase
				
				$this->content .= '<tr>';
				$this->content .= '<td style="color: white; padding: 0 5px;">'.$i.'.</td>'; // #
				$this->content .= '<td style="color: white; padding: 0 5px;">'.date($this->timeformat, $row['crdate']).'</td>'; // date
				$this->content .= '<td style="color: white; padding: 0 5px;">'.$this->divfunctions->linker($row['sender'],' style="color: white; text-decoration: underline;"').'</td>'; // sender email
				$this->content .= '<td style="color: white; padding: 0 5px;">'.$row['senderIP'].'</td>'; // sender IP
				$this->content .= '<td style="color: white; padding: 0 5px;">'.$this->divfunctions->linker($row['recipient'],' style="color: white; text-decoration: underline;"').'</td>'; // receiver email
				$this->content .= '<td style="color: white;" align="center"><a href="index.php?id='.$pid.'&mailID='.$row['uid'].'" onclick="vHWin=window.open(\'index.php?id='.$pid.'&mailID='.$row['uid'].'\',\'FEopenLink\',\'width=500,height=600,scrollbars=yes,resize=yes\');vHWin.focus();return false;"><img src="'.$this->backpath.'sysext/t3skin/icons/gfx/zoom.gif" title="Open mail details" /></a></td>';
				
				$this->content .= '</tr>'."\n";
			}
			$this->content .= '</table></div>'."\n";	
		}
		
		if(!$i) { // if on current page is no result
			$this->content = '<strong>'.$this->LANG->getLL('nopowermails1').'</strong><br />';
			$this->content .= $this->LANG->getLL('nopowermails2').'<br />';
		}
		
		return $this->content; // return
	}
	
	// Show input fields for filtering
	function inputFields() {
		$content = '<div style="float: left;">'."\n";
		$content .= '<input type="text" name="startdate" value="'.$this->startdate.'" /><br />'."\n";
		$content .= '<input type="text" name="enddate" value="'.$this->enddate.'" />'."\n";
		if(isset($_GET['id'])) $content .= '<input type="hidden" name="id" value="'.$_GET['id'].'" />'."\n";
		$content .= '<input type="submit" value="Filter" />'."\n";
		$content .= '</div>'."\n";
		
		return $content;
	}
	
	// Show links for export methods
	function exportIcons() {
		$content = '<div style="float: right;">';
		$content .= '<a href="index.php?id='.$this->pid.'&export=xls&startdate='.urlencode($this->startdate).'&enddate='.urlencode($this->enddate).'"><img src="../img/icon_xls.gif" style="margin: 5px;" title="Export to excel file format" /></a>';
		$content .= '<a href="index.php?id='.$this->pid.'&export=csv&startdate='.urlencode($this->startdate).'&enddate='.urlencode($this->enddate).'"><img src="../img/icon_csv.gif" style="margin: 5px;" title="Export to CSV file format" /></a>';
		$content .= '<a href="index.php?id='.$this->pid.'&export=table&startdate='.urlencode($this->startdate).'&enddate='.urlencode($this->enddate).'" target="_blank"><img src="../img/icon_table.gif" style="margin: 5px;" title="Export to HTML table" /></a>';
		$content .= '</div>';
		$content .= '<div style="clear: both;"></div>';
		$content .= '<br />';
		
		return $content;
	}
	
	// Init
	function init($LANG) {
		$this->LANG = $LANG; // make $LANG global
	}

}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_belist.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_belist.php']);
}
?>