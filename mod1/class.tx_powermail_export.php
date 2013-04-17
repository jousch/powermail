<?php
require_once('../lib/class.tx_powermail_functions_div.php'); // include div functions

class tx_powermail_export {

	var $extKey = 'powermail'; // Extension key
	var $dateformat = 'Y-m-d'; // timeformat for displaying date
	var $timeformat = 'H:i:s'; // timeformat for displaying date
	var $seperator = ';'; // separator for csv
	var $csvfilename = 'powermail_export.csv'; // filename of exported CSV file
	var $zip = 1; // activate CSV file compressing to .gz

	// Function Main
	function main($export,$pid = 0,$LANG = '') {
		// config
		$this->pid = $pid; // Page id
		$this->startdate = $_GET['startdate'];
		$this->enddate = $_GET['enddate'];
		$this->LANG = $LANG; // make $LANG global
		$content = ''; // init content variable
		$i = 0;
	
		// DB query
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'*',
			'tx_powermail_mails',
			$where_clause = 'pid = '.$this->pid.' AND hidden = 0 AND deleted = 0 AND crdate > '.strtotime($this->startdate).' AND crdate < '.strtotime($this->enddate),
			$groupBy = '',
			$orderBy = 'crdate DESC',
			$limit = ''
		);
		if ($res) { // If on current page is a result
			if($export == 'xls' || $export == 'table') {
				$table = '<table>'; // Init table
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // one loop for every db entry
					if($row['piVars']) {
						$i++; // increase counter
						$table .= '<tr>';
						$table .= '<td>'.$i.'.</td>';
						$table .= '<td>'.date($this->dateformat, $row['crdate']).'</td>';
						$table .= '<td>'.date($this->timeformat, $row['crdate']).'</td>';
						$table .= '<td>'.$row['sender'].'</td>';
						$table .= '<td>'.$row['senderIP'].'</td>';
						$table .= '<td>'.$row['recipient'].'</td>';
						$table .= '<td>'.$this->decode($row['subject_r']).'</td>';
						$values = t3lib_div::xml2array($row['piVars'],'pivars'); // xml2array
						if(isset($values) && is_array($values)) {
							foreach ($values as $key => $value) { // one loop for every piVar
								if(!is_array($value)) $table .= '<td>'.$this->decode($value).'</td>';
							}
						}
						$table .= '<td>'.$row['formid'].'</td>';
						$table .= '<td>'.$this->decode(trim(strip_tags($row['content'],'<a><b>'))).'</td>';
						$table .= '<td>'.$row['UserAgent'].'</td>';
						$table .= '<td>'.$row['Referer'].'</td>';
						$table .= '<td>'.$row['SP_TZ'].'</td>';
						$table .= '</tr>';
					}
				}
				$table .= '</table>';
			} elseif ($export == 'csv') {
				//$table .= 'sep=,'."\n"; // write first line
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // one loop for every db entry
					if($row['piVars']) {
						$i++; // increase counter
						$values = t3lib_div::xml2array($row['piVars'],'pivars'); // xml2array
						$table .= '"'.$i.'."'.$this->seperator;
						$table .= '"'.date($this->dateformat, $row['crdate']).'"'.$this->seperator;
						$table .= '"'.date($this->timeformat, $row['crdate']).'"'.$this->seperator;
						$table .= '"'.$row['sender'].'"'.$this->seperator;
						$table .= '"'.$row['senderIP'].'"'.$this->seperator;
						$table .= '"'.$row['recipient'].'"'.$this->seperator;
						$table .= '"'.$this->decode($row['subject_r']).'"'.$this->seperator;
						if(isset($values) && is_array($values)) {
							foreach ($values as $key => $value) { // one loop for every piVar
								if(!is_array($value)) $table .= '"'.str_replace('"',"'",str_replace(array("\n\r","\r\n","\n","\r"),'',$this->decode($value))).'"'.$this->seperator;
							}
						}
						$table .= '"'.$row['formid'].'"'.$this->seperator;
						$table .= '"'.str_replace(array("\n\r","\r\n","\n","\r"),'',$this->decode(strip_tags($row['content'],'<a><b>'))).'"'.$this->seperator;
						$table .= '"'.$row['UserAgent'].'"'.$this->seperator;
						$table .= '"'.$row['Referer'].'"'.$this->seperator;
						$table .= '"'.$row['SP_TZ'].'"'.$this->seperator;
						$table = substr($table,0,-1); // delete last ,
						$table .= "\n"; // new line
					}
				}
			}
		}
		
		// What to show
		if($export == 'xls') {
		
			$content .= header("Content-type: application/vnd-ms-excel");
			$content .= header("Content-Disposition: attachment; filename=export.xls");
			$content .= $table; // add table to content
		
		} elseif($export == 'csv') {
		
			if(!t3lib_div::writeFileToTypo3tempDir(PATH_site.'typo3temp/'.$this->csvfilename,$table)) { // write to typo3temp and if success returns FALSE
				$content .= '<strong>'.$this->LANG->getLL('export_download_success').'</strong><br />';
				$this->gzcompressfile(PATH_site.'typo3temp/'.$this->csvfilename); // compress file
				$content .= '<a href="http://'.$_SERVER['HTTP_HOST'].'/typo3temp/'.$this->csvfilename.'" target="_blank"><u>'.$this->LANG->getLL('export_download_download').'</u></a><br />'; // link to xx.csv.gz
				$content .= '<a href="http://'.$_SERVER['HTTP_HOST'].'/typo3temp/'.$this->csvfilename.'.gz" target="_blank"><u>'.$this->LANG->getLL('export_download_downloadZIP').'</u></a><br />'; // link to xx.csv
			} else {
				$content .= t3lib_div::writeFileToTypo3tempDir(PATH_site.'typo3temp/'.$this->csvfilename,$table); // Error message
			}
		
		} elseif($export == 'table') {
		
			$content .= $table; // add table to content
		
		} else { // not supported method
			$content = 'Wrong export method chosen!';
		}
		
		return $content;
	}
	
	// Function decode() decodes string if utf-8 is in use
	function decode($string) {
		if($this->LANG->charSet == 'utf-8') $string = utf8_decode($string);
		
		return $string;
	}

	// Compress a file
	function gzcompressfile($source,$level=false){ 
		$dest = $source.'.gz';
		$mode = 'wb'.$level;
		$error = false;
		if($fp_out=gzopen($dest,$mode)){
			if($fp_in=fopen($source,'rb')){
				while(!feof($fp_in))
				gzwrite($fp_out,fread($fp_in,1024*512));
				fclose($fp_in);
			}
			else $error=true;
			gzclose($fp_out);
		}
		else $error=true;
		
		if($error) return false;
		else return $dest;
	}

}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_export.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_export.php']);
}
?>