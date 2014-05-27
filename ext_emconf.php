<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "powermail".
 *
 * Auto generated 09-01-2013 13:05
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'powermail',
	'description' => 'Powerful and easy to use mailform extension with many features like storing form datas in database, export them to HTML, CSV and EXCEL (last one needs extension phpexcel_library), javascript validation (jQuery), multi step forms, different HTML templates, IRRE (Inline Relationship Record Editing), static_info_tables and many more...',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '1.6.11',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'excludeFromUpdates',
	'uploadfolder' => 0,
	'createDirs' => 'uploads/tx_powermail/files',
	'modify_tables' => 'tt_content',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Powermail development team',
	'author_email' => 'alexander.kellner@in2code.de',
	'author_company' => '',
	'CGLcompliance' => NULL,
	'CGLcompliance_note' => NULL,
	'constraints' => 
	array (
		'depends' => 
		array (
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.4.6-0.0.0',
		),
		'conflicts' => 
		array (
		),
		'suggests' => 
		array (
		),
	),
);

?>
