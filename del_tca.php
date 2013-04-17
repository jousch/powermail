<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_powermail_forms"] = array (
	"ctrl" => $TCA["tx_powermail_forms"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,fe_group,title,recipient,subject_r,subject_s,sender,html,multiple,recip_table,recip_id,recip_field,query,thanks"
	),
	"feInterface" => $TCA["tx_powermail_forms"]["feInterface"],
	"columns" => array (
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l18n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_powermail_forms',
				'foreign_table_where' => 'AND tx_powermail_forms.pid=###CURRENT_PID### AND tx_powermail_forms.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(0, 0, 0, 12, 31, 2020),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'fe_group' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		"title" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"recipient" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recipient",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		"subject_r" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.subject_r",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"subject_s" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.subject_s",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"sender" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.sender",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_powermail_fields",	
				"foreign_table_where" => "AND tx_powermail_fields.pid=###CURRENT_PID### ORDER BY tx_powermail_fields.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"html" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.html",		
			"config" => Array (
				"type" => "check",
				"default" => 1,
			)
		),
		"multiple" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.multiple",		
			"config" => Array (
				"type" => "check",
			)
		),
		"recip_table" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_table",		
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_table.I.0", "0"),
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_table.I.1", "1"),
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_table.I.2", "2"),
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_table.I.3", "3"),
				),
				"itemsProcFunc" => "tx_powermail_tx_powermail_forms_recip_table->main",	
				"size" => 1,	
				"maxitems" => 1,
			)
		),
		"recip_id" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_id",		
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_id.I.0", "0"),
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_id.I.1", "1"),
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_id.I.2", "2"),
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_id.I.3", "3"),
				),
				"itemsProcFunc" => "tx_powermail_tx_powermail_forms_recip_id->main",	
				"size" => 1,	
				"maxitems" => 1,
			)
		),
		"recip_field" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_field",		
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_field.I.0", "0"),
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_field.I.1", "1"),
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_field.I.2", "2"),
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_field.I.3", "3"),
				),
				"itemsProcFunc" => "tx_powermail_tx_powermail_forms_recip_field->main",	
				"size" => 1,	
				"maxitems" => 1,
			)
		),
		"query" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.query",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		"thanks" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.thanks",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
			)
		),
		"fieldsets" => Array(
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldsets",		
			"config" => Array (
				"type" => "inline",
				"foreign_table" => "tx_powermail_fieldsets",
				"foreign_field" => "form",
				'foreign_sortby' => 'sorting',
				'foreign_label' => 'title',
				'appearance' => Array(
					'collapseAll' => 1,
					'expandSingle' => 1,
					'useSortable' => 1,
				),
			)
		)
	),
	"types" => array (
		"0" => array("showitem" => "--div--;Form,sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, title;;;;2-2-2, recipient;;;;3-3-3, subject_r, subject_s, sender, html, multiple,--div--;Recipients, recip_table, recip_id, recip_field, query, thanks;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts],--div--;Fields, fieldsets")
	),
	"palettes" => array (
		"1" => array("showitem" => "starttime, endtime, fe_group")
	)
);



$TCA["tx_powermail_fieldsets"] = array (
	"ctrl" => $TCA["tx_powermail_fieldsets"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,fe_group,form,title"
	),
	"feInterface" => $TCA["tx_powermail_fieldsets"]["feInterface"],
	"columns" => array (
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l18n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_powermail_fieldsets',
				'foreign_table_where' => 'AND tx_powermail_fieldsets.pid=###CURRENT_PID### AND tx_powermail_fieldsets.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(0, 0, 0, 12, 31, 2020),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'fe_group' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		"form" => Array (		
			"exclude" => 1,		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_powermail_forms",	
				"foreign_table_where" => "AND tx_powermail_forms.pid=###CURRENT_PID### ORDER BY tx_powermail_forms.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
	/*	"form" => Array (		
			"exclude" => 1,		
			"config" => Array (
				"type" => "input",	
			)
		),*/
		"title" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fieldsets.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"fields" => Array(
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fieldsets.fields",		
			"config" => Array (
				"type" => "inline",
				"foreign_table" => "tx_powermail_fields",
				"foreign_field" => "fieldset",
				'appearance' => Array(
					'collapseAll' => 1,
					'expandSingle' => 1,
					'useSortable' => 1,
				),
			)
		)
	),
	"types" => array (
		"0" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, form, title;;;;2-2-2, fields")
	),
	"palettes" => array (
		"1" => array("showitem" => "starttime, endtime, fe_group")
	)
);



$TCA["tx_powermail_fields"] = array (
	"ctrl" => $TCA["tx_powermail_fields"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,starttime,endtime,fe_group,fieldset,title,name,type,value,size,maxsize,mandantory,more,fe_field"
	),
	"feInterface" => $TCA["tx_powermail_fields"]["feInterface"],
	"columns" => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(0, 0, 0, 12, 31, 2020),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'fe_group' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
	/*	"fieldset" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.fieldset",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_powermail_fieldsets",	
				"foreign_table_where" => "AND tx_powermail_fieldsets.pid=###CURRENT_PID### ORDER BY tx_powermail_fieldsets.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),*/
		"fieldset" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.fieldset",		
			"config" => Array (
				"type" => "input",	
			)
		),
		"title" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"name" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.name",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"type" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.type",		
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.type.I.0", "0"),
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.type.I.1", "1"),
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.type.I.2", "2"),
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.type.I.3", "3"),
				),
				"itemsProcFunc" => "tx_powermail_tx_powermail_fields_type->main",	
				"size" => 1,	
				"maxitems" => 1,
			)
		),
		"value" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.value",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		"size" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.size",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"range" => Array ("lower"=>0,"upper"=>1000),	
				"eval" => "int,nospace",
			)
		),
		"maxsize" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.maxsize",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"range" => Array ("lower"=>0,"upper"=>1000),	
				"eval" => "int,nospace",
			)
		),
		"mandantory" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.mandantory",		
			"config" => Array (
				"type" => "check",
			)
		),
		"more" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.more",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"fe_field" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.fe_field",		
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.fe_field.I.0", "0"),
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.fe_field.I.1", "1"),
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.fe_field.I.2", "2"),
					Array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.fe_field.I.3", "3"),
				),
				"itemsProcFunc" => "tx_powermail_tx_powermail_fields_fe_field->main",	
				"size" => 1,	
				"maxitems" => 1,
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "hidden;;1;;1-1-1, fieldset, title;;;;2-2-2, name;;;;3-3-3, type, value, size, maxsize, mandantory, more, fe_field")
	),
	"palettes" => array (
		"1" => array("showitem" => "starttime, endtime, fe_group")
	)
);



$TCA["tx_powermail_mails"] = array (
	"ctrl" => $TCA["tx_powermail_mails"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,formid,recipient,subject_r,sender,content"
	),
	"feInterface" => $TCA["tx_powermail_mails"]["feInterface"],
	"columns" => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		"formid" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails.formid",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_powermail_forms",	
				"foreign_table_where" => "AND tx_powermail_forms.pid=###CURRENT_PID### ORDER BY tx_powermail_forms.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"recipient" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails.recipient",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"subject_r" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails.subject_r",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"sender" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails.sender",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"content" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails.content",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "hidden;;1;;1-1-1, formid, recipient, subject_r, sender, content")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);
?>
