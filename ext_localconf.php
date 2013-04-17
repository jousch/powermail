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
?>
