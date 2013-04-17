<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$LOCAL_LANG = Array(
	'default' => Array(
		'tx_powermail_title.description' => 'Insert a title for your form. Won\'t be displayed in Frontend.',
		'tx_powermail_html.description' => 'Should the emails be sent in HTML-Format?',
		'tx_powermail_multiple.description' => 'Make a multiple mailform.',
		'tx_powermail_multiple.details' => 'You can choose between these values:
		<b>Single Form</b>
		All Fields are displayed in one form on one page. No pagebreaks, no multiple steps.
		<b>Multiple JS</b>
		The form is divided into several steps. Each fieldset will create one step. The submitting is done by JavaScript. This provides a better performance.
		<b>Multiple PHP</b>
		The form is divided into several steps. Each fieldset will create one step. The submitting is done by PHP-script. This is better for accessibility.',
		'tx_powermail_fieldsets.description' => 'Create a new set of fields',
		'tx_powermail_fieldsets.details' => 'Formfields are kept together in individual fieldsets. This is for two reasons: 
		1. accessibility for disabled persons 
		2. for technical reasons. With fieldsets powermail is able to create a multiple form.',
		'tx_powermail_subject_r.description' => 'Email-subject of the recipient\'s mail',
		'tx_powermail_recipient.description' => 'Recipients of this mailform. To send to multiple recipients, separate with semicolon.',
		'tx_powermail_recip_table.description' => 'Choose a table to send this mailform to stores addresses.',
		'tx_powermail_recip_table.details' => 'With this field it is possible to send a massmail to a group of stores data. (e.g. fe_users). Just select a table.
		A new field will appear with further options to select.',
		'tx_powermail_recip_id.description' => 'Select either groups or different addresses as recipients.',
		'tx_powermail_recip_id.details' => 'If the table selected in previous field contains the string "group", you will find only groups inside this selectbox.
		Otherwise only individual email-Addresses are displayed.',
		'tx_powermail_query.description' => 'For admins: create an SQL-query to get very individual list of recipients.',
		'tx_powermail_query.details' => 'You can put any SQL-query inside here. Only limitation: the result of the query has to be a list of email-addresses.',
		'tx_powermail_sender.description' => 'Select a field in your form containing the sender\'s email-address. The answer-mail will be sent to this address.',
		'tx_powermail_subject_s.description' => 'Email-subject of the sender\'s mail',
		'tx_powermail_thanks.description' => 'Text that is displayed after submitting the form. For using markers see DETAILS',
		'tx_powermail_thanks.details' => 'You are able to set markers inside this text. Markers are set very easily - just use the field\'s name as wrapped with ###
		<b>Example</b>
		You have a field named "phone". So type the marker ###phone### into this field and the submitted content will be placed here.',
	),
	'de' => Array(
		'tx_powermail_title.description' => 'Geben Sie einen Titel für Ihr Formular ein. Dieser wird im Frontend nicht angezeigt.',
		'tx_powermail_html.description' => 'Sollen die E-Mails im HTML-Format gesendet werden?',
		'tx_powermail_multiple.description' => 'Formular in mehreren Schritten erstellen.',
		'tx_powermail_multiple.details' => 'Ist diese Option angewählt, wird das Formular in mehrere Schritte aufgeteilt. Jede Formularseite erstellt dann eine einzelne Seite.',
		'tx_powermail_fieldsets.description' => 'Create a new set of fields',
		'tx_powermail_fieldsets.details' => 'Formfields are kept together in individual fieldsets. This is for two reasons: 
		1. accessibility for disabled persons 
		2. for technical reasons. With fieldsets powermail is able to create a multiple form.',
		'tx_powermail_subject_r.description' => 'Email-subject of the recipient\'s mail',
		'tx_powermail_recipient.description' => 'Recipients of this mailform. To send to multiple recipients, separate with semicolon.',
		'tx_powermail_recip_table.description' => 'Choose a table to send this mailform to stores addresses.',
		'tx_powermail_recip_table.details' => 'With this field it is possible to send a massmail to a group of stores data. (e.g. fe_users). Just select a table.
		A new field will appear with further options to select.',
		'tx_powermail_recip_id.description' => 'Select either groups or different addresses as recipients.',
		'tx_powermail_recip_id.details' => 'If the table selected in previous field contains the string "group", you will find only groups inside this selectbox.
		Otherwise only individual email-Addresses are displayed.',
		'tx_powermail_query.description' => 'For admins: create an SQL-query to get very individual list of recipients.',
		'tx_powermail_query.details' => 'You can put any SQL-query inside here. Only limitation: the result of the query has to be a list of email-addresses.',
		'tx_powermail_sender.description' => 'Select a field in your form containing the sender\'s email-address. The answer-mail will be sent to this address.',
		'tx_powermail_subject_s.description' => 'Email-subject of the sender\'s mail',
		'tx_powermail_thanks.description' => 'Text that is displayed after submitting the form. For using markers see DETAILS',
		'tx_powermail_thanks.details' => 'You are able to set markers inside this text. Markers are set very easily - just use the field\'s name as wrapped with ###
		<b>Example</b>
		You have a field named "phone". So type the marker ###phone### into this field and the submitted content will be placed here.',
	),
);
?>
