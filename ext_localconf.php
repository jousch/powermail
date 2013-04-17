<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_powermail_fieldsets=1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_powermail_fields=1');
t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_powermail_pi1.php','_pi1','CType',0);

$TYPO3_CONF_VARS['EXTCONF']['cms']['db_layout']['addTables']['tx_powermail_fieldsets'][0] = array(
	'fList' => 'uid,title',
	'icon' => TRUE,
);
$TYPO3_CONF_VARS['EXTCONF']['cms']['db_layout']['addTables']['tx_powermail_fields'][0] = array(
	'fList' => 'uid,title,name,type,fieldset',
	'icon' => TRUE,
);



// Set realurlconf for type = 3131 (needed to get a dynamic JavaScript for formcheck)
if(t3lib_extMgm::isLoaded('realurl',0)) { // only if realurl is loaded
	
	// $TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']
	if(isset($TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT'])) { // only if array is already set in localconf.php
		$i=0; $set=0; // init counter and flag
		if(isset($TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars']) && is_array($TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars'])) { // if preVars already set in realurl conf
			foreach ($TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars'] as $key => $value) { // one loop for every preVar
				if($TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars'][$i]['GETvar'] == 'type') { // if current preVar == type
					$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars'][$i]['valueMap']['validation'] = '3131'; // add validation => 3131
					$set = 1; // validation alreade added - so flag = 1
				}
				$i++; // increase loop counter
			}
		}
		if($set==0) { // if flag == 0 (valdiation => 3131 not set) add complete type array
			$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars'][] = array ( // add complete type array
				'GETvar' => 'type',
				'valueMap' => array (
					'validation' => '3131'
				),
				'noMatch' => 'bypass'
			);
		}
	} else { // set preVars for realurl
		$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars'][] = array ( // add complete type array
        	'GETvar' => 'type',
        	'valueMap' => array (
            	'validation' => '3131'
        	),
        	'noMatch' => 'bypass'
        );
	}
	
	
	// $TYPO3_CONF_VARS['EXTCONF']['realurl']['www.currentURL.com']
	if(isset($TYPO3_CONF_VARS['EXTCONF']['realurl'][$_SERVER['HTTP_HOST']])) { // only if array is already set in localconf.php
		$i=0; $set=0; // init counter and flag
		if(isset($TYPO3_CONF_VARS['EXTCONF']['realurl'][$_SERVER['HTTP_HOST']]['preVars']) && is_array($TYPO3_CONF_VARS['EXTCONF']['realurl'][$_SERVER['HTTP_HOST']]['preVars'])) { // if preVars already set in realurl conf
			foreach ($TYPO3_CONF_VARS['EXTCONF']['realurl'][$_SERVER['HTTP_HOST']]['preVars'] as $key => $value) { // one loop for every preVar
				if($TYPO3_CONF_VARS['EXTCONF']['realurl'][$_SERVER['HTTP_HOST']]['preVars'][$i]['GETvar'] == 'type') { // if current preVar == type
					$TYPO3_CONF_VARS['EXTCONF']['realurl'][$_SERVER['HTTP_HOST']]['preVars'][$i]['valueMap']['validation'] = '3131'; // add validation => 3131
					$set = 1; // validation alreade added - so flag = 1
				}
				$i++; // increase loop counter
			}
		}
		if($set==0) { // if flag == 0 (valdiation => 3131 not set) add complete type array
			$TYPO3_CONF_VARS['EXTCONF']['realurl'][$_SERVER['HTTP_HOST']]['preVars'][] = array ( // add complete type array
				'GETvar' => 'type',
				'valueMap' => array (
					'validation' => '3131'
				),
				'noMatch' => 'bypass'
			);
		}
	}
	
}

?>
