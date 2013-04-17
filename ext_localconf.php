<?php
if (!defined ('TYPO3_MODE'))     die ('Access denied.');
t3lib_extMgm::addUserTSConfig('
    options.saveDocNew.tx_powermail_fieldsets=1
');
t3lib_extMgm::addUserTSConfig('
    options.saveDocNew.tx_powermail_fields=1
');

t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_powermail_pi1.php','_pi1','CType',1);

$TYPO3_CONF_VARS['EXTCONF']['cms']['db_layout']['addTables']['tx_powermail_fieldsets'][0] = array(
	'fList' => 'uid,title',
	'icon' => TRUE,
);
$TYPO3_CONF_VARS['EXTCONF']['cms']['db_layout']['addTables']['tx_powermail_fields'][0] = array(
	'fList' => 'uid,title,name,type,fieldset',
	'icon' => TRUE,
);

// Set realurlconf for type = 3131
if(t3lib_extMgm::isLoaded('realurl',0)) { // only if realurl is loaded
	
	if(isset($TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT'])) { // only if array is already set
		$i=0;
		foreach ($TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars'] as $key => $value) {
			if($TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars'][$i]['GETvar'] == 'type') {
				$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars'][$i]['valueMap']['validation'] = '3131';
			}
			$i++;
		}
	}
	
	else { // set preVars for realurl
		$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars'][] = array(
        	'GETvar' => 'type',
        	'valueMap' => array (
            	'validation' => '3131'
        	),
        	'noMatch' => 'bypass'
        );
	}
	
}

?>
