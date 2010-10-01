<?php

########################################################################
# Extension Manager/Repository config file for ext "elemente_fenews".
#
# Auto generated 02-12-2009 18:46
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Frontend News',
	'description' => 'Frontend news submitter with RTE support (htmlArea RTE) and upload capabilities. Supports CAPTCHA (captcha, sr_freecap) for none access protected sites and DAM references (dam_ttnews) for image and file upload.',
	'category' => 'plugin',
	'author' => 'Andre Steiling',
	'author_email' => 'steiling@elemente.ms',
	'author_company' => 'elemente websolutions',
	'shy' => '',
	'dependencies' => 'taskcenter,tt_news',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '1.0.5',
	'constraints' => array(
		'depends' => array(
			'taskcenter' => '',
			'tt_news' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:24:{s:9:"ChangeLog";s:4:"f40c";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"17b7";s:17:"ext_localconf.php";s:4:"1d88";s:14:"ext_tables.php";s:4:"a698";s:14:"ext_tables.sql";s:4:"1b6d";s:15:"flexform_ds.xml";s:4:"cfec";s:13:"locallang.xml";s:4:"f3fa";s:16:"locallang_db.xml";s:4:"a1cf";s:17:"locallang_tca.xml";s:4:"a490";s:19:"doc/wizard_form.dat";s:4:"7e0b";s:20:"doc/wizard_form.html";s:4:"6bb0";s:40:"hooks/class.tx_elementefenews_ttnews.php";s:4:"3435";s:45:"modfunc1/class.tx_elementefenews_modfunc1.php";s:4:"e21c";s:22:"modfunc1/locallang.xml";s:4:"1b11";s:14:"pi1/ce_wiz.gif";s:4:"2d8a";s:35:"pi1/class.tx_elementefenews_pi1.php";s:4:"ae82";s:43:"pi1/class.tx_elementefenews_pi1_wizicon.php";s:4:"65a3";s:17:"pi1/locallang.xml";s:4:"7db1";s:15:"res/ico_del.gif";s:4:"0deb";s:16:"res/ico_edit.gif";s:4:"4a05";s:21:"res/tmpl_default.html";s:4:"760c";s:23:"static/ts/constants.txt";s:4:"f338";s:19:"static/ts/setup.txt";s:4:"89ef";}',
	'suggests' => array(
	),
);

?>