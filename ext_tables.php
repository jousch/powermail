<?php
if (!defined ('TYPO3_MODE'))     die ('Access denied.');

$GLOBALS['TYPO3_DB']->debugOutput = true; // SQL Debug mode 

t3lib_extMgm::allowTableOnStandardPages('tx_powermail_fieldsets');

if (TYPO3_MODE=="BE")	include_once(t3lib_extMgm::extPath("powermail")."lib/class.tx_powermail_tx_powermail_forms_recip_table.php");
if (TYPO3_MODE=="BE")	include_once(t3lib_extMgm::extPath("powermail")."lib/class.tx_powermail_tx_powermail_forms_recip_id.php");
if (TYPO3_MODE=="BE")	include_once(t3lib_extMgm::extPath("powermail")."lib/class.tx_powermail_tx_powermail_forms_preview.php");
if (TYPO3_MODE=="BE")	include_once(t3lib_extMgm::extPath("powermail")."lib/class.tx_powermail_tx_powermail_forms_sender_field.php");
if (TYPO3_MODE=="BE")	include_once(t3lib_extMgm::extPath("powermail")."lib/class.tx_powermail_tx_powermail_fields_fe_field.php");
//if (TYPO3_MODE=="BE")	include_once(t3lib_extMgm::extPath("powermail")."lib/class.tx_powermail_xml_fieldreturn.php");

t3lib_extMgm::addToInsertRecords('tx_powermail_fieldsets');

$TCA["tx_powermail_fieldsets"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_fieldsets',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'default_sortby' => "ORDER BY crdate",	
		'delete' => 'deleted',	
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_powermail_fieldsets.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "fe_group, form, title, felder",
	)
);


t3lib_extMgm::allowTableOnStandardPages('tx_powermail_fields');


t3lib_extMgm::addToInsertRecords('tx_powermail_fields');

$TCA["tx_powermail_fields"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields',		
		"requestUpdate" => "formtype",
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'delete' => 'deleted',	
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_powermail_fields.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "fieldset, title, name, flexform, value, size, maxsize, mandantory, more, fe_field",
	)
);


t3lib_extMgm::allowTableOnStandardPages('tx_powermail_mails');


t3lib_extMgm::addToInsertRecords('tx_powermail_mails');

$TCA["tx_powermail_mails"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails',		
		'label'     => 'sender',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY crdate DESC",	
		'delete' => 'deleted',	
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_powermail_mails.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "formid, recipient, subject_r, sender, content, piVars, senderIP, UserAgent, Referer, SP_TZ",
	)
);

t3lib_extMgm::addStaticFile($_EXTKEY,'static/powermail/', 'powermail');

t3lib_div::loadTCA("tt_content");

t3lib_extMgm::addPlugin(array('LLL:EXT:powermail/locallang_db.xml:tt_content.CType_pi1', $_EXTKEY.'_pi1'),'CType');

