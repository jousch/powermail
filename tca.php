<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');
$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']); // Get backandconfig

$TCA["tx_powermail_fieldsets"] = array (
	"ctrl" => $TCA["tx_powermail_fieldsets"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,fe_group,form,title"
	),
	"feInterface" => $TCA["tx_powermail_fieldsets"]["feInterface"],
	"columns" => array (
		"tt_content" => array (		
			"config" => array (
				"type" => "passthrough"
			)
		),
		"formtable" => array (		
			"config" => array (
				"type" => "passthrough"
			)
		),
		"title" => array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fieldsets.title",		
			"config" => array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"felder" => array(
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fieldsets.fields",		
			"config" => array (
				"type" => "inline",
				"foreign_table" => "tx_powermail_fields",
				"foreign_field" => "fieldset",
				"maxitems" => 1000,
				'appearance' => array(
					'collapseAll' => 1,
					'expandSingle' => 1,
					'useSortable' => 1,
					'newRecordLinkAddTitle' => 1,
					'newRecordLinkPosition' => 'both',
				),
			)
		)
	),
	"types" => array (
		"0" => array("showitem" => "form, title;;;;2-2-2, felder")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);

// Make powermail available in older TYPO3 version (fieldsets)
if($GLOBALS['TYPO_VERSION'] < '4.0' || $confArr['useIRRE'] == 0) {
	$TCA["tx_powermail_fieldsets"]["columns"]["tt_content"]['config'] = array (
		"type" => "select",
		"foreign_table" => "tt_content",
		'foreign_table_where' => 'AND tt_content.pid=###CURRENT_PID### ',
		"maxitems" => 1,
	);
	$TCA["tx_powermail_fieldsets"]["columns"]["tt_content"]['label'] = "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fieldsets.tt_content";
	$TCA["tx_powermail_fieldsets"]["columns"]["felder"]['config']['type'] = 'passthrough';
	$TCA["tx_powermail_fieldsets"]["types"]["0"]['showitem'] = "form, title;;;;2-2-2, tt_content, felder";
}



$TCA["tx_powermail_fields"] = array (
	"ctrl" => $TCA["tx_powermail_fields"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,starttime,endtime,fe_group,fieldset,title,name,type,value,size,maxsize,mandantory,more,fe_field"
	),
	"feInterface" => $TCA["tx_powermail_fields"]["feInterface"],
	"columns" => array (
		"fieldset" => array (		
			"config" => array (
				"type" => "passthrough"
			)
		),
		"title" => array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.title",		
			"config" => array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"formtype" => array (
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.type",		
			"config" => array (
				"type" => "select",
				"items" => array (
					array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.type.I.0", "0",),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.text', 'text'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.textarea', 'textarea'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.select', 'select'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.check', 'check'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.radio', 'radio'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.submit', 'submit'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.reset', 'reset'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.label', 'label'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.content', 'content'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.html', 'html'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.password', 'password'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.file', 'file'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.hidden', 'hidden'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.datetime', 'datetime'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.date', 'date'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.time', 'time'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.button', 'button'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.submitgraphic', 'submitgraphic'),
					array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldtitle.countryselect', 'countryselect'),
				),
				//"itemsProcFunc" => "tx_powermail_xml_fieldreturn->main",	
				"size" => 1,	
				"maxitems" => 1,
			)
		),
		"flexform" => array (
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.field",		
			"config" => array (
				"type" => "flex",
				"ds_pointerField" => "formtype",
				'ds' => array(
					"default" => 'FILE:EXT:powermail/lib/def/def_field_error.xml',
					"button" => 'FILE:EXT:powermail/lib/def/def_field_button.xml',
					"check" => 'FILE:EXT:powermail/lib/def/def_field_check.xml',
					"content" => 'FILE:EXT:powermail/lib/def/def_field_content.xml',
					"countryselect" => 'FILE:EXT:powermail/lib/def/def_field_countryselect.xml',
					"date" => 'FILE:EXT:powermail/lib/def/def_field_date.xml',
					"datetime" => 'FILE:EXT:powermail/lib/def/def_field_datetime.xml',
					"file" => 'FILE:EXT:powermail/lib/def/def_field_file.xml',
					"hidden" => 'FILE:EXT:powermail/lib/def/def_field_hidden.xml',
					"html" => 'FILE:EXT:powermail/lib/def/def_field_html.xml',
					"label" => 'FILE:EXT:powermail/lib/def/def_field_label.xml',
					"password" => 'FILE:EXT:powermail/lib/def/def_field_password.xml',
					"radio" => 'FILE:EXT:powermail/lib/def/def_field_radio.xml',
					"reset" => 'FILE:EXT:powermail/lib/def/def_field_reset.xml',
					"select" => 'FILE:EXT:powermail/lib/def/def_field_select.xml',
					"submit" => 'FILE:EXT:powermail/lib/def/def_field_submit.xml',
					"submitgraphic" => 'FILE:EXT:powermail/lib/def/def_field_submitgraphic.xml',
					"text" => 'FILE:EXT:powermail/lib/def/def_field_text.xml',
					"textarea" => 'FILE:EXT:powermail/lib/def/def_field_textarea.xml',
					"time" => 'FILE:EXT:powermail/lib/def/def_field_time.xml',
				),
			)
		),
		"fe_field" => array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.fe_field",		
			"config" => array (
				"type" => "select",
				"items" => array (
					array("LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.fe_field.I.0", "0"),
				),
				"itemsProcFunc" => "tx_powermail_tx_powermail_fields_fe_field->main",	
				"size" => 1,	
				"maxitems" => 1,
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "title;;;;1-1-1,formtype;;;;2-2-2,flexform;;;;3-3-3, fe_field;;;;4-4-4")
	),
	"palettes" => array (
		"1" => array("showitem" => ""),
	)
);

// Make powermail available in older TYPO3 version (fields)
if($GLOBALS['TYPO_VERSION'] < '4.0' || $confArr['useIRRE'] == 0) {
	$TCA["tx_powermail_fields"]["columns"]["fieldset"]['config'] = array (
		"type" => "select",
		"foreign_table" => "tx_powermail_fieldsets",
		"maxitems" => 1,
	);
	$TCA["tx_powermail_fields"]["columns"]["fieldset"]['label'] = "LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields.fieldset";
	$TCA["tx_powermail_fields"]["types"]["0"]['showitem'] = "title;;;;1-1-1,fieldset,formtype;;;;2-2-2,flexform;;;;3-3-3, fe_field;;;;4-4-4";
}

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
		"formid" => array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails.formid",		
			"config" => array (
				"type" => "group",
				"internal_type" => 'db',
				"allowed" => 'tt_content',
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
				"show_thumbs" => 1
			)
		),
		"recipient" => array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails.recipient",		
			"config" => array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"subject_r" => array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails.subject_r",		
			"config" => array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"sender" => array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails.sender",		
			"config" => array (
				"type" => "input",	
				"size" => "30",
			)
		), 
		"content" => array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails.content",        
            "config" => array (
                "type" => "text",
                "cols" => "30",
                "rows" => "5",
                "wizards" => array (
                    "_PADDING" => 2,
                    "RTE" => array(
                        "notNewRecords" => 1,
                        "RTEonly" => 1,
                        "type" => "script",
                        "title" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails.content_RTE",
                        "icon" => "wizard_rte2.gif",
                        "script" => "wizard_rte.php",
                    ),
                ),
            )
        ),
		"piVars" => array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails.piVars",		
			"config" => array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		"senderIP" => array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails.senderIP",		
			"config" => array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"UserAgent" => array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails.UserAgent",		
			"config" => array (
				"type" => "input",	
				"size" => "40",
			)
		),
		"Referer" => array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails.Referer",		
			"config" => array (
				"type" => "input",	
				"size" => "40",
			)
		),
		"SP_TZ" => array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails.SP_TZ",		
			"config" => array (
				"type" => "input",	
				"size" => "30",
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "hidden;;1;;1-1-1, formid, recipient, subject_r, sender, content;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_powermail/rte/], piVars, senderIP, UserAgent, Referer, SP_TZ")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);
?>