$tempColumns = Array (
    "tx_powermail_title" => Array (        
        "exclude" => 1,        
        "label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.title",        
        "config" => Array (
            "type" => "input",    
            "size" => "30",  
			"max" => "30",  
            "eval" => "required,trim,lower,alphanum_x,nospace",
        )
    ),
    "tx_powermail_recipient" => Array (        
        "exclude" => 1,        
        "label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recipient",        
        "config" => Array (
            "type" => "text",
            "cols" => "60",    
            "rows" => "2",
        )
    ),
	"tx_powermail_subject_r" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.subject_r",		
		"config" => Array (
			"type" => "input",	
			"size" => "30",
		)
	),
	"tx_powermail_subject_s" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.subject_s",		
		"config" => Array (
			"type" => "input",	
			"size" => "30",
		)
	),
	"tx_powermail_sender" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.sender",		
		"config" => Array (
			"type" => "select",	
			"items" => Array (
				Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_table.I.0", "0"),
			),
			"itemsProcFunc" => "tx_powermail_tx_powermail_forms_sender_field->main",	
			"size" => 1,	
			"maxitems" => 1,
		)
	),
	"tx_powermail_confirm" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.confirm",		
		"config" => Array (
			"type" => "check",
			"default" => 1,
		)
	),
	"tx_powermail_multiple" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.multiple",		
		"config" => Array (
			'type'  => 'select',
			'items' => array (
				Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.multiple.I.0", "0"),
				Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.multiple.I.1", "1"),
				Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.multiple.I.2", "2"),
			),
		)
	),
	"tx_powermail_recip_table" => Array (		
		"exclude" => 1,
		"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_table",		
		"config" => Array (
			"type" => "select",
			"items" => Array (
				Array("", "0"),
			),
			"itemsProcFunc" => "tx_powermail_tx_powermail_forms_recip_table->main",	
			"size" => 1,	
			"maxitems" => 1,
		)
	),
	"tx_powermail_recip_id" => Array (		
		"exclude" => 1,		
		"displayCond" => 'FIELD:tx_powermail_recip_table:REQ:true',
		"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_id",		
		"config" => Array (
			"type" => "select",
			"items" => Array (
			),
			"itemsProcFunc" => "tx_powermail_tx_powermail_forms_recip_id->main",	
			"size" => 5,	
			"maxitems" => 100,
			"allowNonIdValues" => 1,
		)
	),
	"tx_powermail_query" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.query",		
		"config" => Array (
			"type" => "text",
			"cols" => "60",    
			"rows" => "2",
		)
	),
	"tx_powermail_thanks" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.thanks",		
		"config" => Array (
			"type" => "text",
			"cols" => "60",
			"rows" => "2",
		)
	),
	"tx_powermail_fieldsets" => Array(
		"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldsets",		
		"config" => Array (
			"type" => "inline",
			"foreign_table" => "tx_powermail_fieldsets",
			"foreign_field" => "tt_content",
			'foreign_sortby' => 'sorting',
			'foreign_label' => 'title',
			'maxitems' => 1000,
			'appearance' => Array(
				'collapseAll' => 1,
				'expandSingle' => 1,
				'useSortable' => 1,
				'newRecordLinkAddTitle' => 1,
				'newRecordLinkPosition' => 'both',
			),
		)
	),
	"tx_powermail_users" => Array (		
		"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.users",
		"config" => Array (
			"type" => "passthrough"
		)
	),
	"tx_powermail_preview" => Array (		
		"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.preview",
		"config" => Array (
			"type" => "user",
			'userFunc' => 'tx_powermail_tx_powermail_forms_preview->main',
		)
	),
);


t3lib_div::loadTCA("tt_content");
t3lib_extMgm::addTCAcolumns("tt_content",$tempColumns,1);
$TCA['tt_content']['types'][$_EXTKEY.'_pi1']['showitem']='
	CType;;4;button;1-1-1, sys_language_uid;;;;2-2-2, l18n_parent, l18n_diffsource, hidden;;1, header;;3;;3-3-3,
	--div--;LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.div1, tx_powermail_title;;;;2-2-2, tx_powermail_confirm;;;;3-3-3, tx_powermail_multiple,
	--div--;LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.div2, tx_powermail_fieldsets;;;;4-4-4, tx_powermail_preview,
	--div--;LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.div3, tx_powermail_subject_r, tx_powermail_recipient, tx_powermail_users;;;;5-5-5,tx_powermail_recip_table, tx_powermail_recip_id, tx_powermail_query;;;;6-6-6,
	--div--;LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.div4, tx_powermail_sender, tx_powermail_subject_s, 
	--div--;LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.div5, tx_powermail_thanks;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts],
	--div--;LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.div6';



$TCA['tt_content']['ctrl']['requestUpdate'] = "tx_powermail_recip_table";
$TCA['tt_content']['ctrl']['dividers2tabs'] = $confArr['noTabDividers']?FALSE:TRUE;




t3lib_extMgm::addLLrefForTCAdescr('tt_content','EXT:powermail/lang/locallang_csh_tt_content.php');

if (TYPO3_MODE=="BE") {	
	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_powermail_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_powermail_pi1_wizicon.php';
	t3lib_extMgm::addModule('web','txpowermailM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
	
}
?>
